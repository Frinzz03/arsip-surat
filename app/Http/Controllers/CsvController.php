<?php

namespace App\Http\Controllers;

use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CsvController extends Controller
{
    /**
     * Show the CSV export/import page.
     */
    public function index()
    {
        return view('csv.index');
    }

    /**
     * Export surat masuk data to CSV.
     */
    public function export(Request $request)
    {
        $query = SuratMasuk::query();

        // Apply filters
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_masuk', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_masuk', $request->tahun);
        }
        if ($request->filled('pengirim')) {
            $query->where('pengirim', 'LIKE', '%' . $request->pengirim . '%');
        }

        $suratList = $query->orderBy('tanggal_masuk')->get();

        $format = $request->input('format', 'csv');
        $ext = $format === 'xlsx' ? 'xlsx' : 'csv';

        $filename = 'LAPORAN DISPOSISI SURAT' . now()->format('Y-m-d_His') . '.' . $ext;

        $exportData = [];
        $exportData[] = [
            'NO',
            'DARI',
            'NOMOR',
            'TGL SURAT',
            'NO AGENDA',
            'SIFAT',
            'DITERIMA TGL',
            'PERIHAL',
        ];

        $no = 1;
        \Carbon\Carbon::setLocale('id');
        foreach ($suratList as $surat) {
            $exportData[] = [
                $no++,
                $surat->pengirim,
                $surat->nomor_surat,
                $surat->tanggal_surat ? $surat->tanggal_surat->translatedFormat('j F Y') : '',
                $surat->no_agenda,
                $surat->sifat ?: '-',
                $surat->tanggal_masuk ? $surat->tanggal_masuk->translatedFormat('j F Y') : '',
                $surat->perihal,
            ];
        }

        if ($format === 'xlsx') {
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($exportData);
            return response()->streamDownload(function() use ($xlsx) {
                echo (string) $xlsx;
            }, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($exportData) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            foreach ($exportData as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Import surat masuk from CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $file = $request->file('file_csv');
        $ext = strtolower($file->getClientOriginalExtension());

        $rows = [];
        if (in_array($ext, ['xlsx', 'xls'])) {
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($file->getPathname())) {
                $rows = $xlsx->rows();
                if (count($rows) > 0) array_shift($rows); // Skip header
            } else {
                return redirect()->back()->withErrors(['file_csv' => \Shuchkin\SimpleXLSX::parseError()]);
            }
        } else {
            $handle = fopen($file->getPathname(), 'r');
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            fgetcsv($handle); // Skip header
            while (($data = fgetcsv($handle)) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $row = 1;

        foreach ($rows as $data) {
            $row++;

            // Pad row to 8 columns if short (don't skip)
            while (count($data) < 8) {
                $data[] = '';
            }

            // Skip rows without meaningful data in columns 1-7
            // Column 0 is just the sequential NO from export, not real data
            $hasData = false;
            for ($i = 1; $i <= 7; $i++) {
                $val = trim((string) ($data[$i] ?? ''));
                if ($val !== '' && $val !== '-') {
                    $hasData = true;
                    break;
                }
            }
            if (!$hasData) {
                continue;
            }

            // no_agenda: use as-is from CSV, skip row if empty
            $noAgenda = trim((string) ($data[4] ?? ''));
            if ($noAgenda === '' || $noAgenda === '-') {
                // Skip rows without no_agenda — these are not real data
                $skipped++;
                continue;
            }

            // Handle duplicate no_agenda by appending suffix
            $originalAgenda = $noAgenda;
            $suffix = 1;
            while (SuratMasuk::where('no_agenda', $noAgenda)->exists()) {
                $noAgenda = $originalAgenda . '-' . $suffix;
                $suffix++;
            }

            // Parse dates — preserve as-is
            $tanggalSurat = $this->parseDate($data[3]);
            $tanggalMasuk = $this->parseDate($data[6]);

            // Sifat: keep as-is, null if not in valid enum values
            $sifatRaw = strtolower(trim((string) ($data[5] ?? '')));
            $sifat = in_array($sifatRaw, ['biasa', 'penting', 'segera', 'rahasia']) ? $sifatRaw : null;

            try {
                SuratMasuk::create([
                    'no_agenda' => $noAgenda,
                    'pengirim' => trim((string) ($data[1] ?? '')) ?: null,
                    'nomor_surat' => trim((string) ($data[2] ?? '')) ?: null,
                    'tanggal_surat' => $tanggalSurat,
                    'sifat' => $sifat,
                    'tanggal_masuk' => $tanggalMasuk,
                    'perihal' => trim((string) ($data[7] ?? '')) ?: null,
                    'penerima' => '-',
                    'uploaded_by' => auth()->id(),
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$row}: " . $e->getMessage();
                $skipped++;
            }
        }

        return redirect()->route('csv.index')
            ->with('success', "Berhasil mengimpor {$imported} data surat.")
            ->with('import_skipped', $skipped)
            ->with('import_errors', $errors);
    }

    /**
     * Parse date string from various formats (Indonesian, d/m/Y, Excel serial, etc.)
     * Returns Y-m-d string or null if unparseable.
     */
    private function parseDate($value): ?string
    {
        if (empty($value) || trim((string) $value) === '' || trim((string) $value) === '-') {
            return null;
        }

        $dateStr = trim((string) $value);

        // Handle Excel serial date numbers (e.g., 44927 = 2023-01-01)
        if (is_numeric($dateStr) && (int) $dateStr > 30000 && (int) $dateStr < 60000) {
            try {
                $unix = ((int) $dateStr - 25569) * 86400;
                return date('Y-m-d', $unix);
            } catch (\Exception $e) {
                // Fall through
            }
        }

        $dateStrLower = strtolower($dateStr);

        // Handle Indonesian month names (e.g., "1 Januari 2026")
        $bulanIndo = [
            'januari' => '01', 'februari' => '02', 'maret' => '03',
            'april' => '04', 'mei' => '05', 'juni' => '06',
            'juli' => '07', 'agustus' => '08', 'september' => '09',
            'oktober' => '10', 'november' => '11', 'desember' => '12',
        ];

        foreach ($bulanIndo as $indo => $num) {
            if (strpos($dateStrLower, $indo) !== false) {
                $dateStrLower = str_replace($indo, "-{$num}-", $dateStrLower);
                $dateStrLower = str_replace(' ', '', $dateStrLower);
                $dateStrLower = str_replace('--', '-', $dateStrLower);
                $dateStrLower = trim($dateStrLower, '-');
                break;
            }
        }

        try {
            // Handle d/m/Y format
            if (strpos($dateStrLower, '/') !== false) {
                $parts = explode('/', $dateStrLower);
                if (count($parts) === 3) {
                    return sprintf('%04d-%02d-%02d', (int) $parts[2], (int) $parts[1], (int) $parts[0]);
                }
            }

            // Handle d-m-Y or Y-m-d
            if (strpos($dateStrLower, '-') !== false) {
                $parts = explode('-', $dateStrLower);
                if (count($parts) === 3) {
                    // If first part is 4 digits, assume Y-m-d
                    if (strlen($parts[0]) === 4) {
                        return sprintf('%04d-%02d-%02d', (int) $parts[0], (int) $parts[1], (int) $parts[2]);
                    }
                    // Otherwise assume d-m-Y
                    return sprintf('%04d-%02d-%02d', (int) $parts[2], (int) $parts[1], (int) $parts[0]);
                }
            }

            // Fallback: let Carbon try to parse
            return \Carbon\Carbon::parse($dateStr)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}

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
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        $dateStr = trim((string) $value);

        if ($dateStr === '' || $dateStr === '-' || $dateStr === '0') {
            return null;
        }

        // 1. Handle Excel serial date numbers (e.g., 44927 = 2023-01-01)
        //    Excel dates range roughly from 1 (1900-01-01) to ~73000 (2099-12-31)
        if (is_numeric($dateStr)) {
            $num = (float) $dateStr;
            if ($num > 1 && $num < 73000) {
                try {
                    // Excel epoch: 1899-12-30 (accounting for Excel's leap year bug)
                    $unix = ($num - 25569) * 86400;
                    $result = date('Y-m-d', (int) $unix);
                    // Validate the result is a sane date
                    $year = (int) date('Y', (int) $unix);
                    if ($year >= 1990 && $year <= 2099) {
                        return $result;
                    }
                } catch (\Exception $e) {
                    // Fall through
                }
            }
        }

        // 2. Handle Indonesian month names with regex (e.g., "1 Januari 2026", "15 Mei 2025")
        $bulanIndo = [
            'januari' => 1, 'februari' => 2, 'maret' => 3,
            'april' => 4, 'mei' => 5, 'juni' => 6,
            'juli' => 7, 'agustus' => 8, 'september' => 9,
            'oktober' => 10, 'november' => 11, 'desember' => 12,
            // Abbreviated
            'jan' => 1, 'feb' => 2, 'mar' => 3,
            'apr' => 4, 'jun' => 6, 'jul' => 7,
            'agu' => 8, 'ags' => 8, 'aug' => 8,
            'sep' => 9, 'okt' => 10, 'oct' => 10,
            'nov' => 11, 'des' => 12, 'dec' => 12,
        ];

        $dateStrLower = strtolower($dateStr);
        foreach ($bulanIndo as $name => $monthNum) {
            if (preg_match('/(\d{1,2})[\s\-\/]+' . preg_quote($name, '/') . '[\s\-\/]+(\d{2,4})/i', $dateStrLower, $m)) {
                $y = (int) $m[2];
                if ($y < 100) $y += 2000;
                return sprintf('%04d-%02d-%02d', $y, $monthNum, (int) $m[1]);
            }
        }

        // 3. Handle English month names (e.g., "January 1, 2026", "1 March 2026")
        $bulanEng = [
            'january' => 1, 'february' => 2, 'march' => 3,
            'april' => 4, 'may' => 5, 'june' => 6,
            'july' => 7, 'august' => 8, 'september' => 9,
            'october' => 10, 'november' => 11, 'december' => 12,
        ];

        foreach ($bulanEng as $name => $monthNum) {
            // "1 March 2026" or "01-Mar-26" format
            if (preg_match('/(\d{1,2})[\s\-\/]+' . preg_quote($name, '/') . '[\s\-\/]+(\d{2,4})/i', $dateStrLower, $m)) {
                $y = (int) $m[2];
                if ($y < 100) $y += 2000;
                return sprintf('%04d-%02d-%02d', $y, $monthNum, (int) $m[1]);
            }
            // "March 1, 2026" format
            if (preg_match('/' . preg_quote($name, '/') . '[\s\-\/]+(\d{1,2}),?[\s\-\/]+(\d{2,4})/i', $dateStrLower, $m)) {
                $y = (int) $m[2];
                if ($y < 100) $y += 2000;
                return sprintf('%04d-%02d-%02d', $y, $monthNum, (int) $m[1]);
            }
        }

        try {
            // 4. Handle d/m/Y or m/d/Y format
            if (strpos($dateStr, '/') !== false) {
                $parts = explode('/', $dateStr);
                if (count($parts) === 3) {
                    $a = (int) $parts[0];
                    $b = (int) $parts[1];
                    $c = (int) $parts[2];

                    // If third part is 4 digits → d/m/Y
                    if ($c > 100) {
                        return sprintf('%04d-%02d-%02d', $c, $b, $a);
                    }
                    // If first part is 4 digits → Y/m/d
                    if ($a > 100) {
                        return sprintf('%04d-%02d-%02d', $a, $b, $c);
                    }
                }
            }

            // 5. Handle d-m-Y or Y-m-d format
            if (strpos($dateStr, '-') !== false) {
                $parts = explode('-', $dateStr);
                if (count($parts) === 3) {
                    if (strlen($parts[0]) === 4) {
                        return sprintf('%04d-%02d-%02d', (int) $parts[0], (int) $parts[1], (int) $parts[2]);
                    }
                    return sprintf('%04d-%02d-%02d', (int) $parts[2], (int) $parts[1], (int) $parts[0]);
                }
            }

            // 6. Handle d.m.Y format (common in some locales)
            if (strpos($dateStr, '.') !== false) {
                $parts = explode('.', $dateStr);
                if (count($parts) === 3) {
                    return sprintf('%04d-%02d-%02d', (int) $parts[2], (int) $parts[1], (int) $parts[0]);
                }
            }

            // 7. Fallback: let Carbon try to parse
            return \Carbon\Carbon::parse($dateStr)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}

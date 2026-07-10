<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'surat_masuk';

    protected $fillable = [
        'no_agenda',
        'nomor_surat',
        'tanggal_surat',
        'tanggal_masuk',
        'pengirim',
        'sifat',
        'perihal',
        'hari_acara',
        'tanggal_acara',
        'waktu_acara',
        'tempat_acara',
        'penerima',
        'file_path',
        'file_original_name',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_surat' => 'date',
            'tanggal_masuk' => 'date',
            'tanggal_acara' => 'date',
        ];
    }

    /**
     * Get the user who uploaded this surat.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Scope: filter by date range.
     */
    public function scopeFilterByDate($query, $from = null, $to = null)
    {
        if ($from) {
            $query->where('tanggal_masuk', '>=', $from);
        }
        if ($to) {
            $query->where('tanggal_masuk', '<=', $to);
        }
        return $query;
    }

    /**
     * Scope: filter by pengirim.
     */
    public function scopeFilterByPengirim($query, $pengirim)
    {
        if ($pengirim) {
            $query->where('pengirim', 'LIKE', '%' . $pengirim . '%');
        }
        return $query;
    }

    /**
     * Scope: FULLTEXT search on perihal (BM25-style).
     */
    public function scopeSearchPerihal($query, $keyword)
    {
        if ($keyword) {
            $query->whereRaw(
                'MATCH(perihal) AGAINST(? IN BOOLEAN MODE)',
                [$keyword]
            );
        }
        return $query;
    }

    /**
     * Get sifat badge color class.
     */
    public function getSifatBadgeClass(): string
    {
        return match ($this->sifat) {
            'penting' => 'badge-penting',
            'segera' => 'badge-segera',
            'rahasia' => 'badge-rahasia',
            default => 'badge-biasa',
        };
    }
}

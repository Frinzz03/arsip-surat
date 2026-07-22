<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\SuratMasuk;

class SearchService
{
    /**
     * Perform advanced search on surat masuk.
     * Supports exact match on no_agenda, LIKE on pengirim,
     * FULLTEXT on perihal, and standard date/sifat filters.
     */
    public function search(array $params, int $perPage = 15)
    {
        $query = SuratMasuk::query()->with('uploader');

        // Filter by no_agenda (exact or partial match)
        if (!empty($params['no_agenda'])) {
            $noAgenda = trim($params['no_agenda']);
            $query->where('no_agenda', 'LIKE', "%{$noAgenda}%");
        }

        // Filter by pengirim (LIKE)
        if (!empty($params['pengirim'])) {
            $query->where('pengirim', 'LIKE', '%' . $params['pengirim'] . '%');
        }

        // Filter by date range
        if (!empty($params['tanggal_dari'])) {
            $query->where('tanggal_masuk', '>=', $params['tanggal_dari']);
        }
        if (!empty($params['tanggal_sampai'])) {
            $query->where('tanggal_masuk', '<=', $params['tanggal_sampai']);
        }

        // Filter by sifat
        if (!empty($params['sifat'])) {
            $query->where('sifat', $params['sifat']);
        }

        // FULLTEXT search on perihal (BM25-style scoring)
        if (!empty($params['keyword'])) {
            $keyword = trim($params['keyword']);

            // Use BOOLEAN MODE for more flexible matching
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw(
                    'MATCH(perihal) AGAINST(? IN BOOLEAN MODE)',
                    [$keyword]
                )->orWhere('perihal', 'LIKE', "%{$keyword}%");
            });

            // Add relevance score for ordering
            $query->selectRaw(
                '*, MATCH(perihal) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance_score',
                [$keyword]
            );

            $query->orderByDesc('relevance_score');
        } else {
            $query->latest('tanggal_masuk');
        }

        return $query->paginate($perPage)->appends($params);
    }

    /**
     * Quick search — simple LIKE search across multiple fields.
     * Used for the dashboard/navbar quick search bar.
     */
    public function quickSearch(string $term, int $limit = 10)
    {
        $term = trim($term);

        return SuratMasuk::query()
            ->where(function ($q) use ($term) {
                $q->where('no_agenda', 'LIKE', "%{$term}%")
                  ->orWhere('nomor_surat', 'LIKE', "%{$term}%")
                  ->orWhere('pengirim', 'LIKE', "%{$term}%")
                  ->orWhere('perihal', 'LIKE', "%{$term}%");
            })
            ->latest('tanggal_masuk')
            ->limit($limit)
            ->get();
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Display the advanced search page.
     */
    public function index()
    {
        return view('search.index');
    }

    /**
     * Perform advanced search.
     */
    public function search(Request $request)
    {
        $params = $request->only([
            'pengirim', 'tanggal_dari',
            'tanggal_sampai', 'sifat', 'keyword'
        ]);

        $results = $this->searchService->search($params);

        return view('search.index', compact('results', 'params'));
    }

    /**
     * Quick search (AJAX) for dashboard/navbar.
     */
    public function quickSearch(Request $request)
    {
        $term = $request->input('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $results = $this->searchService->quickSearch($term);

        return response()->json($results->map(function ($surat) {
            return [
                'id' => $surat->id,
                'no_agenda' => $surat->no_agenda,
                'pengirim' => $surat->pengirim,
                'perihal' => \Illuminate\Support\Str::limit($surat->perihal, 80),
                'tanggal_masuk' => $surat->tanggal_masuk->format('d/m/Y'),
                'url' => route('surat-masuk.show', $surat->id),
            ];
        }));
    }
}

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <span class="pagination-btn pagination-disabled" aria-disabled="true">&lsaquo;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="pagination-btn" rel="prev">&lsaquo;</a>
        @endif

        @php
            $currentPage = $paginator->currentPage();
            $lastPage = $paginator->lastPage();

            // Build page numbers: first 3, ..., last 3
            $pages = collect();

            // First 3 pages
            for ($i = 1; $i <= min(3, $lastPage); $i++) {
                $pages->push($i);
            }

            // Last 3 pages
            for ($i = max(1, $lastPage - 2); $i <= $lastPage; $i++) {
                $pages->push($i);
            }

            // Pages around current page
            for ($i = max(1, $currentPage - 1); $i <= min($lastPage, $currentPage + 1); $i++) {
                $pages->push($i);
            }

            $pages = $pages->unique()->sort()->values();
        @endphp

        @foreach ($pages as $index => $page)
            {{-- Show ellipsis if there's a gap --}}
            @if ($index > 0 && $page - $pages[$index - 1] > 1)
                <span class="pagination-dots">&hellip;</span>
            @endif

            @if ($page == $currentPage)
                <span class="pagination-btn pagination-active">{{ $page }}</span>
            @else
                <a href="{{ $paginator->url($page) }}" class="pagination-btn">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="pagination-btn" rel="next">&rsaquo;</a>
        @else
            <span class="pagination-btn pagination-disabled" aria-disabled="true">&rsaquo;</span>
        @endif
    </nav>
@endif

@if ($paginator->hasPages())
    <nav class="jy-pagination" aria-label="Pagination">
        <button type="button" class="jy-page-btn js-page" data-page="{{ $paginator->currentPage() - 1 }}" {{ $paginator->onFirstPage() ? 'disabled' : '' }}>
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        @php
            $current = $paginator->currentPage();
            $last = $paginator->lastPage();
            $window = 2;
            $pages = [];
            for ($p = 1; $p <= $last; $p++) {
                if ($p === 1 || $p === $last || abs($p - $current) <= $window) {
                    $pages[] = $p;
                }
            }
            $pages = array_values(array_unique($pages));
        @endphp

        @foreach ($pages as $i => $p)
            @if ($i > 0 && $p - $pages[$i - 1] > 1)
                <span style="padding:0 4px;color:var(--color-text-muted);">…</span>
            @endif
            <button type="button" class="jy-page-btn js-page {{ $p === $current ? 'active' : '' }}" data-page="{{ $p }}">{{ $p }}</button>
        @endforeach

        <button type="button" class="jy-page-btn js-page" data-page="{{ $paginator->currentPage() + 1 }}" {{ ! $paginator->hasMorePages() ? 'disabled' : '' }}>
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </nav>
@endif

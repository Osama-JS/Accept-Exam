@if ($paginator->hasPages())
    <nav class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="disabled">
                <i class="bi bi-chevron-right"></i>
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <i class="bi bi-chevron-right"></i>
            </a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <span class="dots">{{ $element }}</span>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="active"><span>{{ $page }}</span></span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">
                <i class="bi bi-chevron-left"></i>
            </a>
        @else
            <span class="disabled">
                <i class="bi bi-chevron-left"></i>
            </span>
        @endif
    </nav>
@endif

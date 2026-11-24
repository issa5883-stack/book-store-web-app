@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="{{ __('Page navigation') }}">
        @if ($paginator->onFirstPage())
            <span class="disabled"><x-icon name="arrow-left" class="flip-rtl" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-icon name="arrow-left" class="flip-rtl" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="disabled">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"><x-icon name="arrow-right" class="flip-rtl" /></a>
        @else
            <span class="disabled"><x-icon name="arrow-right" class="flip-rtl" /></span>
        @endif
    </nav>
@endif

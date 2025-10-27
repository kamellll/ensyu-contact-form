@if ($paginator->hasPages())
    <div class="page__content">
        <nav role="navigation" aria-label="Pagination Navigation" class="pager">
            {{-- 左矢印 --}}
            @if ($paginator->onFirstPage())
                <span class="pager__arrow-left pager__arrow--disabled" aria-disabled="true" aria-label="前のページへ">
                    {{-- 左矢印アイコン --}}
                    <svg class="pager__icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pager__arrow-left" rel="prev" aria-label="前のページへ">
                    <svg class="pager__icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            @endif

            {{-- ページ番号 --}}
            <span class="pager__numbers">
                @foreach ($elements as $element)
                    {{-- "..." のところ --}}
                    @if (is_string($element))
                        <span class="pager__page pager__page--ellipsis" aria-disabled="true">{{ $element }}</span>
                    @endif

                    {{-- 配列: [ページ => URL] --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="pager__page pager__page--current" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="pager__page" aria-label="ページ {{ $page }} へ">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </span>

            {{-- 右矢印 --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pager__arrow-right" rel="next" aria-label="次のページへ">
                    <svg class="pager__icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            @else
                <span class="pager__arrow-right pager__arrow--disabled" aria-disabled="true" aria-label="次のページへ">
                    <svg class="pager__icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                            clip-rule="evenodd"></path>
                    </svg>
                </span>
            @endif
        </nav>
    </div>
@endif
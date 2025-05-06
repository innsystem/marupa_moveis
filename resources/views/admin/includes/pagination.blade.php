@if ($paginator->hasPages())
    <nav aria-label="Navegação da paginação">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @else
                <li class="page-item">
                    <button class="page-link pagination-link" data-page="{{ $paginator->currentPage() - 1 }}">Anterior</button>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <button class="page-link pagination-link" data-page="{{ $page }}">{{ $page }}</button>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button class="page-link pagination-link" data-page="{{ $paginator->currentPage() + 1 }}">Próximo</button>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Próximo</span>
                </li>
            @endif
        </ul>
    </nav>
@endif 
{{-- Componente de Paginação Reutilizável --}}
<div class="col-12 mt-2">
    <div class="card">
        <div class="card-body p-2">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-0">
                        Exibindo {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} de {{ $paginator->total() }} {{ $resourceName ?? 'itens' }}
                    </p>
                </div>
                <div>
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Botão anterior --}}
                            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="javascript:void(0);" onclick="loadPage('{{ $paginator->previousPageUrl() }}')" aria-label="Anterior">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            {{-- Números das páginas com encurtamento --}}
                            @php
                            $current = $paginator->currentPage();
                            $last = $paginator->lastPage();
                            $start = max(1, $current - 2);
                            $end = min($last, $current + 2);
                            @endphp
                            @if ($start > 1)
                            <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadPage('{{ $paginator->url(1) }}')">1</a></li>
                            @if ($start > 2)
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                            @endif
                            @endif
                            @for ($i = $start; $i <= $end; $i++)
                                <li class="page-item {{ $current == $i ? 'active' : '' }}">
                                <a class="page-link" href="javascript:void(0);" onclick="loadPage('{{ $paginator->url($i) }}')">{{ $i }}</a>
                                </li>
                                @endfor
                                @if ($end < $last)
                                    @if ($end < $last - 1)
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                    <li class="page-item"><a class="page-link" href="javascript:void(0);" onclick="loadPage('{{ $paginator->url($last) }}')">{{ $last }}</a></li>
                                    @endif

                                    {{-- Botão próximo --}}
                                    <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                                        <a class="page-link" href="javascript:void(0);" onclick="loadPage('{{ $paginator->nextPageUrl() }}')" aria-label="Próximo">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
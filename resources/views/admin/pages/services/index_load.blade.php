@if(isset($results) && count($results) > 0)
@foreach($results as $service)
<div id="row_service_{{$service->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="flex-grow-1 d-flex align-items-center">
            <div>
                <h5 class="h6 mb-1 fw-bold">{{$service->title}}</h5>
                
                {{-- Exibição dos valores --}}
                <div class="small text-muted mt-1">
                    @php
                        $valores = [];
                        if(!$service->is_recurring && $service->single_payment_price && $service->single_payment_price > 0) {
                            $valores[] = '<span title="Pagamento Único"><i class="fa fa-money-bill"></i> R$ '.number_format($service->single_payment_price,2,',','.').'</span>';
                        }
                        if($service->is_recurring) {
                            if($service->monthly_price && $service->monthly_price > 0) {
                                $valores[] = '<span title="Mensal"><i class="fa fa-redo"></i> Mensal: R$ '.number_format($service->monthly_price,2,',','.').'</span>';
                            }
                            if($service->quarterly_price && $service->quarterly_price > 0) {
                                $valores[] = '<span title="Trimestral"><i class="fa fa-redo"></i> Trimestral: R$ '.number_format($service->quarterly_price,2,',','.').'</span>';
                            }
                            if($service->semiannual_price && $service->semiannual_price > 0) {
                                $valores[] = '<span title="Semestral"><i class="fa fa-redo"></i> Semestral: R$ '.number_format($service->semiannual_price,2,',','.').'</span>';
                            }
                            if($service->annual_price && $service->annual_price > 0) {
                                $valores[] = '<span title="Anual"><i class="fa fa-redo"></i> Anual: R$ '.number_format($service->annual_price,2,',','.').'</span>';
                            }
                            if($service->biennial_price && $service->biennial_price > 0) {
                                $valores[] = '<span title="Bienal"><i class="fa fa-redo"></i> Bienal: R$ '.number_format($service->biennial_price,2,',','.').'</span>';
                            }
                        }
                    @endphp
                    @if(count($valores) > 0)
                        <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                            {!! implode(' <span class="mx-1">|</span> ', $valores) !!}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-info button-services-edit" data-service-id="{{$service->id}}">Editar</button>
            <button type="button" class="btn btn-sm btn-danger button-services-delete" data-service-id="{{$service->id}}" data-service-name="{{$service->title}}">Apagar</button>
        </div>
    </div>
</div><!-- col-12 -->
@endforeach

<div class="d-flex justify-content-center mt-4 pagination-container">
    <nav aria-label="Navegação da paginação">
        <ul class="pagination">
            @if($results->currentPage() > 1)
                <li class="page-item">
                    <button class="page-link pagination-link" data-page="{{ $results->currentPage() - 1 }}">Anterior</button>
                </li>
            @endif

            @for($i = 1; $i <= $results->lastPage(); $i++)
                <li class="page-item {{ $results->currentPage() == $i ? 'active' : '' }}">
                    <button class="page-link pagination-link" data-page="{{ $i }}">{{ $i }}</button>
                </li>
            @endfor

            @if($results->currentPage() < $results->lastPage())
                <li class="page-item">
                    <button class="page-link pagination-link" data-page="{{ $results->currentPage() + 1 }}">Próximo</button>
                </li>
            @endif
        </ul>
    </nav>
</div>

@else
<div class="alert alert-warning mb-0">Nenhum resultado foi localizado...</div>
@endif
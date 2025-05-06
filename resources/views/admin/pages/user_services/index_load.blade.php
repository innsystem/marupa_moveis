@if(isset($results) && count($results) > 0)
@foreach($results as $user_service)
<div id="row_user_service_{{$user_service->id}}" class="col-12 pb-2 mb-4 border-bottom rounded">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <div class="flex-grow-1 d-flex align-items-center">
            <div>
                @if(!isset($user) && isset($user_service->user))
                <div class="mb-1 text-primary fw-bold small">
                    <i class="fa fa-user"></i> {{ $user_service->user->name }}
                </div>
                @endif
                <h5 class="h6 mb-1 fw-bold">{{$user_service->service->title}}</h5>
                <div class="d-flex flex-wrap gap-3 small text-muted">
                    <div class="badge {{ $user_service->statusRelation->color }}">
                        <i class="{{ $user_service->statusRelation->icon ?? 'fa fa-circle' }}"></i>
                        {{ $user_service->statusRelation->name }}
                    </div>
                    <div>
                        <i class="fa fa-clock"></i> {{ $user_service->period_label }}
                    </div>
                    <div>
                        <i class="fa fa-money-bill"></i> {{ $user_service->formatted_price }}
                    </div>
                    <div>
                        <i class="fa fa-calendar"></i> {{ $user_service->start_date->format('d/m/Y') }}
                    </div>
                    <div>
                        <i class="far fa-calendar-alt"></i>
                        @if($user_service->end_date)
                        Vence em {{ $user_service->end_date->format('d/m/Y') }}
                        @endif
                    </div>
                </div>

                @php
                $metadata = is_string($user_service->metadata) ? json_decode($user_service->metadata, true) : $user_service->metadata;
                @endphp

                @if(!empty($metadata))
                <div class="mt-2">
                    <strong class="small d-block text-muted mb-1">Informações adicionais:</strong>
                    <div class="d-flex flex-wrap gap-3 small">
                        @foreach($metadata as $title => $value)
                        <div><strong>{{ $title }}:</strong> {{ $value }}</div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-warning button-user-services-generate-invoice"
                data-user-id="{{ isset($user) ? $user->id : ($user_service->user->id ?? '') }}"
                data-service-id="{{$user_service->id}}"
                data-service-name="{{$user_service->service->title}}">
                Gerar Fatura
            </button>
            <button type="button" class="btn btn-sm btn-info button-user-services-edit" data-user-id="{{ isset($user) ? $user->id : ($user_service->user->id ?? '') }}" data-service-id="{{$user_service->id}}">Editar</button>
            <button type="button" class="btn btn-sm btn-danger button-user-services-delete" data-user-id="{{ isset($user) ? $user->id : ($user_service->user->id ?? '') }}" data-service-id="{{$user_service->id}}" data-service-name="{{$user_service->service->title}}">Apagar</button>
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
<div class="alert alert-warning mb-0">Nenhum serviço encontrado...</div>
@endif
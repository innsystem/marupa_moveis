@extends('admin.base')

@section('title', $customer->name)

@section('content')
<div class="container">
    <div class="row py-2">
        <div class="col-12 col-md-4 col-lg-4">
            <div class="card custom-card overflow-hidden">
                <div class="card-body p-3">
                    <div class="d-sm-flex align-items-top border-bottom pb-2 mb-2">
                        <div>
                            <img src="{{ asset('/galerias/avatares/innsystem.png') }}" alt="{{$customer->name}}" class="avatar-sm rounded-circle me-1">
                        </div>
                        <div class="flex-fill main-profile-info">
                            <div class="d-flex align-items-center justify-content-between">
                                <h4 class="h4 fw-semibold mb-1 text-fixed-white">{{$customer->name}}</h4>
                            </div>
                            <p class="mb-1 text-muted text-fixed-white">Cliente do sistema</p>
                        </div>
                    </div>
                    <div class="border-bottom pb-2 mb-2">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Informações de Contato:</p>
                        <div class="text-muted">
                            <p class="mb-2"> <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted"> <i class="ri-mail-line align-middle fs-14"></i> </span> {{ $customer->email }} </p>
                            <p class="mb-2"> <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted"> <i class="ri-phone-line align-middle fs-14"></i> </span> {{ $customer->ddi }} {{ $customer->phone }} </p>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm mt-2 button-customers-edit" data-customer-id="{{ $customer->id }}"><i class="fa fa-edit"></i> Editar informações</button>
                    </div>
                    <div class="border-bottom pb-2 mb-2">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Documentos:</p>
                        <div class="text-muted">
                            <p class="mb-2"> <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted"> <i class="fa fa-id-card align-middle fs-14"></i> </span> {{ $customer->document ?? '-' }} </p>
                        </div>
                    </div>
                    <div class="border-bottom pb-2 mb-2">
                        <p class="fs-15 mb-2 me-4 fw-semibold">Preferências de Pagamento:</p>
                        <div class="text-muted">
                            <p class="mb-2">
                                <span class="avatar avatar-sm avatar-rounded me-2 bg-light text-muted">
                                    <i class="fa fa-credit-card align-middle fs-14"></i>
                                </span>
                                @if(isset($preferences) && $preferences->payment_default == 'pix')
                                PIX
                                @elseif(isset($preferences) && $preferences->payment_default == 'boleto')
                                Boleto
                                @elseif(isset($preferences) && $preferences->payment_default == 'credit_card')
                                Cartão de Crédito
                                @else
                                Não definido
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-8 col-lg-8">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body p-2">
                            <div class="p-1 border-bottom pb-2 mb-2 d-flex align-items-center justify-content-between">
                                <ul class="nav nav-tabs mb-0 tab-style-6 justify-content-start" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services-tab-pane" type="button" role="tab" aria-controls="services-tab-pane" aria-selected="false">
                                            <i class="fa fa-cogs me-1 align-middle d-inline-block"></i>Serviços
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="invoices-tab" data-bs-toggle="tab" data-bs-target="#invoices-tab-pane" type="button" role="tab" aria-controls="invoices-tab-pane" aria-selected="true">
                                            <i class="ri-file-line me-1 align-middle d-inline-block"></i>Faturas
                                        </button>
                                    </li>
                                </ul>
                                <a href="{{ route('admin.users.services', $customer->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-cogs"></i> Gerenciar Serviços
                                </a>
                            </div>
                            <div class="p-0">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane show active fade p-0 border-0" id="services-tab-pane" role="tabpanel" aria-labelledby="services-tab" tabindex="0">
                                        <div class="p-3">
                                            <h5 class="h5 mb-3">Serviços Contratados</h5>
                                            <div id="services-list">
                                                @if(count($customer->services) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Serviço</th>
                                                                <th>Período</th>
                                                                <th>Valor</th>
                                                                <th>Status</th>
                                                                <th>Vigência</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($customer->services as $service)
                                                            <tr>
                                                                <td>{{ $service->service->title }}</td>
                                                                <td>{{ $service->period_label }}</td>
                                                                <td>{{ $service->formatted_price }}</td>
                                                                <td>
                                                                    <span class="badge {{ $service->statusRelation->color }}">
                                                                        <i class="{{ $service->statusRelation->icon ?? 'fa fa-circle' }}"></i>
                                                                        {{ $service->statusRelation->name }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ $service->start_date->format('d/m/Y') }}
                                                                    @if($service->end_date)
                                                                    até {{ $service->end_date->format('d/m/Y') }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @else
                                                <div class="alert alert-warning">
                                                    Nenhum serviço contratado.
                                                    <a href="{{ route('admin.users.services', $customer->id) }}" class="alert-link">Adicionar serviço</a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade p-0 border-0" id="invoices-tab-pane" role="tabpanel" aria-labelledby="invoices-tab" tabindex="0">
                                        @include('components.invoices_table', ['invoices' => $customerInvoices, 'hideClientColumn' => true])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageMODAL')
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalUsers" aria-labelledby="modalUsersLabel">
    <div class="offcanvas-header">
        <h5 id="modalUsersLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-customers">
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->

<div class="offcanvas offcanvas-end" tabindex="-1" id="modalAddresses" aria-labelledby="modalAddressesLabel">
    <div class="offcanvas-header">
        <h5 id="modalAddressesLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body" id="modal-addresses-content">
        <!-- Conteúdo AJAX aqui -->
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="modalInvoices" aria-labelledby="modalInvoicesLabel">
    <div class="offcanvas-header">
        <h5 id="modalInvoicesLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-invoices">
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->
@endsection

@section('pageCSS')
<!-- Flatpickr Timepicker css -->
<link href="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset('/plugins/intl-tel-input/css/intlTelInput.min.css') }}">

@endsection

@section('pageJS')
<!-- Flatpickr Timepicker Plugin js -->
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/l10n/pt.js') }}"></script>
<script src="{{ asset('/plugins/intl-tel-input/js/intlTelInput.min.js') }}"></script>

@include('admin.pages.customers.scripts')
@endsection
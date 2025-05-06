@extends('admin.base')

@section('title', 'Faturas')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-invoices-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-invoices-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
        </div>
    </div>
    <div id="content_filters" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name">Nome do Cliente:</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Nome do cliente">
                            </div>
                            <div class="col-md-2">
                                <label for="invoice_id">N° da Fatura:</label>
                                <input type="tel" id="invoice_id" name="invoice_id" class="form-control mask-number" placeholder="N° da Fatura">
                            </div>
                            <div class="col-md-2">
                                <label for="invoice_id">Status da Fatura:</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Todas Faturas</option>
                                    <option value="24">Pagas</option>
                                    <option value="23">Pendentes</option>
                                    <option value="26">Canceladas</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="per_page">Resultados:</label>
                                <select name="per_page" id="per_page" class="form-select">
                                    <option value="10">10 por página</option>
                                    <option value="20">20 por página</option>
                                    <option value="50">50 por página</option>
                                    <option value="100">100 por página</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_range">Período de Vencimento:</label>
                                <input type="text" id="date_range" name="date_range" class="form-control rangecalendar-period" placeholder="Selecione o intervalo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-invoices-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="content-load-page"></div><!-- row -->
</div>
@endsection

@section('pageMODAL')
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
@endsection

@section('pageJS')
<!-- Flatpickr Timepicker Plugin js -->
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/l10n/pt.js') }}"></script>

<script>
    $(document).ready(function() {
        $("#date_range").flatpickr({
            "mode": "range",
            "dateFormat": "d/m/Y",
            "locale": "pt", // Configuração para português
            "firstDayOfWeek": 1, // Inicia a semana na segunda-feira
        });

        loadContentPage();
    });

    function loadContentPage(page = 1) {
        $("#content-load-page").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando...</p></div>');
        var url = `{{ url('/admin/invoices/load') }}`;
        var filters = $('#filter-form').serialize();
        if (page > 1) {
            filters += '&page=' + page;
        }

        $.get(url + '?' + filters, function(data) {
            $("#content-load-page").html(data);
            initMasks();
        });
    }

    function initMasks() {
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        $("#due_at").flatpickr({
            // "mode": "range",
            "dateFormat": "d/m/Y",
            "locale": "pt", // Configuração para português
            "firstDayOfWeek": 1, // Inicia a semana na segunda-feira
        });

        $(".mask-money").mask('00000.00', {
            reverse: true,
            placeholder: "0.00"
        });

        var optionsDocument = {
            onKeyPress: function(document, ev, el, op) {
                var masks = ['000.000.000-000', '00.000.000/0000-00'];
                $('.mask-document').mask((document.length > 14) ? masks[1] : masks[0], op);
            }
        }

        if ($('.mask-document').length) {
            $('.mask-document').val().length > 14 ? $('.mask-document').mask('00.000.000/0000-00', optionsDocument) : $('.mask-document').mask('000.000.000-00#', optionsDocument);
        }
        var cellMaskBehavior = function(val) {
                return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
            },
            cellOptions = {
                onKeyPress: function(val, e, field, options) {
                    field.mask(cellMaskBehavior.apply({}, arguments), options);
                }
            };

        $('.mask-phone').mask(cellMaskBehavior, cellOptions);
    }

    $(document).on("click", ".button-invoices-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-invoices-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });

    // Função global para ser usada nos links de paginação
    function loadPage(url) {
        if (!url) return;
        $("#content-load-page").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando...</p></div>');
        $.get(url, function(data) {
            $("#content-load-page").html(data);
            initMasks();
        });
    }
</script>

@include('admin.pages.invoices.scripts')
@endsection
@extends('admin.base')

@section('title', 'Fluxo de Caixa')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-transactions-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-transactions-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
        </div>
    </div>
    <div id="content_filters" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="description">Descrição:</label>
                                <input type="text" id="description" name="description" class="form-control" placeholder="Digite a descrição">
                            </div>
                            <div class="col-md-3">
                                <label for="invoice_id">N° da Fatura:</label>
                                <input type="text" id="invoice_id" name="invoice_id" class="form-control" placeholder="Digite o número da fatura">
                            </div>
                            <div class="col-md-2">
                                <label for="per_page">Resultados por página:</label>
                                <select name="per_page" id="per_page" class="form-select">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_range">Período de Data:</label>
                                <input type="text" id="date_range" name="date_range" class="form-control rangecalendar-period" placeholder="Selecione o intervalo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-transactions-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="content-load-page"></div>
</div>
@endsection

@section('pageMODAL')
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalTransactions" aria-labelledby="modalTransactionsLabel">
    <div class="offcanvas-header">
        <h5 id="modalTransactionsLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-transactions">
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
        var url = `{{ url('/admin/transactions/load') }}`;
        var filters = $('#filter-form').serialize();
        if (page > 1) { filters += '&page=' + page; }

        $.get(url + '?' + filters, function(data) {
            $("#content-load-page").html(data);
            initMasks();
        });
    }

    // Função global para ser usada nos links de paginação
    function loadPage(url) {
        if (!url) return;
        $("#content-load-page").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando...</p></div>');
        $.get(url, function(data) {
            $("#content-load-page").html(data);
            initMasks();
        });
    }

    function initMasks() {
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        $("#date").flatpickr({
            // "mode": "range",
            "enableTime": !0,
            "dateFormat": "d/m/Y H:i",
            "locale": "pt", // Configuração para português
            "firstDayOfWeek": 1, // Inicia a semana na segunda-feira
        });

        $(".mask-money").mask('00000.00', {
            reverse: true,
            placeholder: "0.00"
        });
    }

    $(document).on("click", ".button-transactions-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-transactions-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-transactions-create", function(e) {
        e.preventDefault();

        $("#modal-content-transactions").html('');
        $("#modalTransactionsLabel").text('Nova Transação');
        var offcanvas = new bootstrap.Offcanvas($('#modalTransactions'));
        offcanvas.show();

        var url = `{{ url('/admin/transactions/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-transactions").html(data);
                $(".button-transactions-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-transactions-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let transaction_id = button.data('transaction-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/transactions/store/') }}`;
        } else {
            if (transaction_id) {
                var url = `{{ url('/admin/transactions/${transaction_id}/update') }}`;
            }
        }

        var form = $('#form-request-transactions')[0];
        var formData = new FormData(form);

        $.ajax({
            url: url,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            method: 'POST',
            beforeSend: function() {
                //disable the submit button
                button.attr("disabled", true);
                button.append('<i class="fa fa-spinner fa-spin ml-3"></i>');
            },
            complete: function() {
                button.prop("disabled", false);
                button.find('.fa-spinner').addClass('d-none');
            },
            success: function(data) {
                Swal.fire({
                    text: data,
                    icon: 'success',
                    showClass: {
                        popup: 'animate_animated animate_backInUp'
                    },
                    onClose: () => {
                        $("#modal-content-transactions").html('');
                        var offcanvas = bootstrap.Offcanvas.getInstance($('#modalTransactions'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                        loadContentPage();
                    }
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        text: 'Validação: ' + xhr.responseJSON,
                        icon: 'warning',
                        showClass: {
                            popup: 'animate_animated animate_wobble'
                        }
                    });
                } else {
                    Swal.fire({
                        text: 'Erro Interno: ' + xhr.responseJSON,
                        icon: 'error',
                        showClass: {
                            popup: 'animate_animated animate_wobble'
                        }
                    });
                }
            }
        });
    });
</script>
@endsection
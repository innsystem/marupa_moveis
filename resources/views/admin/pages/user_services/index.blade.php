@extends('admin.base')

@section('title', 'Serviços do Cliente: ' . $user->name)

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <a href="{{ route('admin.users.services.create', $user->id) }}" class="btn btn-sm btn-success button-user-services-create" data-user-id="{{ $user->id }}"><i class="fa fa-plus"></i> Adicionar Serviço</a>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-user-services-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
            <a href="{{ route('admin.customers.show', $user->id) }}" class="btn btn-sm btn-secondary ms-2"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
    <div id="content_filters" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="service_name">Nome do Serviço:</label>
                                <input type="text" id="service_name" name="service_name" class="form-control" placeholder="Digite o nome do serviço">
                            </div>
                            <div class="col-md-4">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="3">Ativo</option>
                                    <option value="4">Inativo</option>
                                    <option value="5">Inadimplente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_range">Período:</label>
                                <input type="text" id="date_range" name="date_range" class="form-control rangecalendar-period" placeholder="Selecione o intervalo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-user-services-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="content-load-page" class="row">
                    </div><!-- row -->
                </div> <!-- end card body -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageMODAL')
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalUserServices" aria-labelledby="modalUserServicesLabel">
    <div class="offcanvas-header">
        <h5 id="modalUserServicesLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-user-services">
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

        // Carrega o conteúdo da página
        loadContentPage();
    });

    function loadContentPage(page = 1) {
        $("#content-load-page").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando...</p></div>');
        var url = `{{ route('admin.users.services.load', $user->id) }}`;
        var filters = $('#filter-form').serialize();

        if (page > 1) {
            filters += '&page=' + page;
        }

        $.get(url + '?' + filters, function(data) {
            $("#content-load-page").html(data);

            // Remover manipuladores de eventos antigos para evitar duplicação
            $(document).off("click", ".pagination-link");

            // Adicionar novo manipulador de eventos para botões de paginação
            $(document).on("click", ".pagination-link", function(e) {
                e.preventDefault();
                var pageNum = $(this).data('page');
                loadContentPage(pageNum);
            });
        });
    }

    function initMasks() {
        $("#start_date").flatpickr({
            // "mode": "range",
            "dateFormat": "d/m/Y",
            "locale": "pt", // Configuração para português
            "firstDayOfWeek": 1, // Inicia a semana na segunda-feira
        });

        $("#end_date").flatpickr({
            // "mode": "range",
            "dateFormat": "d/m/Y",
            "locale": "pt", // Configuração para português
            "firstDayOfWeek": 1, // Inicia a semana na segunda-feira
        });

        $(".mask-money").mask('00000.00', {
            reverse: true,
            placeholder: "0.00"
        });

        // Inicializa as opções de preço
        updatePriceOptions();
    }

    $(document).on("click", ".button-user-services-toggle-filters", function(e) {
        e.preventDefault();
        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-user-services-filters", function(e) {
        e.preventDefault();
        loadContentPage();
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-user-services-create", function(e) {
        e.preventDefault();

        let user_id = $(this).data('user-id');

        $("#modal-content-user-services").html('');
        $("#modalUserServicesLabel").text('Novo Serviço');
        var offcanvas = new bootstrap.Offcanvas($('#modalUserServices'));
        offcanvas.show();

        var url = `{{ route('admin.users.services.create', $user->id) }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-user-services").html(data);
                $(".button-user-services-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Edit
    $(document).on("click", ".button-user-services-edit", function(e) {
        e.preventDefault();

        let user_id = $(this).data('user-id');
        let service_id = $(this).data('service-id');

        $("#modal-content-user-services").html('');
        $("#modalUserServicesLabel").text('Editar Serviço');
        var offcanvas = new bootstrap.Offcanvas($('#modalUserServices'));
        offcanvas.show();

        var url = `{{ url('/admin/users/${user_id}/services/${service_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-user-services").html(data);
                $(".button-user-services-save").attr('data-type', 'edit').attr('data-service-id', service_id);
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-user-services-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let user_id = `{{$user->id}}`;
        let service_id = button.data('service-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ route('admin.users.services.store', $user->id) }}`;
        } else {
            if (service_id) {
                var url = `{{ url('/admin/users/${user_id}/services/${service_id}/update') }}`;
            }
        }

        var form = $('#form-request-user-services')[0];
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
                        $("#modal-content-user-services").html('');
                        var offcanvas = bootstrap.Offcanvas.getInstance($('#modalUserServices'));
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

    // Delete
    $(document).on('click', '.button-user-services-delete', function(e) {
        e.preventDefault();
        let user_id = $(this).data('user-id');
        let service_id = $(this).data('service-id');
        let service_name = $(this).data('service-name');

        Swal.fire({
            title: 'Deseja apagar este serviço?',
            text: service_name,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#333',
            confirmButtonText: 'Sim, apagar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: `{{ url('/admin/users/${user_id}/services/${service_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_user_service_' + service_id).remove();
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            $('#row_user_service_' + service_id).remove();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            Swal.fire({
                                text: xhr.responseJSON,
                                icon: 'warning',
                                showClass: {
                                    popup: 'animate__animated animate__headShake'
                                }
                            });
                        } else {
                            Swal.fire({
                                text: xhr.responseJSON,
                                icon: 'error',
                                showClass: {
                                    popup: 'animate__animated animate__headShake'
                                }
                            });
                        }
                    }
                });
            }
        })
    });

    // Adiciona evento para adicionar nova linha de metadata
    $(document).on('click', '#add_metadata', function() {
        var newRow = `
                <div class="row mb-2 metadata-row">
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_title[]" placeholder="Título">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_value[]" placeholder="Valor">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger btn-sm remove-metadata"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            `;
        $('#metadata_container').append(newRow);
    });

    // Remove linha de metadata
    $(document).on('click', '.remove-metadata', function() {
        $(this).closest('.metadata-row').remove();
    });

    // Adiciona os campos para conta pessoal
    $(document).on('click', '#add_conta_pessoal', function() {
        $('#metadata_container').html('');
        var camposPessoais = [
            {titulo: 'tipo', valor: 'Exemplo: Energia'},
            {titulo: 'fornecedor', valor: 'Exemplo: NET CLARO'},
            {titulo: 'is_expense', valor: 'true'},
        ];
        camposPessoais.forEach(function(campo) {
            var newRow = `
                <div class="row mb-2 metadata-row">
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_title[]" placeholder="" value="${campo.titulo}">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_value[]" placeholder="${campo.valor}" value="">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger btn-sm remove-metadata"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            `;
            $('#metadata_container').append(newRow);
        });
        // Exibe mensagem de ajuda
        Swal.fire({
            title: 'Campos para Conta Pessoal adicionados!',
            text: 'Preencha o "tipo" (ex: Energia, Internet) e o "fornecedor" (ex: CPFL, Vivo) para melhor organização.',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });
</script>

<script>
    function updatePriceOptions() {
        var selectedService = $('#service_id option:selected');
        var period = $('#period');

        // Limpa opções anteriores
        period.find('option').css('display', 'block');

        if (selectedService.val()) {
            var isRecurring = selectedService.data('is-recurring') == 1;

            // Se não for recorrente, só permite pagamento único
            if (!isRecurring) {
                period.find('option:not([value="once"])').css('display', 'none');
                period.val('once');
            }

            // Desabilita opções de período que não têm preço definido
            if (selectedService.data('monthly-price') <= 0)
                period.find('option[value="monthly"]').css('diplay', 'none');

            if (selectedService.data('quarterly-price') <= 0)
                period.find('option[value="quarterly"]').css('diplay', 'none');

            if (selectedService.data('semiannual-price') <= 0)
                period.find('option[value="semiannual"]').css('diplay', 'none');

            if (selectedService.data('annual-price') <= 0)
                period.find('option[value="annual"]').css('diplay', 'none');

            if (selectedService.data('biennial-price') <= 0)
                period.find('option[value="biennial"]').css('diplay', 'none');

            if (selectedService.data('single-price') <= 0)
                period.find('option[value="once"]').css('diplay', 'none');
        }

        updatePrice();
    }

    function updatePrice() {
        var selectedService = $('#service_id option:selected');
        var selectedPeriod = $('#period').val();
        var priceInput = $('#price');

        // Se não houver serviço selecionado, retorna
        if (!selectedService.val()) return;

        // Obtém o preço correspondente ao período
        var price = 0;
        switch (selectedPeriod) {
            case 'once':
                price = selectedService.data('single-price');
                break;
            case 'monthly':
                price = selectedService.data('monthly-price');
                break;
            case 'quarterly':
                price = selectedService.data('quarterly-price');
                break;
            case 'semiannual':
                price = selectedService.data('semiannual-price');
                break;
            case 'annual':
                price = selectedService.data('annual-price');
                break;
            case 'biennial':
                price = selectedService.data('biennial-price');
                break;
        }

        // Preenche o campo de preço se não estiver já preenchido ou se for zero
        var currentPrice = priceInput.val().replace(/[^\d]/g, '');
        if (currentPrice === '' || currentPrice === '0' || currentPrice === '000') {
            priceInput.val(price.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }));
        }
    }
</script>

<script>
    // Gerar Fatura para serviço específico
    $(document).on('click', '.button-user-services-generate-invoice', function(e) {
        e.preventDefault();
        let button = $(this);
        let userId = button.data('user-id');
        let serviceId = button.data('service-id');
        let serviceName = button.data('service-name');

        Swal.fire({
            title: 'Gerar nova fatura?',
            text: `Deseja gerar uma nova fatura para o serviço: ${serviceName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, gerar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Pergunta se deseja notificar o cliente
                Swal.fire({
                    title: 'Notificar o cliente?',
                    text: 'Deseja enviar uma notificação por e-mail/WhatsApp ao cliente?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, notificar',
                    cancelButtonText: 'Não notificar'
                }).then((notifyResult) => {
                    // Procede com a geração da fatura, com ou sem notificação
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    $.ajax({
                        url: `/admin/users/${userId}/services/${serviceId}/generate-invoice`,
                        method: 'POST',
                        data: {
                            notify_user: notifyResult.isConfirmed
                        },
                        beforeSend: function() {
                            button.attr("disabled", true);
                            button.append('<i class="fa fa-spinner fa-spin ms-2"></i>');
                        },
                        complete: function() {
                            button.prop("disabled", false);
                            button.find('.fa-spinner').remove();
                        },
                        success: function(response) {
                            Swal.fire({
                                text: response.message,
                                icon: 'success',
                                showClass: {
                                    popup: 'animate__animated animate__headShake'
                                }
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                Swal.fire({
                                    text: xhr.responseJSON?.message,
                                    icon: 'warning',
                                    showClass: {
                                        popup: 'animate__animated animate__headShake'
                                    }
                                });
                            } else {
                                Swal.fire({
                                    text: xhr.responseJSON?.message,
                                    icon: 'error',
                                    showClass: {
                                        popup: 'animate__animated animate__headShake'
                                    }
                                });
                            }
                        }
                    });
                });
            }
        });
    });
</script>
@endsection
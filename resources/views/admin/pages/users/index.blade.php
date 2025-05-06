@extends('admin.base')

@section('title', 'Administrador')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-users-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-users-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
        </div>
    </div>
    <div id="content_filters" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="name">Nome:</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Digite o nome">
                            </div>
                            <div class="col-md-4">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Habilitado</option>
                                    <option value="2">Desabilitado</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_range">Período:</label>
                                <input type="text" id="date_range" name="date_range" class="form-control rangecalendar-period" placeholder="Selecione o intervalo">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-users-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalUsers" aria-labelledby="modalUsersLabel">
    <div class="offcanvas-header">
        <h5 id="modalUsersLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-users">
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

    function loadContentPage() {
        $("#content-load-page").html('');
        var url = `{{ url('/admin/users/load') }}`;
        var filters = $('#filter-form').serialize();

        $.get(url + '?' + filters, function(data) {
            $("#content-load-page").html(data);
        });
    }

    function initMasks() {
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

        var input = document.querySelector("#phone");
        var ddiInput = document.querySelector("#ddi");
        if (input && ddiInput) {
            var iti = window.intlTelInput(input, {
                initialCountry: "br",
                separateDialCode: true,
                countrySearch: true,
                autoPlaceholder: "aggressive",
                formatOnDisplay: true,
                nationalMode: false,
                loadUtils: () => import(`{{ asset('/plugins/intl-tel-input/js/utils.js') }}`)
            });

            function atualizarDDI() {
                var dialCode = iti.getSelectedCountryData().dialCode;
                ddiInput.value = '+' + dialCode;
            }

            input.addEventListener('countrychange', atualizarDDI);

            setTimeout(function() {
                if (ddiInput.value) {
                    try {
                        var savedDialCode = ddiInput.value.replace('+', '');
                        if (iti.loadUtils) {
                            iti.promise.then(function() {
                                var fullNumber = ddiInput.value + input.value;
                                iti.setNumber(fullNumber);
                            });
                        } else {
                            var allCountries = iti.getCountryData();
                            for (var i = 0; i < allCountries.length; i++) {
                                if (allCountries[i].dialCode === savedDialCode) {
                                    iti.setCountry(allCountries[i].iso2);
                                    break;
                                }
                            }
                        }
                    } catch (e) {
                        console.log("Erro ao definir país:", e);
                    }
                }
                atualizarDDI();
            }, 500);

            // Preparar para a submissão AJAX
            $(document).on('click', '.button-users-save', function() {
                prepararTelefoneParaEnvio();
            });
            // Antes de enviar o formulário, garantir que o DDI esteja atualizado
            document.querySelector('#form-request-users').addEventListener('submit', function(e) {
                prepararTelefoneParaEnvio();
            });
            function prepararTelefoneParaEnvio() {
                atualizarDDI();
                if (iti.isValidNumber && iti.getNumber) {
                    var numeroCompleto = iti.getNumber();
                    var dialCode = '+' + iti.getSelectedCountryData().dialCode;
                    var phoneWithoutCode = numeroCompleto.replace(dialCode, '');
                    input.value = phoneWithoutCode.trim();
                    ddiInput.value = dialCode;
                } else {
                    var dialCode = '+' + iti.getSelectedCountryData().dialCode;
                    ddiInput.value = dialCode;
                }
            }
        }
    }

    $(document).on("click", ".button-users-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-users-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-users-create", function(e) {
        e.preventDefault();

        $("#modal-content-users").html('');
        $("#modalUsersLabel").text('Nova Administrador');
        var offcanvas = new bootstrap.Offcanvas($('#modalUsers'));
        offcanvas.show();

        var url = `{{ url('/admin/users/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-users").html(data);
                $(".button-users-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Edit
    $(document).on("click", ".button-users-edit", function(e) {
        e.preventDefault();

        let user_id = $(this).data('user-id');

        $("#modal-content-users").html('');
        $("#modalUsersLabel").text('Editar Administrador');
        var offcanvas = new bootstrap.Offcanvas($('#modalUsers'));
        offcanvas.show();

        var url = `{{ url('/admin/users/${user_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-users").html(data);
                $(".button-users-save").attr('data-type', 'edit').attr('data-user-id', user_id);
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-users-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let user_id = button.data('user-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/users/store/') }}`;
        } else {
            if (user_id) {
                var url = `{{ url('/admin/users/${user_id}/update') }}`;
            }
        }

        var form = $('#form-request-users')[0];
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
                        $("#modal-content-users").html('');
                        var offcanvas = bootstrap.Offcanvas.getInstance($('#modalUsers'));
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
    $(document).on('click', '.button-users-delete', function(e) {
        e.preventDefault();
        let user_id = $(this).data('user-id');
        let user_name = $(this).data('user-name');

        Swal.fire({
            title: 'Deseja apagar ' + user_name + '?',
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
                    url: `{{ url('/admin/users/${user_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_user_' + user_id).remove();
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            $('#row_user_' + user_id).remove();
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
</script>
@endsection
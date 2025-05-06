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
        var url = `{{ url('/admin/customers/load') }}`;
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

    // Função global para ser usada nos links de paginação
    function loadPage(url) {
        if (!url) return;
        $("#content-load-page").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando...</p></div>');
        $.get(url, function(data) {
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
                if (ddiInput.value && input.value) {
                    try {
                        // Monta o número completo para o intlTelInput
                        var ddi = ddiInput.value.replace(/[^\d\+]/g, '');
                        var phone = input.value.replace(/\D/g, '');
                        var fullNumber = ddi + phone;
                        iti.setNumber(fullNumber);
                    } catch (e) {
                        console.log("Erro ao definir país:", e);
                    }
                }
                atualizarDDI();
            }, 500);

            // Preparar para a submissão AJAX
            $(document).on('click', '.button-customers-save', function() {
                prepararTelefoneParaEnvio();
            });
            document.querySelector('#form-request-customers').addEventListener('submit', function(e) {
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

    $(document).on("click", ".button-customers-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-customers-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-customers-create", function(e) {
        e.preventDefault();

        $("#modal-content-customers").html('');
        $("#modalUsersLabel").text('Novo Cliente');
        var offcanvas = new bootstrap.Offcanvas($('#modalUsers'));
        offcanvas.show();

        var url = `{{ url('/admin/customers/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-customers").html(data);
                $(".button-customers-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Edit
    $(document).on("click", ".button-customers-edit", function(e) {
        e.preventDefault();

        let customer_id = $(this).data('customer-id');

        $("#modal-content-customers").html('');
        $("#modalUsersLabel").text('Editar Cliente');
        var offcanvas = new bootstrap.Offcanvas($('#modalUsers'));
        offcanvas.show();

        var url = `{{ url('/admin/customers/${customer_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-customers").html(data);
                $(".button-customers-save").attr('data-type', 'edit').attr('data-customer-id', customer_id);
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-customers-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let customer_id = button.data('customer-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/customers/store/') }}`;
        } else {
            if (customer_id) {
                var url = `{{ url('/admin/customers/${customer_id}/update') }}`;
            }
        }

        var form = $('#form-request-customers')[0];
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
                        $("#modal-content-customers").html('');
                        var offcanvas = bootstrap.Offcanvas.getInstance($('#modalUsers'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }
                        @if(Route::currentRouteName() == 'admin.customers.show' || Route::currentRouteName() == 'admin.invoices.show')
                        location.reload();
                        @else
                        loadContentPage();
                        @endif
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
    $(document).on('click', '.button-customers-delete', function(e) {
        e.preventDefault();
        let customer_id = $(this).data('customer-id');
        let customer_name = $(this).data('customer-name');

        Swal.fire({
            title: 'Deseja apagar ' + customer_name + '?',
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
                    url: `{{ url('/admin/customers/${customer_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_customer_' + customer_id).remove();
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            $('#row_customer_' + customer_id).remove();
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

<script>
    // Mostrar endereços do cliente
    $(document).on("click", ".button-customer-addresses", function(e) {
        e.preventDefault();

        let customer_id = $(this).data('customer-id');
        let customer_name = $(this).data('customer-name');

        $("#modal-addresses-content").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando endereços...</p></div>');
        $("#modalAddressesCustomerName").text(customer_name);
        var offcanvas = new bootstrap.Offcanvas($('#modalAddresses'));
        offcanvas.show();

        var url = `{{ url('/admin/customers/${customer_id}/addresses') }}`;
        $.get(url, function(data) {
            $("#modal-addresses-content").html(data);
        });
    });

    // Criar novo endereço
    $(document).on("click", ".button-addresses-create", function(e) {
        e.preventDefault();

        let customer_id = $(this).data('customer-id');
        let customer_name = $(this).data('customer-name');

        $("#modal-addresses-content").html('');
        $("#modalAddressesLabel").text('Novo Endereço para ' + customer_name);
        var offcanvas = new bootstrap.Offcanvas($('#modalAddresses'));
        offcanvas.show();

        var url = `{{ url('/admin/customers/${customer_id}/addresses/create') }}`;
        $.get(url, function(data) {
            $("#modal-addresses-content").html(data);
        });
    });

    // Editar endereço
    $(document).on("click", ".button-addresses-edit", function(e) {
        e.preventDefault();

        let customer_id = $(this).data('customer-id');
        let address_id = $(this).data('address-id');
        let customer_name = $(this).data('customer-name');

        $("#modal-addresses-content").html('');
        $("#modalAddressesLabel").text('Editar Endereço de ' + customer_name);
        var offcanvas = new bootstrap.Offcanvas($('#modalAddresses'));
        offcanvas.show();

        var url = `{{ url('/admin/customers/${customer_id}/addresses/${address_id}/edit') }}`;
        $.get(url, function(data) {
            $("#modal-addresses-content").html(data);
        });
    });

    // Salvar endereço (criar ou atualizar)
    $(document).on('click', '.button-address-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let customer_id = button.data('customer-id');
        let address_id = button.data('address-id');
        let type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/customers/${customer_id}/addresses/store') }}`;
        } else {
            if (address_id) {
                var url = `{{ url('/admin/customers/${customer_id}/addresses/${address_id}/update') }}`;
            }
        }

        var form = $('#form-request-address')[0];
        var formData = new FormData(form);

        $.ajax({
            url: url,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            method: 'POST',
            beforeSend: function() {
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
                        // Fecha o modal de endereço
                        var offcanvasAddresses = bootstrap.Offcanvas.getInstance($('#modalAddresses'));
                        if (offcanvasAddresses) {
                            offcanvasAddresses.hide();
                        }

                        // Atualiza a lista de endereços
                        var url = `{{ url('/admin/customers/${customer_id}/addresses') }}`;
                        $.get(url, function(data) {
                            $("#modal-addresses-content").html(data);
                        });
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

    // Remover endereço
    $(document).on('click', '.button-address-delete', function(e) {
        e.preventDefault();
        let customer_id = $(this).data('customer-id');
        let address_id = $(this).data('address-id');

        Swal.fire({
            title: 'Deseja remover este endereço?',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#333',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: `{{ url('/admin/customers/${customer_id}/addresses/${address_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            // Atualiza a lista de endereços
                            var url = `{{ url('/admin/customers/${customer_id}/addresses') }}`;
                            $.get(url, function(data) {
                                $("#modal-addresses-content").html(data);
                            });
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            text: xhr.responseJSON,
                            icon: 'error',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        });
                    }
                });
            }
        });
    });

    $(document).ready(function() {
        // Inicializa máscara para o CEP se o plugin estiver disponível
        if ($.fn.mask) {
            $('#zipcode').mask('00000-000');
        } else {
            // Implementação manual simples para formatar o CEP
            $('#zipcode').on('input', function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep.length > 5) {
                    cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
                }
                $(this).val(cep);
            });
        }

        // Consulta CEP nas APIs
        $(document).on('blur', '#zipcode', function() {
            var cep = $(this).val().replace(/\D/g, '');

            if (cep.length === 8) {
                // Limpa classes de validação
                $(this).removeClass('is-valid is-invalid');

                // Limpa os campos do endereço para nova consulta se o CEP for diferente
                if ($(this).data('last-cep') !== cep) {
                    $('#street, #district, #city, #state').val('');
                    $(this).data('last-cep', cep);
                } else {
                    // Se for o mesmo CEP da última consulta, não faz nada
                    return;
                }

                // Desabilita os campos enquanto consulta
                $('#street, #district, #city, #state').prop('disabled', true);

                // Mostra indicador de carregamento
                let loadingSpinner = $('<i class="fas fa-spinner fa-spin ms-2 cep-spinner"></i>');
                $('.cep-spinner').remove(); // Remove spinner anterior se existir
                $(this).after(loadingSpinner);

                // Primeiro tenta a BrasilAPI
                consultarBrasilAPI(cep, loadingSpinner);
            }
        });

        // Função para consultar a BrasilAPI
        function consultarBrasilAPI(cep, loadingSpinner) {
            $.ajax({
                url: `https://brasilapi.com.br/api/cep/v1/${cep}`,
                dataType: 'json',
                timeout: 3000, // 3 segundos de timeout
                success: function(data) {
                    // Remove o spinner
                    loadingSpinner.remove();

                    // Preenche os campos com os dados retornados
                    $('#street').val(data.street);
                    $('#district').val(data.neighborhood);
                    $('#city').val(data.city);
                    $('#state').val(data.state);

                    // Habilita os campos
                    $('#street, #district, #city, #state').prop('disabled', false);

                    // Foca no campo de número para facilitar o preenchimento
                    $('#number').focus();

                    // Adiciona classe de validação
                    $('#zipcode').addClass('is-valid');
                },
                error: function() {
                    // Se falhar, tenta a ViaCEP como alternativa
                    consultarViaCEP(cep, loadingSpinner);
                }
            });
        }

        // Função para consultar a ViaCEP (fallback)
        function consultarViaCEP(cep, loadingSpinner) {
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                dataType: 'json',
                timeout: 3000, // 3 segundos de timeout
                success: function(data) {
                    // Remove o spinner
                    loadingSpinner.remove();

                    if (!("erro" in data)) {
                        // Preenche os campos com os dados retornados
                        $('#street').val(data.logradouro);
                        $('#district').val(data.bairro);
                        $('#city').val(data.localidade);
                        $('#state').val(data.uf);

                        // Habilita os campos
                        $('#street, #district, #city, #state').prop('disabled', false);

                        // Foca no campo de número para facilitar o preenchimento
                        $('#number').focus();

                        // Adiciona classe de validação
                        $('#zipcode').addClass('is-valid');
                    } else {
                        // CEP não encontrado
                        $('#zipcode').addClass('is-invalid');
                        $('#street, #district, #city, #state').prop('disabled', false);
                    }
                },
                error: function() {
                    // Remove o spinner
                    loadingSpinner.remove();

                    // Ambas as APIs falharam
                    $('#zipcode').addClass('is-invalid');
                    $('#street, #district, #city, #state').prop('disabled', false);
                }
            });
        }
    });

    // Definir endereço padrão
    $(document).on('change', '.set-default-address', function(e) {
        var address_id = $(this).val();
        var customer_id = $(this).data('customer-id');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });
        $.post(`{{ url('/admin/customers') }}/${customer_id}/addresses/${address_id}/set-default`, {}, function(data) {
            // Atualiza a lista de endereços para refletir o novo padrão
            var url = `{{ url('/admin/customers/${customer_id}/addresses') }}`;
            $.get(url, function(data) {
                $("#modal-addresses-content").html(data);
            });
        }).fail(function(xhr) {
            Swal.fire({
                text: xhr.responseJSON || 'Erro ao definir endereço padrão',
                icon: 'error',
                showClass: {
                    popup: 'animate_animated animate_wobble'
                }
            });
        });
    });
</script>
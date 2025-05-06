<script>
    // Create
    $(document).on("click", ".button-invoices-create", function(e) {
        e.preventDefault();

        $("#modal-content-invoices").html('');
        $("#modalInvoicesLabel").text('Nova Fatura');
        var offcanvas = new bootstrap.Offcanvas($('#modalInvoices'));
        offcanvas.show();

        var url = `{{ url('/admin/invoices/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-invoices").html(data);
                $(".button-invoices-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Edit
    $(document).on("click", ".button-invoices-edit", function(e) {
        e.preventDefault();

        let invoice_id = $(this).data('invoice-id');

        $("#modal-content-invoices").html('');
        $("#modalInvoicesLabel").text('Editar Fatura');
        var offcanvas = new bootstrap.Offcanvas($('#modalInvoices'));
        offcanvas.show();

        var url = `{{ url('/admin/invoices/${invoice_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-invoices").html(data);
                $(".button-invoices-save").attr('data-type', 'edit').attr('data-invoice-id', invoice_id);
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-invoices-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let invoice_id = button.data('invoice-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/invoices/store/') }}`;
        } else {
            if (invoice_id) {
                var url = `{{ url('/admin/invoices/${invoice_id}/update') }}`;
            }
        }

        var form = $('#form-request-invoices')[0];
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
                        @if(Route::currentRouteName() == 'admin.customers.show' || Route::currentRouteName() == 'admin.invoices.show')
                        location.reload();
                        @else
                        $("#modal-content-invoices").html('');
                        var offcanvas = bootstrap.Offcanvas.getInstance($('#modalInvoices'));
                        if (offcanvas) {
                            offcanvas.hide();
                        }
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
    $(document).on('click', '.button-invoices-delete', function(e) {
        e.preventDefault();
        let invoice_id = $(this).data('invoice-id');

        Swal.fire({
            title: 'Deseja apagar a fatura #' + invoice_id + '?',
            text: 'Esta ação é irreversível!',
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
                    url: `{{ url('/admin/invoices/${invoice_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            @if(Route::currentRouteName() == 'admin.customers.show' || Route::currentRouteName() == 'admin.invoices.show')
                            location.reload();
                            @else
                            loadContentPage();
                            @endif
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

    // Cancel
    $(document).on('click', '.button-invoices-cancel', function(e) {
        e.preventDefault();
        let invoice_id = $(this).data('invoice-id');

        Swal.fire({
            title: 'Deseja cancelar a fatura #' + invoice_id + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#333',
            confirmButtonText: 'Sim, cancelar fatura!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: `{{ url('/admin/invoices/${invoice_id}/cancel') }}`,
                    method: 'POST',
                    success: function(data) {
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            @if(Route::currentRouteName() == 'admin.customers.show' || Route::currentRouteName() == 'admin.invoices.show')
                            location.reload();
                            @else
                            loadContentPage();
                            @endif
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

    // Confirm Payment
    $(document).on('click', '.button-invoices-confirm-payment', function(e) {
        e.preventDefault();
        let invoice_id = $(this).data('invoice-id');

        Swal.fire({
            title: 'Deseja confirmar o pagamento da fatura #' + invoice_id + '?',
            icon: 'success',
            showCancelButton: true,
            cancelButtonColor: '#333',
            confirmButtonText: 'Sim, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deseja notificar o cliente?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, notificar',
                    cancelButtonText: 'Não, apenas confirmar'
                }).then((notifyResult) => {
                    if (notifyResult.isConfirmed || notifyResult.dismiss === Swal.DismissReason.cancel) {
                        let notifyClient = notifyResult.isConfirmed ? 1 : 0;

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        });

                        $.ajax({
                            url: `{{ url('/admin/invoices/${invoice_id}/confirm-payment') }}`,
                            method: 'POST',
                            data: {
                                notify: notifyClient
                            }, // Enviando a opção do usuário
                            success: function(data) {
                                Swal.fire({
                                    text: data,
                                    icon: 'success',
                                    showClass: {
                                        popup: 'animate__animated animate__headShake'
                                    }
                                }).then(() => {
                                    @if(Route::currentRouteName() == 'admin.customers.show' || Route::currentRouteName() == 'admin.invoices.show')
                                    location.reload();
                                    @else
                                    loadContentPage();
                                    @endif
                                });
                            },
                            error: function(xhr) {
                                let errorMessage = xhr.responseJSON || 'Erro desconhecido';
                                Swal.fire({
                                    text: errorMessage,
                                    icon: 'error',
                                    showClass: {
                                        popup: 'animate__animated animate__headShake'
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    });


    // Send Reminder
    $(document).on('click', '.button-invoices-send-reminder', function(e) {
        e.preventDefault();
        let invoice_id = $(this).data('invoice-id');

        Swal.fire({
            title: 'Deseja enviar um lembrete para a fatura #' + invoice_id + '?',
            icon: 'info',
            showCancelButton: true,
            cancelButtonColor: '#333',
            confirmButtonText: 'Sim, enviar lembrete!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                $.ajax({
                    url: `{{ url('/admin/invoices/${invoice_id}/send-reminder') }}`,
                    method: 'POST',
                    success: function(data) {
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
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
    // Função para atualizar os índices dos itens dinamicamente
    function updateIndexes() {
        $('#invoice-items tr').each(function(index) {
            $(this).find('input, select').each(function() {
                let name = $(this).attr('name');
                if (name) {
                    name = name.replace(/\[\d+\]/, `[${index}]`);
                    $(this).attr('name', name);
                }
            });
        });
    }

    // Adicionar nova linha de item
    $(document).on('click', '.button-invoices-add-item', function(e) {
        e.preventDefault();

        let rowCount = $('#invoice-items tr').length; // Conta quantas linhas existem
        let newRow = `
            <tr>
                <td><input type="text" name="items[${rowCount}][description]" class="form-control" placeholder="Descrição do item" required></td>
                <td class="p-1"><input type="tel" name="items[${rowCount}][quantity]" class="form-control quantity" style="width:62px;" value="1" required></td>
                <td class="p-1"><input type="text" name="items[${rowCount}][price_unit]" class="form-control price_unit mask-money" style="width:90px;" required></td>
                <td class="p-1"><input type="text" name="items[${rowCount}][price_total]" class="form-control price_total mask-money" style="width:100px;" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm fs-7 p-1 button-invoices-remove-item"><i class="fa fa-times"></i></button></td>
            </tr>
        `;

        $('#invoice-items').append(newRow);
        updateIndexes(); // Atualiza os índices

        initMasks();
    });

    // Remover linha de item
    $(document).on('click', '.button-invoices-remove-item', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        updateIndexes(); // Atualiza os índices após remoção
    });

    // Atualizar o preço total automaticamente
    $(document).on('input', '.quantity, .price_unit', function() {
        let row = $(this).closest('tr');
        let quantity = parseFloat(row.find('.quantity').val()) || 0;
        let priceUnit = parseFloat(row.find('.price_unit').val()) || 0;
        row.find('.price_total').val((quantity * priceUnit).toFixed(2));
    });
</script>
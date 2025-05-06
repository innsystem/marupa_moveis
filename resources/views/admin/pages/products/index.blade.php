@extends('admin.base')

@section('title', 'Produtos')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-products-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-products-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
        </div>
    </div>
    <div id="content_filters" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name">Nome:</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Digite o nome">
                            </div>
                            <div class="col-md-3">
                                <label for="status">Status:</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1">Habilitado</option>
                                    <option value="2">Desabilitado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date_range">Período:</label>
                                <input type="text" id="date_range" name="date_range" class="form-control rangecalendar-period" placeholder="Selecione o intervalo">
                            </div>
                            <div class="col-md-3">
                                <label for="per_page">Itens por página:</label>
                                <select id="per_page" name="per_page" class="form-control">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-products-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalProducts" aria-labelledby="modalProductsLabel">
    <div class="offcanvas-header">
        <h5 id="modalProductsLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-products">
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->
@endsection

@section('pageCSS')
<!-- Flatpickr Timepicker css -->
<link href="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
@endsection

@section('pageJS')
<!-- Query String ToSlug - Transforma o titulo em URL amigavel sem acentos ou espaço -->
<script type="text/javascript" src="{{ asset('/plugins/stringToSlug/jquery.stringToSlug.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/plugins/stringToSlug/speakingurl.js') }}"></script>

<!-- Flatpickr Timepicker Plugin js -->
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/l10n/pt.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

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
        var url = `{{ url('/admin/products/load') }}`;
        var filters = $('#filter-form').serialize();

        $.get(url + '?' + filters, function(data) {
            $("#content-load-page").html(data);
        });
    }

    function loadPage(url) {
        if (!url) return;
        
        $("#content-load-page").html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></div>');
        
        $.get(url, function(data) {
            $("#content-load-page").html(data);
        });
    }

    function initMasks() {
        $('input[name="name"]').stringToSlug({
            setEvents: 'keyup keydown blur',
            getPut: 'input[name="slug"]',
            space: '-',
            replace: '/\s?\([^\)]*\)/gi',
            AND: 'e'
        });

        $('.select2').select2({
            placeholder: "Selecione as categorias",
            allowClear: true
        });
    }

    $(document).on("click", ".button-products-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-products-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-products-create", function(e) {
        e.preventDefault();

        $("#modal-content-products").html('');
        $("#modalProductsLabel").text('Nova Produto');
        var offcanvas = new bootstrap.Offcanvas($('#modalProducts'));
        offcanvas.show();

        var url = `{{ url('/admin/products/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-products").html(data);
                $(".button-products-save").attr('data-type', 'store');
                initMasks();
                initAffiliateLinksScript();
            });
    });

    // Edit
    $(document).on("click", ".button-products-edit", function(e) {
        e.preventDefault();

        let product_id = $(this).data('product-id');

        $("#modal-content-products").html('');
        $("#modalProductsLabel").text('Editar Produto');
        var offcanvas = new bootstrap.Offcanvas($('#modalProducts'));
        offcanvas.show();

        var url = `{{ url('/admin/products/${product_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-products").html(data);
                $(".button-products-save").attr('data-type', 'edit').attr('data-product-id', product_id);
                initMasks();
                initAffiliateLinksScript();
            });
    });

    // Save
    $(document).on('click', '.button-products-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let product_id = button.data('product-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/products/store/') }}`;
        } else {
            if (product_id) {
                var url = `{{ url('/admin/products/${product_id}/update') }}`;
            }
        }

        var form = $('#form-request-products')[0];
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
                // $("#modal-content-products").html('');
                // var offcanvas = bootstrap.Offcanvas.getInstance($('#modalProducts'));
                // if (offcanvas) {
                //     offcanvas.hide();
                // }

                // form.reset();
                loadContentPage();
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
    $(document).on('click', '.button-products-delete', function(e) {
        e.preventDefault();
        let product_id = $(this).data('product-id');
        let product_name = $(this).data('product-name');

        Swal.fire({
            title: 'Deseja apagar ' + product_name + '?',
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
                    url: `{{ url('/admin/products/${product_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_product_' + product_id).remove();
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            $('#row_product_' + product_id).remove();
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

    $(document).on('click', '.button-products-publish-group', function(e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let product_id = $(this).data('product-id');
        let product_name = $(this).data('product-name');

        $.ajax({
            url: `{{ url('/admin/products/${product_id}/publish-product-group') }}`,
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
                    text: data.message,
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

    });

    $(document).on('click', '.button-products-facebook-catalog', function(e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let product_id = $(this).data('product-id');
        let product_name = $(this).data('product-name');

        $.ajax({
            url: `{{ url('/admin/products/${product_id}/facebook-catalog') }}`,
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
                    text: data.message,
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

    });

    $(document).on('click', '.button-products-generate-image', function(e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let product_id = $(this).data('product-id');
        let product_name = $(this).data('product-name');

        $.ajax({
            url: `{{ url('/admin/products/${product_id}/generate-image') }}`,
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
                    title: data.message,
                    html: '<a href="' + data.image + '" target="_Blank"><img src="' + data.image + '" class="border border-radius img-fluid" style="max-width: 300px"></a><br><br><p>' + data.link_affiliate + '</p>',
                    // icon: 'success',
                    showClass: {
                        popup: 'animate__animated animate__headShake'
                    }
                }).then((result) => {});
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

    });

    $(document).on('click', '.button-products-generate-image-feed', function(e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let product_id = $(this).data('product-id');
        let product_name = $(this).data('product-name');

        $.ajax({
            url: `{{ url('/admin/products/${product_id}/generate-image-feed') }}`,
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
                    title: data,
                    icon: 'success',
                    showClass: {
                        popup: 'animate__animated animate__headShake'
                    }
                }).then((result) => {});
            },
            error: function(xhr) {
            console.log(xhr);
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

    });

    // Corrigimos os eventos para evitar múltiplas execuções
    function initAffiliateLinksScript() {
        // Remove eventos antigos antes de adicionar novos
        $(document).off("click", "#add-affiliate-link").on("click", "#add-affiliate-link", function() {
            let container = $("#affiliate-links");
            let div = $("<div>").addClass("input-group mb-2");

            let select = $("<select>").addClass("form-select").attr("name", "marketplace[]");
            select.append('<option value="">Selecione o Marketplace</option>');

            @foreach($integrations as $integration)
            select.append(`<option value="{{ $integration->id }}">{{ $integration->name }}</option>`);
            @endforeach

            div.append(select);
            div.append('<input type="text" class="form-control" name="affiliate_links[]" placeholder="URL do Produto">');
            div.append('<button type="button" class="btn btn-danger remove-link">X</button>');

            container.append(div);
        });

        // Remove eventos antigos antes de adicionar novos para remover links
        $(document).off("click", ".remove-link").on("click", ".remove-link", function() {
            $(this).closest(".input-group").remove();
        });
    }
</script>
@endsection
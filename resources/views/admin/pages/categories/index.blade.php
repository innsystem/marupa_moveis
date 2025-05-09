@extends('admin.base')

@section('title', 'Categorias')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-categories-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-categories-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
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
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-categories-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalCategories" aria-labelledby="modalCategoriesLabel">
    <div class="offcanvas-header">
        <h5 id="modalCategoriesLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-categories">
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->
@endsection

@section('pageCSS')
<!-- Flatpickr Timepicker css -->
<link href="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pageJS')
<!-- Query String ToSlug - Transforma o titulo em URL amigavel sem acentos ou espaço -->
<script type="text/javascript" src="{{ asset('/plugins/stringToSlug/jquery.stringToSlug.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/plugins/stringToSlug/speakingurl.js') }}"></script>

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

    function loadContentPage() {
        $("#content-load-page").html('');
        var url = `{{ url('/admin/categories/load') }}`;
        var filters = $('#filter-form').serialize();

        $.get(url + '?' + filters, function(data) {
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
    }

    $(document).on("click", ".button-categories-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-categories-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });

    $(document).on("change", "#parent_id", function(e) {
        let categorySlug = $(this).find(":selected").data("category-slug"); 
        console.log(categorySlug);

        $('input[name="parent_slug"]').val(categorySlug);
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-categories-create", function(e) {
        e.preventDefault();

        $("#modal-content-categories").html('');
        $("#modalCategoriesLabel").text('Nova Categoria');
        var offcanvas = new bootstrap.Offcanvas($('#modalCategories'));
        offcanvas.show();

        var url = `{{ url('/admin/categories/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-categories").html(data);
                $(".button-categories-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Edit
    $(document).on("click", ".button-categories-edit", function(e) {
        e.preventDefault();

        let category_id = $(this).data('category-id');

        $("#modal-content-categories").html('');
        $("#modalCategoriesLabel").text('Editar Categoria');
        var offcanvas = new bootstrap.Offcanvas($('#modalCategories'));
        offcanvas.show();

        var url = `{{ url('/admin/categories/${category_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-categories").html(data);
                $(".button-categories-save").attr('data-type', 'edit').attr('data-category-id', category_id);
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-categories-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let category_id = button.data('category-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/categories/store/') }}`;
        } else {
            if (category_id) {
                var url = `{{ url('/admin/categories/${category_id}/update') }}`;
            }
        }

        var form = $('#form-request-categories')[0];
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
                        // $("#modal-content-categories").html('');
                        // var offcanvas = bootstrap.Offcanvas.getInstance($('#modalCategories'));
                        // if (offcanvas) {
                        //     offcanvas.hide();
                        // }
                        form.reset();
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
    $(document).on('click', '.button-categories-delete', function(e) {
        e.preventDefault();
        let category_id = $(this).data('category-id');
        let category_name = $(this).data('category-name');

        Swal.fire({
            title: 'Deseja apagar ' + category_name + '?',
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
                    url: `{{ url('/admin/categories/${category_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_category_' + category_id).remove();
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            $('#row_category_' + category_id).remove();
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
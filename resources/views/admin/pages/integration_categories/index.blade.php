@extends('admin.base')

@section('title', 'Categorias de Integração')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-integration-categories-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-integration-categories-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
        </div>
    </div>
    <div id="content_filters" class="row d-none">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="name">Nome da Categoria:</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Digite o nome da categoria">
                            </div>
                            <div class="col-md-4">
                                <label for="integration_id">Integração:</label>
                                <select id="integration_id" name="integration_id" class="form-control">
                                    <option value="">Todas</option>
                                    @foreach($integrations as $integration)
                                    <option value="{{ $integration->id }}">{{ $integration->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="date_range">Período:</label>
                                <input type="text" id="date_range" name="date_range" class="form-control" placeholder="Selecione o período">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="button" id="button-integration-categories-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalIntegrationCategories" aria-labelledby="modalIntegrationCategoriesLabel">
    <div class="offcanvas-header">
        <h5 id="modalIntegrationCategoriesLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-integration-categories">
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

    function loadContentPage() {
        $("#content-load-page").html('');
        var url = `{{ url('/admin/integration_categories/load') }}`;
        var filters = $('#filter-form').serialize();

        $.get(url + '?' + filters, function(data) {
            $("#content-load-page").html(data);
        });
    }

    $(document).on("click", ".button-integration-categories-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-integration-categories-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-integration-categories-create", function(e) {
        e.preventDefault();

        $("#modal-content-integration-categories").html('');
        $("#modalIntegrationCategoriesLabel").text('Nova Categoria de Integração');
        var offcanvas = new bootstrap.Offcanvas($('#modalIntegrationCategories'));
        offcanvas.show();

        var url = `{{ url('/admin/integration_categories/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-integration-categories").html(data);
                $(".button-integration-categories-save").attr('data-type', 'store');
            });
    });

    // Edit
    $(document).on("click", ".button-integration-categories-edit", function(e) {
        e.preventDefault();

        let category_id = $(this).data('category-id');

        $("#modal-content-integration-categories").html('');
        $("#modalIntegrationCategoriesLabel").text('Editar Categoria de Integração');
        var offcanvas = new bootstrap.Offcanvas($('#modalIntegrationCategories'));
        offcanvas.show();

        var url = `{{ url('/admin/integration_categories/${category_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-integration-categories").html(data);
                $(".button-integration-categories-save").attr('data-type', 'edit').attr('data-category-id', category_id);
            });
    });

    // Save
    $(document).on('click', '.button-integration-categories-save', function(e) {
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
            var url = `{{ url('/admin/integration_categories/store/') }}`;
        } else {
            if (category_id) {
                var url = `{{ url('/admin/integration_categories/${category_id}/update') }}`;
            }
        }

        var form = $('#form-request-integration-categories')[0];
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
    $(document).on('click', '.button-integration-categories-delete', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let category_id = $(this).data('category-id');
        let category_name = $(this).data('category-name');

        Swal.fire({
            title: 'Tem certeza?',
            text: "Você está prestes a excluir a categoria: " + category_name,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                var url = `{{ url('/admin/integration_categories/${category_id}/delete') }}`;

                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function(data) {
                        Swal.fire({
                            text: data,
                            icon: 'success',
                            showClass: {
                                popup: 'animate_animated animate_backInUp'
                            },
                            onClose: () => {
                                loadContentPage();
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            text: 'Erro Interno: ' + xhr.responseJSON,
                            icon: 'error',
                            showClass: {
                                popup: 'animate_animated animate_wobble'
                            }
                        });
                    }
                });
            }
        });
    });
</script>
@endsection 
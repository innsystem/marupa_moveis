@extends('admin.base')

@section('title', 'Portfólios')

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
        <div>
            <button type="button" class="btn btn-sm btn-success button-portfolios-create"><i class="fa fa-plus"></i> Adicionar</button>
            <button type="button" class="btn btn-sm btn-primary ms-2 button-portfolios-toggle-filters"><i class="fas fa-filter"></i> Filtros</button>
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
                                <button type="button" id="button-portfolios-filters" class="btn btn-sm btn-primary"><i class="fa fa-search"></i> Filtrar</button>
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
<div class="offcanvas offcanvas-end" tabindex="-1" id="modalPortfolios" aria-labelledby="modalPortfoliosLabel" style="width:650px;">
    <div class="offcanvas-header">
        <h5 id="modalPortfoliosLabel"></h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div> <!-- end offcanvas-header-->

    <div class="offcanvas-body" id="modal-content-portfolios">
    </div> <!-- end offcanvas-body-->
</div> <!-- end offcanvas-->
@endsection

@section('pageCSS')
<!-- Quill css -->
<link href="{{ asset('/tpl_dashboard/vendor/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/tpl_dashboard/vendor/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('/tpl_dashboard/vendor/quill/quill.bubble.css') }}" rel="stylesheet" type="text/css" />

<!-- Flatpickr Timepicker css -->
<link href="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pageJS')
<!-- Quill Editor js -->
<script src="{{ asset('/tpl_dashboard/vendor/quill/quill.min.js') }}"></script>

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
            "locale": "pt",
            "firstDayOfWeek": 1,
        });

        loadContentPage();
    });

    function loadContentPage(page = 1) {
        $("#content-load-page").html('<div class="text-center my-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Carregando...</p></div>');
        var url = `{{ url('/admin/portfolios/load') }}`;
        var filters = $('#filter-form').serialize();
        
        if(page > 1) {
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
        // Inicializar o Quill Editor
        var snowEditor = new Quill('.snow-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        font: []
                    }, {
                        size: []
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        color: []
                    }, {
                        background: []
                    }],
                    [{
                        script: 'super'
                    }, {
                        script: 'sub'
                    }],
                    [{
                        header: [false, 1, 2, 3, 4, 5, 6]
                    }, 'blockquote', 'code-block'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }, {
                        indent: '-1'
                    }, {
                        indent: '+1'
                    }],
                    ['direction', {
                        align: []
                    }],
                    ['link', 'image', 'video'],
                    ['clean']
                ]
            }
        });

        // Carregar conteúdo salvo no editor ao editar
        var existingContent = $('#description').val(); // Pega o valor do textarea
        if (existingContent) {
            snowEditor.root.innerHTML = existingContent; // Carrega no Quill Editor
        }

        $('input[name="title"]').stringToSlug({
            setEvents: 'keyup keydown blur',
            getPut: 'input[name="slug"]',
            space: '-',
            replace: '/\s?\([^\)]*\)/gi',
            AND: 'e'
        });
    }

    $(document).on("click", ".button-portfolios-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-portfolios-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });

    $(document).on('change', '#images', function() {
        let files = this.files;
        let previewContainer = $('#preview-container');
        previewContainer.empty();

        Array.from(files).forEach((file, index) => {
            let reader = new FileReader();
            reader.onload = function(e) {
                let imgPreview = `
                <div class="preview-item border rounded p-1" style="display: inline-block; margin-right: 10px; text-align: center;">
                    <img src="${e.target.result}" alt="Preview" class="avatar-md" style="object-fit: cover; margin-bottom: 5px;">
                </div>
            `;
                previewContainer.append(imgPreview);
            };
            reader.readAsDataURL(file);
        });
    });
</script>

<script>
    // Create
    $(document).on("click", ".button-portfolios-create", function(e) {
        e.preventDefault();

        $("#modal-content-portfolios").html('');
        $("#modalPortfoliosLabel").text('Nova Portfólio');
        var offcanvas = new bootstrap.Offcanvas($('#modalPortfolios'));
        offcanvas.show();

        var url = `{{ url('/admin/portfolios/create') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-portfolios").html(data);
                $(".button-portfolios-save").attr('data-type', 'store');
                initMasks();
            });
    });

    // Edit
    $(document).on("click", ".button-portfolios-edit", function(e) {
        e.preventDefault();

        let portfolio_id = $(this).data('portfolio-id');

        $("#modal-content-portfolios").html('');
        $("#modalPortfoliosLabel").text('Editar Portfólio');
        var offcanvas = new bootstrap.Offcanvas($('#modalPortfolios'));
        offcanvas.show();

        var url = `{{ url('/admin/portfolios/${portfolio_id}/edit') }}`;
        $.get(url,
            $(this).addClass('modal-scrollfix'),
            function(data) {
                $("#modal-content-portfolios").html(data);
                $(".button-portfolios-save").attr('data-type', 'edit').attr('data-portfolio-id', portfolio_id);
                initMasks();
            });
    });

    // Save
    $(document).on('click', '.button-portfolios-save', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        let portfolio_id = button.data('portfolio-id');
        var type = button.data('type');

        if (type == 'store') {
            var url = `{{ url('/admin/portfolios/store/') }}`;
        } else {
            if (portfolio_id) {
                var url = `{{ url('/admin/portfolios/${portfolio_id}/update') }}`;
            }
        }

        var snowEditor = new Quill('.snow-editor');
        $('#description').html(snowEditor.root.innerHTML);

        var form = $('#form-request-portfolios')[0];
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
                        $("#modal-content-portfolios").html('');
                        var offcanvas = bootstrap.Offcanvas.getInstance($('#modalPortfolios'));
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
    $(document).on('click', '.button-portfolios-delete', function(e) {
        e.preventDefault();
        let portfolio_id = $(this).data('portfolio-id');
        let portfolio_name = $(this).data('portfolio-name');

        Swal.fire({
            title: 'Deseja apagar ' + portfolio_name + '?',
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
                    url: `{{ url('/admin/portfolios/${portfolio_id}/delete') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_portfolio_' + portfolio_id).remove();
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

    // Delete Image
    $(document).on('click', '.button-portfolio-images-delete', function(e) {
        e.preventDefault();
        let portfolio_image_id = $(this).data('portfolio-image-id');
        let portfolio_name = $(this).data('portfolio-name');

        Swal.fire({
            title: 'Deseja apagar a imagem do ' + portfolio_name + '?',
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
                    url: `{{ url('/admin/portfolios/${portfolio_image_id}/delete-image') }}`,
                    method: 'POST',
                    success: function(data) {
                        $('#row_portfolio_image_' + portfolio_image_id).remove();
                        loadContentPage();
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

    $(document).on('change', 'input[name="thumb_portfolio"]', function() {
        let image_id = $(this).val(); // ID da imagem selecionada
        let portfolio_id = $('#form-request-portfolios').data('portfolio-id'); // ID do portfólio

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        $.ajax({
            url: `/admin/portfolios/${portfolio_id}/define-image-thumb`,
            method: 'POST',
            data: {
                image_id: image_id
            },
            success: function(response) {
                loadContentPage();
            },
            error: function(xhr) {
                Swal.fire({
                    text: 'Erro ao atualizar imagem destacada.',
                    icon: 'error',
                });
            }
        });
    });
</script>
@endsection
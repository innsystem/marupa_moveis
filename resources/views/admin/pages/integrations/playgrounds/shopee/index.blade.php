@extends('admin.base')

@section('title', 'Playground ' . $title)

@section('content')
<div class="container">
    <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">@yield('title')</h4>
        </div>
    </div>
    <div id="content_filters" class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="filter-form">
                        <input type="hidden" name="slug" value="{{ $slug }}">
                        <div class="row">
                            <div class="col-6 col-md-3 col-lg-2 mb-2">
                                <!-- <label for="type">Tipo:</label> -->
                                <select id="type" name="type" class="form-control">
                                    <option value="products_offers">Produtos em Oferta</option>
                                    <option value="shopee_offers">Shopee Ofertas</option>
                                    <!-- <option value="shop_offers">Ofertas de Lojas</option> -->
                                </select>
                            </div>
                            <div class="col-6 col-md-3 col-lg-3 mb-2 input_products_offers">
                                <!-- <label for="item_id">Item ID:</label> -->
                                <input type="text" id="item_id" name="item_id" class="form-control" placeholder="Ex: 19472058506">
                            </div>

                            <div class="col-12 col-md-3 col-lg-3 mb-2">
                                <!-- <label for="keyword">Palavra chave do Produto:</label> -->
                                <input type="text" id="keyword" name="keyword" class="form-control" placeholder="Ex: Home Appliances">
                            </div>
                            <div class="col-12 col-md-3 col-lg-4 mb-2 input_products_offers">
                                <!-- <label for="category_id">Category ID <a href="https://seller.shopee.com.br/edu/category-guide" target="_Blank"><i class="fa fa-link"></i></a>:</label> -->
                                <select name="category_id" id="category_id" class="form-select select2">
                                    <option value=""></option>
                                    @foreach($categoriesShopee as $category_shopee)
                                    <option value="{{ $category_shopee['api_category_id'] }}">{{ str_replace('20221014 -  KOL - 2022 - ', '', $category_shopee['api_category_name']) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-6 col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Ordem</span>
                                    <select id="sortType" name="sortType" class="form-control">
                                        <option value="2">Mais vendidos</option>
                                        <option value="3">Preço mais alto</option>
                                        <option value="4">Preço mais baixo</option>
                                        <option value="5">Comissão mais alta</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Página</span>
                                    <select id="page" name="page" class="form-control">
                                        @for ($i = 1; $i <= 50; $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text">Limite</span>
                                    <select id="limit" name="limit" class="form-control">
                                        <!-- <option value="5">5 resultados</option>
                                        <option value="10">10 resultados</option> -->
                                        <option value="15">15 resultados</option>
                                        <option value="20">20 resultados</option>
                                        <option value="25">25 resultados</option>
                                        <option value="30">30 resultados</option>
                                        <option value="50">50 resultados</option>
                                        <option value="100">100 resultados</option>
                                        <option value="200">200 resultados</option>
                                        <option value="500">500 resultados</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mt-2">
                                <button type="button" id="button-integrations-filters" class="btn btn-primary"><i class="fa fa-search"></i> Pesquisa</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="content-load-page">
            </div><!-- row -->
        </div> <!-- end card body -->
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <button type="button" id="button-integrations-filters" class="btn btn-sm btn-primary"><i class="fa fa-history"></i> Recarregar</button>
        </div>
    </div>
</div>
@endsection

@section('pageMODAL')
@endsection

@section('pageCSS')
<link href="{{ asset('/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<!-- Flatpickr Timepicker css -->
<link href="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pageJS')
<script src="{{ asset('/plugins/select2/js/select2.min.js') }}"></script>


<!-- Query String ToSlug - Transforma o titulo em URL amigavel sem acentos ou espaço -->
<script type="text/javascript" src="{{ asset('/plugins/stringToSlug/jquery.stringToSlug.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/plugins/stringToSlug/speakingurl.js') }}"></script>

<!-- Flatpickr Timepicker Plugin js -->
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/l10n/pt.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#category_id').select2({
            width: '100%', // Garante que ocupe todo o espaço disponível
            placeholder: "Selecione uma categoria",
            allowClear: true
        });
    });

    function loadContentPage() {
        $("#content-load-page").html('');
        var url = `{{ route('admin.integrations.playground.load', $slug) }}`;
        var filters = $('#filter-form').serialize();

        var button = $('#button-integrations-filters');

        // Exibe o spinner de carregamento
        $("#content-load-page").html('<h3><i class="fa fa-spinner fa-spin"></i> Carregando dados...</h3>');


        $.ajax({
            url: url,
            data: filters,
            method: 'GET',
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
                console.log(data);
                $("#content-load-page").html(data);
                loadLazyImages();
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
    }

    function initMasks() {}

    $(document).on("click", ".button-integrations-toggle-filters", function(e) {
        e.preventDefault();

        $('#content_filters').toggleClass('d-none');
    });

    $(document).on("click", "#button-integrations-filters", function(e) {
        e.preventDefault();

        loadContentPage();
    });

    $(document).on("change", "select#type", function(e) {
        e.preventDefault();
        var type = $(this).val();

        if (type == 'products_offers') {
            $('.input_products_offers').removeClass('d-none');
        } else {
            $('.input_products_offers').addClass('d-none');
        }
    });

    $(document).on('click', '.button-create-product', function(e) {
        e.preventDefault();
        let button = $(this);
        let slug_integration = `{{$slug}}`;

        Swal.fire({
            title: 'Como deseja cadastrar o produto?',
            icon: 'question',
            input: 'radio',
            inputOptions: {
                'queue': 'Agendar',
                'immediate': 'Agora',
            },
            inputValidator: (value) => {
                if (!value) {
                    return 'Você precisa selecionar uma opção!';
                }
            },
            showCancelButton: false,
            showConfirmButton: false,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Continuar',
            onOpen: () => {
                $('.swal2-radio input').on('change', function() {
                    let processType = $(this).val();
                    Swal.close(); // Fecha o Swal atual

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    var url = `{{ url('/admin/integrations/${slug_integration}/playground/createProduct') }}`;

                    $.ajax({
                        url: url,
                        data: {
                            slug_integration: slug_integration,
                            process_type: processType,
                            product_id: button.data('product-id'),
                            product_name: button.data('product-name'),
                            product_images: button.data('product-images'),
                            product_categories: button.data('product-categories'),
                            product_price_min: button.data('product-price-min'),
                            product_price_max: button.data('product-price-max'),
                            product_link: button.data('product-link'),
                        },
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
                            sendNotification("Sucesso!", data, "top-center", "rgba(0,0,0,0.05)", "success");

                            // Swal.fire({
                            //     text: data,
                            //     icon: 'success',
                            //     showClass: {
                            //         popup: 'animate_animated animate_backInUp'
                            //     },
                            // });
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
            },
        });
    });
</script>
@endsection
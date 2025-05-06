@extends('admin.base')

@section('title', 'Configurações')

@section('content')
<div class="container">
    <form id="form-request-settings">
        <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Informações Básicas</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label>Título do Site</label>
                                    <input type="text" class="form-control" name="meta_title" placeholder="Ex: Meu App" value="{{ $result['meta_title'] }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Palavras Chave</label>
                                    <input type="text" class="form-control" name="meta_keywords" placeholder="Palavras Chave" value="{{ $result['meta_keywords'] }}">
                                </div>
                                <div class="form-group mb-0">
                                    <label>Descrição do Site</label>
                                    <textarea rows="3" class="form-control" name="meta_description" placeholder="Descrição do Site">{{ $result['meta_description'] }}</textarea>
                                    <span class="help-block">Máximo de 255 caracteres</span>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Incluir <b>Script</b> na seção HEAD</label>
                                    <textarea rows="4" class="form-control" name="script_head" placeholder="Scripts de Redes Sociais na seção HEAD (Cabeçalho da Página)">{{ $result['script_head'] }}</textarea>
                                </div>
                                <div class="form-group mb-0">
                                    <label>Incluir <b>Script</b> na seção BODY</label>
                                    <textarea rows="4" class="form-control" name="script_body" placeholder="Scripts de Redes Sociais na seção BODY (Corpo da Página)">{{ $result['script_body'] }}</textarea>
                                </div>
                            </div><!-- col -->

                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label>Logo da Empresa</label>
                                    <label for="logo-input" class="cursor-pointer col-12">
                                        <div id="logo-preview" class="mb-2">
                                            @if ($result['logo'] ?? false)
                                            <img src="{{ asset('storage/' . $result['logo']) }}" id="logo-img" class="img-thumbnail" style="max-width: 260px;">
                                            @else
                                            <img src="{{ asset('/galerias/avatares/sem_foto.jpg') }}" id="logo-img" class="img-thumbnail" style="max-width: 260px;">
                                            @endif
                                        </div>
                                        <input type="file" name="logo" id="logo-input" accept="image/*" class="form-control" style="display:none;">
                                    </label>
                                </div>

                                <div class="form-group mb-3">
                                    <label>Favicon do Site</label>
                                    <label for="favicon-input" class="cursor-pointer col-12">
                                        <div id="favicon-preview" class="mb-2">
                                            @if ($result['favicon'] ?? false)
                                            <img src="{{ asset('storage/' . $result['favicon']) }}" id="favicon-img" class="img-thumbnail" style="max-width: 120px;">
                                            @else
                                            <img src="{{ asset('/galerias/avatares/sem_foto.jpg') }}" id="favicon-img" class="img-thumbnail" style="max-width: 120px;">
                                            @endif
                                        </div>
                                        <input type="file" name="favicon" id="favicon-input" accept="image/*" class="form-control" style="display:none;">
                                    </label>
                                </div>
                            </div><!-- col -->
                        </div>

                        <!-- Row Buttons -->
                        <div class="row">
                            <div class="col-12 border-top pt-3 mt-3">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success button-settings-update"><i class="fa fa-check"></i> Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-2 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Informações de Contato</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label>Nome do Site</label>
                                    <input type="text" class="form-control" name="site_name" placeholder="Nome do Site" value="{{ $result['site_name'] }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Proprietário</label>
                                    <input type="text" class="form-control" name="site_proprietary" placeholder="Proprietário" value="{{ $result['site_proprietary'] }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Documento da Empresa</label>
                                    <input type="text" class="form-control mask-document" name="site_document" placeholder="Documento da Empresa" value="{{ $result['site_document'] }}" />
                                </div>

                                <div class="form-group mb-0">
                                    <label>E-mail de Contato</label>
                                    <input type="text" class="form-control" name="site_email" placeholder="E-mail de Contato" value="{{ $result['site_email'] }}">
                                </div>
                            </div><!-- col -->
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label>Telefone</label>
                                    <input type="text" class="form-control mask-phone" name="telephone" placeholder="Telefone" value="{{ $result['telephone'] }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Celular</label>
                                    <input type="text" class="form-control mask-phone" name="cellphone" placeholder="Celular" value="{{ $result['cellphone'] }}">
                                </div>

                                <div class="form-group mb-3">
                                    <label>Endereço do Site </label>
                                    <input type="text" class="form-control" name="address" placeholder="Endereço do Site" value="{{ $result['address'] }}" />
                                </div>

                                <div class="form-group mb-0">
                                    <label>Horário de Atendimento</label>
                                    <textarea rows="4" class="form-control" name="hour_open" placeholder="Horário de Atendimento">{{ $result['hour_open'] }}</textarea>
                                </div>
                            </div>

                        </div><!-- row -->

                        <!-- Row Buttons -->
                        <div class="row">
                            <div class="col-12 border-top pt-3 mt-3">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success button-settings-update"><i class="fa fa-check"></i> Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-2 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Configuração da API (avançado)</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group mb-3">
                                    <label>Cliente ID</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="client-id" name="client_id" value="{{ $result['client_id'] }}" readonly>
                                        <button class="btn btn-primary" type="button" id="generate-client-id">
                                            <i class="fas fa-sync-alt"></i> <!-- Ícone de gerar -->
                                        </button>
                                        <button class="btn btn-secondary" type="button" id="copy-client-id">
                                            <i class="fas fa-copy"></i> <!-- Ícone de copiar -->
                                        </button>
                                    </div>
                                    <div id="msg-success-client-id"></div>
                                </div>
                                <div class="form-group mb-3">
                                    <label>Cliente Secret</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="client-secret" name="client_secret" value="{{ $result['client_secret'] }}" readonly>
                                        <button class="btn btn-primary" type="button" id="generate-client-secret">
                                            <i class="fas fa-sync-alt"></i> <!-- Ícone de gerar -->
                                        </button>
                                        <button class="btn btn-secondary" type="button" id="copy-client-secret">
                                            <i class="fas fa-copy"></i> <!-- Ícone de copiar -->
                                        </button>
                                    </div>
                                    <div id="msg-success-client-secret"></div>
                                </div>
                            </div><!-- col -->

                        </div><!-- row -->

                        <!-- Row Buttons -->
                        <div class="row">
                            <div class="col-12 border-top pt-3 mt-3">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-success button-settings-update"><i class="fa fa-check"></i> Salvar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('pageMODAL')
<!-- Crop Modal -->
<div id="crop-modal" class="modal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cortar Imagem</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="crop-container">
                    <img id="crop-image" src="" alt="Imagem para cortar" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="crop-save-btn" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pageCSS')
<!-- Cropper css -->
<link href="{{ asset('/plugins/croppperjs/cropper.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('pageJS')
<!-- Crop Functions -->
<script src="{{ asset('/plugins/croppperjs/cropper.min.js') }}"></script>
<script>
    let cropper;
    let cropType; // 'logo' ou 'favicon'
    let currentFileType = 'image/jpeg';

    function initCropper(input, aspectRatio) {
        const file = input.files[0];
        if (!file) return;
        currentFileType = file.type;
        const reader = new FileReader();

        reader.onload = function(e) {
            const image = document.getElementById('crop-image');
            image.src = e.target.result;

            // Mostra o modal de corte
            $('#crop-modal').modal('show');

            // Destruir instância anterior
            if (cropper) {
                cropper.destroy();
            }

            // Inicializar Cropper.js
            cropper = new Cropper(image, {
                aspectRatio: aspectRatio,
                viewMode: 1,
                background: false,
            });
        };

        reader.readAsDataURL(file);
    }

    // Logo - recorte livre
    document.getElementById('logo-input').addEventListener('change', function() {
        cropType = 'logo';
        initCropper(this, NaN); // aspectRatio livre
    });
    // favicon - 1:1
    document.getElementById('favicon-input').addEventListener('change', function() {
        cropType = 'favicon';
        initCropper(this, 1); // Aspect Ratio 1:1
        currentFileType = this.files[0] ? this.files[0].type : 'image/jpeg';
    });

    // Salvar imagem cortada
    document.getElementById('crop-save-btn').addEventListener('click', function() {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas();
            // Se for PNG, salva como PNG, senão JPEG
            let mimeType = (currentFileType === 'image/png') ? 'image/png' : 'image/jpeg';
            let fileExt = mimeType === 'image/png' ? 'png' : 'jpg';
            canvas.toBlob(function(blob) {
                const formData = new FormData();
                formData.append(cropType, blob, `${cropType}.${fileExt}`);

                // Enviar para o controller
                $.ajax({
                    url: `{{ url('/admin/settings/update-images') }}`,
                    method: 'POST',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    beforeSend: function(response) {
                        $('#' + cropType + '-img').attr('src', `{{ asset('/galerias/avatares/loading.gif?1')}}`);
                    },
                    success: function(response) {
                        $('#' + cropType + '-input').attr('value', response);
                        $('#' + cropType + '-img').attr('src', response);
                    },
                    error: function(xhr) {
                        Swal.fire('Erro', xhr.responseJSON, 'error');
                    }
                });
            }, mimeType);
        }
        $('#crop-modal').modal('hide');
    });
</script>

<script>
    // Função para copiar conteúdo
    function copyToClipboard(elementId, message) {
        const input = document.getElementById(elementId);
        navigator.clipboard.writeText(input.value).then(() => {
            $('#msg-success-' + elementId).html('<div class="alert alert-success p-1 ps-2 fs-7 mt-1">' + message + '</div>');
        });
        removeMessageAfterDelay('msg-success-' + elementId);
    }

    // Função para gerar novo valor
    function generateRandomValue(length) {
        return Math.random().toString(36).substr(2, length) + Math.random().toString(36).substr(2, length);
    }

    function removeMessageAfterDelay(elementId, delay = 2000) {
        setTimeout(() => {
            $('#' + elementId).html('');
        }, delay);
    }

    // Eventos dos botões
    document.getElementById('generate-client-id').addEventListener('click', () => {
        const newClientId = generateRandomValue(8); // Gerar um valor aleatório curto
        document.getElementById('client-id').value = newClientId;
        $('#msg-success-client-id').html('<div class="alert alert-success p-1 ps-2 fs-7 mt-1">Novo Client ID gerado com successo</div>');
        removeMessageAfterDelay('msg-success-client-id');
    });

    document.getElementById('generate-client-secret').addEventListener('click', () => {
        const newClientSecret = generateRandomValue(128); // Gerar um valor aleatório longo
        document.getElementById('client-secret').value = newClientSecret;
        $('#msg-success-client-secret').html('<div class="alert alert-success p-1 ps-2 fs-7 mt-1">Novo Client Secret gerado com successo</div>');
        removeMessageAfterDelay('msg-success-client-secret');
    });

    document.getElementById('copy-client-id').addEventListener('click', () => {
        copyToClipboard('client-id', 'Cliente ID copiado com sucesso!');
    });

    document.getElementById('copy-client-secret').addEventListener('click', () => {
        copyToClipboard('client-secret', 'Cliente Secret copiado com sucesso!');
    });
</script>

<script>
    // Save
    $(document).on('click', '.button-settings-update', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        var url = `{{ url('/admin/settings/update') }}`;

        var form = $('#form-request-settings')[0];
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
                        // location.reload();
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
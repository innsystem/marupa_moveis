@extends('admin.base')

@section('title', 'CommanderCrud')

@section('content')
<div class="container">
    <form id="form-request-commander">
        <div class="py-2 gap-2 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Comando de Criação de Recursos (Make:Crud)</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label for="name">Nome do Recurso (Ex: Posts):</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Ex: Posts">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="namespace">Namespace (Ex: Admin):</label>
                                    <input type="text" class="form-control" name="namespace" id="namespace" placeholder="Ex: Admin">
                                </div>
                                <div class="form-group mb-3">
                                    <label for="friendly_name">Nome Amigável (Ex: Postagem):</label>
                                    <input type="text" class="form-control" name="friendly_name" id="friendly_name" placeholder="Ex: Postagem">
                                </div>
                                <div id="columns-wrapper">
                                    <div class="column-row d-flex flex-wrap gap-2 mb-2">
                                        <div><input type="text" name="column_name[]" placeholder="Ex: title ou description" class="form-control" required></div>
                                        <div>
                                            <select name="column_type[]" class="form-control">
                                                <option value="string">String</option>
                                                <option value="text">Text</option>
                                                <option value="integer">Integer</option>
                                                <option value="boolean">Boolean</option>
                                                <option value="decimal">Decimal</option>
                                                <option value="select_status">Status</option>
                                                <!-- Outros tipos -->
                                            </select>
                                        </div>
                                        <div>
                                            <input type="text" name="column_options[]" placeholder="Ex: unique, nullable" class="form-control">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger remove-column">Remover</button>
                                    </div>
                                </div>
                                <button type="button" id="add-column" class="btn btn-sm btn-secondary">Adicionar Coluna</button>
                            </div>
                        </div>
                        <!-- Row Buttons -->
                        <div class="row">
                            <div class="col-12 border-top pt-3 mt-3">
                                <div class="d-flex gap-3">
                                    <button type="button" class="btn btn-success button-commander-create"><i class="fa fa-check"></i> Criar Recurso</button>
                                    <button type="button" class="btn btn-warning button-commander-migrate"><i class="fa fa-upload"></i> Rodar Migrate</button>
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
@endsection

@section('pageCSS')
@endsection

@section('pageJS')
<script>
    document.getElementById('add-column').addEventListener('click', function() {
        const wrapper = document.getElementById('columns-wrapper');
        const newRow = document.createElement('div');
        newRow.className = 'column-row d-flex flex-wrap gap-2 mb-2';
        newRow.innerHTML = `
        <div><input type="text" name="column_name[]" placeholder="Ex: title ou description" class="form-control" required></div>
        <div>
        <select name="column_type[]" class="form-control">
            <option value="string">String</option>
            <option value="text">Text</option>
            <option value="integer">Integer</option>
            <option value="boolean">Boolean</option>
            <option value="decimal">Decimal</option>
            <option value="select_status">Status</option>
        </select>
        </div>
        <div><input type="text" name="column_options[]" placeholder="Ex: unique, nullable" class="form-control"></div>
        <button type="button" class="btn btn-sm btn-danger remove-column">Remover</button>
    `;
        wrapper.appendChild(newRow);
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-column')) {
            e.target.parentNode.remove();
        }
    });
</script>
<script>
    $(document).on('click', '.button-commander-create', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        var url = `{{ url('/admin/commander/create') }}`;

        var form = $('#form-request-commander')[0];
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
                        location.reload();
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

    $(document).on('click', '.button-commander-migrate', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);
        var url = `{{ url('/admin/commander/migrate') }}`;

        $.ajax({
            url: url,
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
                        location.reload();
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
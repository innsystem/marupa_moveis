<form id="form-request-permissions">
    <div class="modal-body">
        <div id="permissions-rows">
            <div class="row mb-2 permission-row">
                <div class="col-5">
                    <select name="key[]" class="form-select">
                        @foreach($routes as $route)
                        <option value="{{$route['uri']}}">{{$route['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control" name="title[]" placeholder="Digite o título">
                </div>
                <div class="col-2 d-flex align-items-center">
                    <button type="button" class="btn btn-danger btn-sm remove-permission-row" title="Remover"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-permission-row"><i class="fa fa-plus"></i> Adicionar Permissão</button>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-permissions-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
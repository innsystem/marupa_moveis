<form id="form-request-integrations">
    <div class="modal-body">
        <h4 class="h4 mb-2">{{ isset($result->name) ? $result->name : '' }}</h4>

        <div class="form-group mb-3">
            <label for="site_name" class="col-sm-12">Nome do Site:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="site_name" name="site_name" placeholder="Digite o Nome do Site" value="{{ isset($result->settings['site_name']) ? $result->settings['site_name'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="site_email" class="col-sm-12">E-mail:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="site_email" name="site_email" placeholder="Digite o E-mail" value="{{ isset($result->settings['site_email']) ? $result->settings['site_email'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="client_id" class="col-sm-12">Client ID:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="client_id" name="client_id" placeholder="Digite Client ID" value="{{ isset($result->settings['client_id']) ? $result->settings['client_id'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="client_secret" class="col-sm-12">Client Secret:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="client_secret" name="client_secret" placeholder="Digite Client Secret" value="{{ isset($result->settings['client_secret']) ? $result->settings['client_secret'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="id_template" class="col-sm-12">ID Template:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="id_template" name="id_template" placeholder="Digite o ID do Template Padrão" value="{{ isset($result->settings['id_template']) ? $result->settings['id_template'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="status" class="col-sm-12">Status:</label>
            <div class="col-sm-12">
                <select name="status" id="status" class="form-select">
                    @foreach($statuses as $status)
                    <option value="{{$status->id}}" @if (isset($result->status) && $result->status == $status->id) selected @endif>{{$status->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-integrations-save"><i class="fa fa-check"></i> Salvar Alterações</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
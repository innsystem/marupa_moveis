<form id="form-request-integrations">
    <div class="modal-body">
        <h4 class="h4 mb-2">{{ isset($result->name) ? $result->name : '' }}</h4>

        <div class="form-group mb-3">
            <label for="app_id" class="col-sm-12">App ID (Api Affiliate):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="app_id" name="app_id" placeholder="Digite APP ID" value="{{ isset($result->settings['app_id']) ? $result->settings['app_id'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="secret_key" class="col-sm-12">Secret Key (Api Affiliate):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="secret_key" name="secret_key" placeholder="Digite Secret Key" value="{{ isset($result->settings['secret_key']) ? $result->settings['secret_key'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="parter_id" class="col-sm-12">Parter ID (Api Seller):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="parter_id" name="parter_id" placeholder="Digite Parter ID" value="{{ isset($result->settings['parter_id']) ? $result->settings['parter_id'] : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="parter_key" class="col-sm-12">Parter Key (Api Seller):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="parter_key" name="parter_key" placeholder="Digite Parter Key" value="{{ isset($result->settings['parter_key']) ? $result->settings['parter_key'] : '' }}">
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
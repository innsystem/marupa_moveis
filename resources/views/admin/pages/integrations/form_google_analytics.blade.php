<form id="form-request-integrations">
    <div class="modal-body">
        <h4 class="h4 mb-2">{{ isset($result->name) ? $result->name : '' }}</h4>
        <div class="form-group mb-3">
            <label for="description" class="col-sm-12">Descrição da Extensão:</label>
            <div class="col-sm-12">
                <textarea cols="2" rows="2" class="form-control" id="description" name="description" placeholder="Digite uma descrição...">{{ isset($result->description) ? $result->description : '' }}</textarea>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="tracking_id" class="col-sm-12">Tracking ID (Google User ID):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="tracking_id" name="tracking_id" placeholder="Digite Tracking ID" value="{{ isset($result->settings['tracking_id']) ? $result->settings['tracking_id'] : '' }}">
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
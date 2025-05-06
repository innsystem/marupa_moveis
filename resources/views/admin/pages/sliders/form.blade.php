<form id="form-request-sliders">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="image" class="col-sm-12">Imagem:</label>
            <div class="col-sm-12">
                <input type="file" accept=".jpg, .jpeg, .png, .hiff" class="form-control" id="image" name="image">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="title" class="col-sm-12">Título:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="title" name="title" placeholder="Digite o title" value="{{ isset($result->title) ? $result->title : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="href" class="col-sm-12">Link Externo (opcional):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="href" name="href" placeholder="Digite o href" value="{{ isset($result->href) ? $result->href : '' }}">
            </div>
        </div>
        <div class="form-group mb-3 d-none">
            <label for="target" class="col-sm-12">Target:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="target" name="target" placeholder="Digite o target" value="{{ isset($result->target) ? $result->target : 'page-home' }}">
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
        <div class="form-group mb-3">
            <label for="date_start" class="col-sm-12">Data de Início:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control datepicker" id="date_start" name="date_start" placeholder="Selecione a data de início" value="{{ isset($result->date_start) ? $result->date_start : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="date_end" class="col-sm-12">Data de Fim:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control datepicker" id="date_end" name="date_end" placeholder="Selecione a data de fim" value="{{ isset($result->date_end) ? $result->date_end : '' }}">
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-sliders-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
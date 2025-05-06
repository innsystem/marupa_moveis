<form id="form-request-pages">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="title" class="col-sm-12">Título da Página:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="title" name="title" placeholder="Digite o title" value="{{ isset($result->title) ? $result->title : '' }}">
            </div>
        </div>
        <div class="form-group mb-3 d-none">
            <label for="slug" class="col-sm-12">URL Amigável:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="slug" name="slug" placeholder="Digite o slug" value="{{ isset($result->slug) ? $result->slug : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="keywords" class="col-sm-12">Palavras Chaves:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="keywords" name="keywords" placeholder="Digite o keywords" value="{{ isset($result->keywords) ? $result->keywords : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="content" class="col-sm-12">Conteúdo da Página:</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="content" name="content" placeholder="Faça a Descrição do Conteúdo..." style="display: none;">{{ isset($result->content) ? $result->content : '' }}</textarea>
                <div class="snow-editor" style="height: 250px;"></div>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="status" class="col-sm-12">Status da Página:</label>
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
        <button type="button" class="btn btn-success button-pages-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
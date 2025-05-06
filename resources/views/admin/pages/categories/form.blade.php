<form id="form-request-categories">
    <div class="modal-body">
        <input type="hidden" name="parent_slug" id="parent_slug" value="">
        <div class="form-group mb-3">
            <label for="parent_id" class="col-sm-12">Categoria Principal:</label>
            <div class="col-sm-12">
                <select name="parent_id" id="parent_id" class="form-select">
                    <option value="">-- Nenhuma</option>
                    @foreach($categories as $category)
                    @if($category->parent_id == null)
                    <option value="{{$category->id}}" data-category-slug="{{$category->slug}}" @if(isset($result->parent_id) && $result->parent_id == $category->id) selected @endif>{{ isset($category->parent_id) ? ' •' : '' }} {{$category->name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome da Categoria:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o Nome da Categoria" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>
        <div class="form-group mb-3 d-none1">
            <label for="slug" class="col-sm-12">Url Amigável:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="slug" name="slug" placeholder="Digite o Url Amigável" value="{{ isset($result->slug) ? $result->slug : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="thumb" class="col-sm-12">Imagem:</label>
            <div class="col-sm-12">
                <input type="file" class="form-control" id="thumb" name="thumb" placeholder="Digite o thumb" value="{{ isset($result->thumb) ? $result->thumb : '' }}">
            </div>
        </div>

        <div class="form-group mb-3 d-none">
            <label for="description" class="col-sm-12">Descrição:</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="description" name="description" placeholder="Digite a descrição">{{ isset($result->description) ? $result->description : '' }}</textarea>
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
        <button type="button" class="btn btn-success button-categories-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
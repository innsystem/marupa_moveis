<form id="form-request-portfolios" data-portfolio-id="{{ isset($result->id) ? $result->id : '' }}">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="images" class="col-sm-12">Imagens:</label>
            <div class="col-sm-12">
                <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
            </div>
            <div id="preview-container" class="mt-2"></div>
            <hr>
            @foreach($result->images as $image)
            <div id="row_portfolio_image_{{$image->id}}" class="border p-1 rounded mb-2" data-id="{{ $image->id }}" style="display: inline-block; margin-right: 10px; text-align: center;">
                <img src="{{ asset('storage/' . $image->image_path) }}" alt="Imagem" class="avatar-md border rounded" style="object-fit: cover; margin-bottom: 5px;">
                <br>
                <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                    <label for="thumb_{{$image->id}}" class="cursor-pointer fs-7">
                        <input type="radio" id="thumb_{{$image->id}}" name="thumb_portfolio" value="{{ $image->id }}" {{ $image->featured ? 'checked' : '' }} class="m-0 p-0 cursor-pointer"> Capa
                    </label>
                    <button type="button" class="btn btn-sm btn-danger fs-7 p-0 px-1 button-portfolio-images-delete" data-portfolio-image-id="{{ $image->id }}" data-portfolio-name="{{ isset($result->title) ? $result->title : '' }}">Remover</button>
                </div>
            </div>
            @endforeach
        </div>

        <div class="form-group mb-3">
            <label for="title" class="col-sm-12">Título do Portfólio:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="title" name="title" placeholder="Digite o title" value="{{ isset($result->title) ? $result->title : '' }}">
            </div>
        </div>
        <div class="form-group mb-3 d-none">
            <label for="slug" class="col-sm-12">URL Amigável:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="slug" name="slug" placeholder="Digite o URL Amigável" value="{{ isset($result->slug) ? $result->slug : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="col-sm-12">Descrição:</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="description" name="description" placeholder="Digite o description" style="display: none;">{{ isset($result->description) ? $result->description : '' }}</textarea>
                <div class="snow-editor" style="height: 250px;"></div>
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
            <label for="location" class="col-sm-12">Localização:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="location" name="location" placeholder="Digite a localização" value="{{ isset($result->location) ? $result->location : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="type" class="col-sm-12">Tipo:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="type" name="type" placeholder="Digite o tipo (ex: Hotel, Residencial, etc)" value="{{ isset($result->type) ? $result->type : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="sort_order" class="col-sm-12">Posição de Exibição:</label>
            <div class="col-sm-12">
                <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="Digite o sort_order" value="{{ isset($result->sort_order) ? $result->sort_order : '0' }}">
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-portfolios-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
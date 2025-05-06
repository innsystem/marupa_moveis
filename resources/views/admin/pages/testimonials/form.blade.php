<form id="form-request-testimonials">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome do Cliente:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o Nome do Cliente" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="avatar" class="col-sm-12">Imagem ou Logo:</label>
            <div class="col-sm-12">
                <input type="file" accept=".jpg, .jpeg, .png, .hiff" class="form-control" id="avatar" name="avatar">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="content" class="col-sm-12">Depoimento:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="content" name="content" placeholder="Digite o Depoimento" value="{{ isset($result->content) ? $result->content : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="rating" class="col-sm-12">Avaliação do Cliente:</label>
            <div class="col-sm-12">
                <div class="rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <input type="radio" id="star-{{ $i }}" name="rating" value="{{ $i }}" {{ (isset($result->rating) && $result->rating == $i) ? 'checked' : '' }}>
                        <label for="star-{{ $i }}" class="star">
                            <i class="fas fa-star"></i>
                        </label>
                        @endfor
                </div>
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="localization" class="col-sm-12">Localização (Cidade/Estado):</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="localization" name="localization" placeholder="Digite o Localização (Cidade/Estado)" value="{{ isset($result->localization) ? $result->localization : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="sort_order" class="col-sm-12">Posição de Exibição:</label>
            <div class="col-sm-12">
                <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="Digite o sort_order" value="{{ isset($result->sort_order) ? $result->sort_order : '0' }}">
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
        <button type="button" class="btn btn-success button-testimonials-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
<form id="form-request-products">
    <div class="modal-body">
        {{-- Nome do Produto --}}
        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o nome do produto" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>

        {{-- Slug --}}
        <div class="form-group mb-3 d-none">
            <label for="slug" class="col-sm-12">URL Amigável:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="slug" name="slug" placeholder="Digite a url amigável" value="{{ isset($result->slug) ? $result->slug : '' }}">
            </div>
        </div>

        {{-- Descrição --}}
        <div class="form-group mb-3 d-none">
            <label for="description" class="col-sm-12">Descrição:</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="description" name="description" placeholder="Digite a descrição">{{ isset($result->description) ? $result->description : '' }}</textarea>
            </div>
        </div>

        {{-- Categorias (seleção múltipla) --}}
        <div class="form-group mb-3">
            <label for="categories" class="col-sm-12">Categorias:</label>
            <div class="col-sm-12">
                <select name="categories[]" id="categories" class="form-select select2" multiple style="height: 300px;">
                    @foreach($getCategories as $category)
                    <optgroup label="{{ $category->name }}"> {{-- Categoria Principal --}}
                        @foreach($category->children as $subcategory)
                        <option value="{{ $subcategory->id }}"
                            @if(isset($result->categories) && in_array($subcategory->id, $result->categories->pluck('id')->toArray()))
                            selected
                            @endif>
                            — {{ $subcategory->name }} {{-- Identação para subcategorias --}}
                        </option>
                        @endforeach
                    </optgroup>
                    @endforeach
                </select>
            </div>
        </div>


        {{-- Upload de imagens ou inserir link --}}
        <div class="form-group mb-3">
            <label class="col-sm-12">Imagens:</label>
            <div class="col-sm-12">
                <input type="file" class="form-control d-none" name="images[]" multiple>
                <small class="text-muted d-none">Ou insira links abaixo:</small>
                <textarea class="form-control mt-2" id="images" name="images" placeholder="Cole os links separados por vírgula">{{ isset($result->images) ? implode(',', $result->images) : '' }}</textarea>
            </div>
        </div>

        {{-- Preço --}}
        <div class="form-group mb-3 d-none">
            <label for="price" class="col-sm-12">Preço:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control mask-money" id="price" name="price" placeholder="Digite o preço" value="{{ isset($result->price) ? $result->price : 0 }}">
            </div>
        </div>

        {{-- Preço promocional --}}
        <div class="form-group mb-3 d-none">
            <label for="price_promotion" class="col-sm-12">Preço promocional:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control mask-money" id="price_promotion" name="price_promotion" placeholder="Digite o preço promocional" value="{{ isset($result->price_promotion) ? $result->price_promotion : 0 }}">
            </div>
        </div>

        {{-- Links de afiliados --}}
        <div class="form-group mb-3">
            <label class="col-sm-12">Links de Afiliados:</label>
            <div class="col-sm-12">
                <div id="affiliate-links">
                    @if(isset($result->affiliateLinks))
                    @foreach($result->affiliateLinks as $link)
                    <div class="input-group mb-2">
                        <select class="form-select" name="marketplace[]">
                            <option value="">Selecione o Marketplace</option>
                            @foreach($integrations as $integration)
                            <option value="{{ $integration->id }}"
                                {{ (isset($link['integration_id']) && $link['integration_id'] == $integration->id) ? 'selected' : '' }}>
                                {{ $integration->name }}
                            </option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control" name="affiliate_links[]" placeholder="URL do Produto" value="{{ $link['affiliate_link'] ?? '' }}">
                        <button type="button" class="btn btn-danger remove-link">X</button>
                    </div>
                    @endforeach
                    @else
                    <div class="input-group mb-2">
                        <select class="form-select" name="marketplace[]">
                            <option value="">Selecione o Marketplace</option>
                            @foreach($integrations as $integration)
                            <option value="{{ $integration->id }}"
                                {{ (isset($link['integration_id']) && $link['integration_id'] == $integration->id) ? 'selected' : '' }}>
                                {{ $integration->name }}
                            </option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control" name="affiliate_links[]" placeholder="URL do Produto" value="">
                        <button type="button" class="btn btn-danger remove-link">X</button>
                    </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-affiliate-link">Adicionar Link</button>
            </div>
        </div>

        {{-- Status --}}
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

    {{-- Botões --}}
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-products-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
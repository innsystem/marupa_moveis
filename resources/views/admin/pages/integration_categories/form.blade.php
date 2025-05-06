<form id="form-request-integration-categories">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="integration_id" class="col-sm-12">Integração:</label>
            <div class="col-sm-12">
                <select name="integration_id" id="integration_id" class="form-select" required>
                    <option value="">-- Selecione a Integração --</option>
                    @foreach($integrations as $integration)
                    <option value="{{$integration->id}}" @if(isset($result->integration_id) && $result->integration_id == $integration->id) selected @endif>{{$integration->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label for="api_category_id" class="col-sm-12">ID da Categoria:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="api_category_id" name="api_category_id" placeholder="Digite o ID da categoria" value="{{ isset($result->api_category_id) ? $result->api_category_id : '' }}" required>
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label for="api_category_name" class="col-sm-12">Nome da Categoria:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="api_category_name" name="api_category_name" placeholder="Digite o nome da categoria" value="{{ isset($result->api_category_name) ? $result->api_category_name : '' }}" required>
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label for="api_category_link_affiliate" class="col-sm-12">Link de Afiliado:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="api_category_link_affiliate" name="api_category_link_affiliate" placeholder="Digite o link de afiliado" value="{{ isset($result->api_category_link_affiliate) ? $result->api_category_link_affiliate : '' }}">
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label for="api_category_commission" class="col-sm-12">Comissão (%):</label>
            <div class="col-sm-12">
                <input type="number" class="form-control" id="api_category_commission" name="api_category_commission" placeholder="Digite a comissão em porcentagem" value="{{ isset($result->api_category_commission) ? $result->api_category_commission : '' }}" step="0.01" min="0">
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label for="category_id" class="col-sm-12">Categoria do Sistema (opcional):</label>
            <div class="col-sm-12">
                <input type="number" class="form-control" id="category_id" name="category_id" placeholder="ID da categoria do sistema" value="{{ isset($result->category_id) ? $result->category_id : '' }}">
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label for="parent_id" class="col-sm-12">Categoria Pai (opcional):</label>
            <div class="col-sm-12">
                <input type="number" class="form-control" id="parent_id" name="parent_id" placeholder="ID da categoria pai" value="{{ isset($result->parent_id) ? $result->parent_id : '' }}">
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-integration-categories-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form> 
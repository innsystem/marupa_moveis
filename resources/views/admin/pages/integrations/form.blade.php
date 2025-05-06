<form id="form-request-integrations">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="type" class="col-sm-12">Tipo de Integração:</label>
            <div class="col-sm-12">
                <select id="type" name="type" class="form-control">
                    <option value="analytics">Análise</option>
                    <option value="storage">Armazenamento</option>
                    <option value="calendar">Calendários</option>
                    <option value="communication">Comunicação</option>
                    <option value="crm">CRM</option>
                    <option value="ecommerce">E-commerce</option>
                    <option value="finance">Finanças</option>
                    <option value="payments">Pagamentos</option>
                    <option value="projects">Projetos</option>
                    <option value="utilities">Utilitários</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome da Extensão:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o Nome da Extensão" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>
        <div class="form-group mb-3 d-none">
            <label for="slug" class="col-sm-12">Nome Amigável:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="slug" name="slug" placeholder="Digite o Nome Amigável" value="{{ isset($result->slug) ? $result->slug : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="col-sm-12">Descrição da Extensão:</label>
            <div class="col-sm-12">
                <textarea cols="2" rows="2" class="form-control" id="description" name="description" placeholder="Digite uma descrição...">{{ isset($result->description) ? $result->description : '' }}</textarea>
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
        <button type="button" class="btn btn-success button-integrations-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
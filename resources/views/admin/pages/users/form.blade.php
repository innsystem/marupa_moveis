<form id="form-request-users">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="user_group_id" class="col-sm-12">Grupo de Permissões:</label>
            <div class="col-sm-12">
                <select name="user_group_id" id="user_group_id" class="form-select">
                    <option value="" selected disabled>Selecione um Grupo de Permissões</option>
                    @foreach($user_groups as $user_group)
                    @if($user_group->name !== 'Developer' || Auth::user()->group->name === 'Developer')
                    <option value="{{ $user_group->id }}" {{ isset($result->user_group_id) && $result->user_group_id == $user_group->id ? 'selected' : '' }}>{{ $user_group->name }}</option>
                    @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome do Usuário:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o name" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="email" class="col-sm-12">E-mail de Acesso:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="email" name="email" placeholder="Digite o email" value="{{ isset($result->email) ? $result->email : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="password" class="col-sm-12">Senha de Acesso:</label>
            <div class="col-sm-12">
                <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha de acesso" value="">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="document" class="col-sm-12">Documento CPF/CNPJ:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control mask-document" id="document" name="document" placeholder="Digite o Documento CPF/CNPJ" value="{{ isset($result->document) ? $result->document : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="phone" class="col-sm-12">Telefone:</label>
            <div class="col-sm-12">
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Digite o Telefone" value="{{ isset($result->phone) ? $result->phone : '' }}">
                <input type="hidden" id="ddi" name="ddi" value="{{ isset($result->ddi) ? $result->ddi : '+55' }}">
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-users-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
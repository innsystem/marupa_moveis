<form id="form-request-user-groups">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o Nome" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>

        <div class="form-group mb-3">
            <label for="permissions" class="col-sm-12">Permissões:</label>
            <div class="col-sm-12">
                @foreach ($permissions as $type => $typePermissions)
                <div class="mb-3">
                    <div class="d-flex justify-content-start align-items-center gap-1">
                        <h5 class="mb-0">{{ ucfirst($type) }}</h5>
                        <button type="button"
                            class="btn btn-sm btn-link fs-7 p-1 toggle-group"
                            data-group="{{ $type }}">
                            Selecionar todos
                        </button>
                    </div>
                    <hr>
                    @foreach ($typePermissions as $permission)
                    <div class="form-check">
                        <input type="checkbox"
                            class="form-check-input {{ $type }}-checkbox cursor-pointer"
                            name="permissions[]"
                            value="{{ $permission->id }}"
                            id="permission_{{ $permission->id }}"
                            {{ in_array($permission->id, $groupPermissions) ? 'checked' : '' }}>
                        <label class="form-check-label cursor-pointer" for="permission_{{ $permission->id }}">
                            {{ $permission->title }}
                        </label>
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-user-groups-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
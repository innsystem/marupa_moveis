<form id="form-request-statuses">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="type" class="col-sm-12">Tipo:</label>
            <div class="col-sm-12">
                <select name="type" id="type" class="form-control">
                    <option value="default" @if (isset($result->type) && $result->type == 'default') selected @endif>Padrão</option>
                    <option value="general" @if (isset($result->type) && $result->type == 'general') selected @endif>Geral</option>
                    <option value="payments" @if (isset($result->type) && $result->type == 'payments') selected @endif>Pagamentos</option>
                    <option value="sales" @if (isset($result->type) && $result->type == 'sales') selected @endif>Vendas/Produtos</option>
                </select>
            </div>
        </div><!-- form-group -->

        <div class="form-group mb-3">
            <label for="name" class="col-sm-12">Nome:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="name" name="name" placeholder="Digite o nome (ex: ativo, desativado)" value="{{ isset($result->name) ? $result->name : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="col-sm-12">Description:</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="description" name="description" placeholder="Digite uma explicação sobre o status">{{ isset($result->description) ? $result->description : '' }}</textarea>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="color" class="col-sm-12">Cor de Destaque:</label>
            <div class="col-sm-12">
                <select name="color" id="color" class="form-control">
                    <option value="bg-success" @if (isset($result->color) && $result->color == 'bg-success') selected @endif>Verde</option>
                    <option value="bg-warning" @if (isset($result->color) && $result->color == 'bg-warning') selected @endif>Amarelo</option>
                    <option value="bg-danger" @if (isset($result->color) && $result->color == 'bg-danger') selected @endif>Vermelho</option>
                    <option value="bg-info" @if (isset($result->color) && $result->color == 'bg-info') selected @endif>Azul</option>
                    <option value="bg-primary" @if (isset($result->color) && $result->color == 'bg-primary') selected @endif>Primária</option>
                    <option value="bg-secondary" @if (isset($result->color) && $result->color == 'bg-secondary') selected @endif>Secondária</option>
                </select>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="icon" class="col-sm-12">Icone:</label>
            <div class="col-sm-12">
                <div class="form-group mb-2">
                    @foreach ([
                    'fa fa-check' => 'fa fa-check',
                    'fa fa-ban' => 'fa fa-ban',
                    'fa fa-times' => 'fa fa-times',
                    'fa fa-lock' => 'fa fa-lock',
                    'fa fa-pause' => 'fa fa-pause',
                    'fa fa-hourglass-half' => 'fa fa-hourglass-half',
                    'fa fa-trash' => 'fa fa-trash',
                    'fa fa-user-edit' => 'fa fa-user-edit',
                    'fa fa-folder-open' => 'fa fa-folder-open',
                    'fa fa-spinner' => 'fa fa-spinner',
                    'fa fa-clock' => 'fa fa-clock',
                    'fa fa-check-circle' => 'fa fa-check-circle',
                    'fa fa-times-circle' => 'fa fa-times-circle',
                    'fa fa-undo' => 'fa fa-undo',
                    'fa fa-wifi' => 'fa fa-wifi',
                    'fa fa-plug' => 'fa fa-plug',
                    'fa fa-user-lock' => 'fa fa-user-lock',
                    'fa fa-user-times' => 'fa fa-user-times',
                    'fa fa-key' => 'fa fa-key',
                    'fa fa-envelope' => 'fa fa-envelope',
                    'fa fa-box' => 'fa fa-box',
                    'fa fa-box-open' => 'fa fa-box-open',
                    'fa fa-layer-group' => 'fa fa-layer-group',
                    'fa fa-calendar-check' => 'fa fa-calendar-check',
                    'fa fa-cogs' => 'fa fa-cogs',
                    'fa fa-truck-loading' => 'fa fa-truck-loading',
                    'fa fa-truck' => 'fa fa-truck',
                    'fa fa-shipping-fast' => 'fa fa-shipping-fast',
                    'fa fa-credit-card' => 'fa fa-credit-card',
                    'fa fa-exclamation-triangle' => 'fa fa-exclamation-triangle',
                    'fa fa-percent' => 'fa fa-percent'
                    ] as $value => $label)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="icon" id="{{ $value }}" value="{{ $value }}" @if (isset($result->icon) && $result->icon == $value) checked @endif>
                        <label class="form-check-label" for="{{ $value }}">
                            <i class="{!! $label !!}"></i> {!! $label !!}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-statuses-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
    </div>
</form>
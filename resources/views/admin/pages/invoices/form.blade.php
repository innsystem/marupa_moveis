<form id="form-request-invoices">
    <div class="modal-body">
        @if(Route::currentRouteName() != 'admin.invoices.edit')
        <div class="form-group mb-3">
            <label for="user_id" class="col-sm-12">Cliente:</label>
            <div class="col-sm-12">
                <select name="user_id" id="user_id" class="form-select">
                    <option value="" selected disabled>Selecione um usuário</option>
                    @foreach($users as $user)
                    <option value="{{$user->id}}" @if (isset($result->user_id) && $result->user_id == $user->id) selected @endif>{{$user->name}} ({{$user->email}})</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif
        <div class="row">
            <div class="col-12 col-md-7 form-group mb-3">
                <label for="integration_id" class="col-sm-12">Meio de Pagamento:</label>
                <div class="col-sm-12">
                    <select name="integration_id" id="integration_id" class="form-select">
                        @foreach($integrations as $integration)
                        <option value="{{$integration->id}}" @if (isset($result->integration_id) && $result->integration_id == $integration->id) selected @endif>{{$integration->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-md-5 form-group mb-3">
                <label for="method_type" class="col-sm-12">Método:</label>
                <div class="col-sm-12">
                    <select name="method_type" id="method_type" class="form-select">
                        <option value="pix" @if (isset($result->method_type) && $result->method_type == 'pix') selected @endif>PIX</option>
                        <option value="boleto" @if (isset($result->method_type) && $result->method_type == 'boleto') selected @endif>Boleto</option>
                        <option value="credit_card" @if (isset($result->method_type) && $result->method_type == 'credit_card') selected @endif>Cartão de Crédito</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="form-group mb-3">
                    <label for="due_at" class="col-sm-12">Data de Vencimento:</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" id="due_at" name="due_at" placeholder="Data de Vencimento" value="{{ isset($result->due_at) ? $result->due_at : \Carbon\Carbon::now()->format('d/m/Y') }}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="form-group mb-3">
                    <label for="status" class="col-sm-12">Status da Fatura:</label>
                    <div class="col-sm-12">
                        <select name="status" id="status" class="form-select">
                            @foreach($statuses as $status)
                            <option value="{{$status->id}}" @if (isset($result->status) && $result->status == $status->id) selected @endif>{{$status->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div><!-- row -->
        <hr>
        <h5>Itens da Fatura</h5>
        <div class="table-responsive">
            <table class="table table-sm table-centered table-hover table-bordered border-light fs-7">
                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th>Qtde.</th>
                        <th>Valor Un.</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="invoice-items">
                    @if(isset($result->items))
                    @foreach($result->items as $key => $item)
                    <tr>
                        <td><input type="text" name="items[{{$key}}][description]" class="form-control" placeholder="Descrição do item" value="{{$item->description}}" required></td>
                        <td class="p-1"><input type="tel" name="items[{{$key}}][quantity]" class="form-control quantity" style="width:62px;" value="{{$item->quantity}}" required></td>
                        <td class="p-1"><input type="text" name="items[{{$key}}][price_unit]" class="form-control price_unit mask-money" style="width:90px;" value="{{$item->price_unit}}" required></td>
                        <td class="p-1"><input type="text" name="items[{{$key}}][price_total]" class="form-control price_total mask-money" style="width:100px;" value="{{$item->price_total}}" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm fs-7 p-1 button-invoices-remove-item"><i class="fa fa-times"></i></button></td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td><input type="text" name="items[0][description]" class="form-control" placeholder="Descrição do item" required></td>
                        <td class="p-1"><input type="tel" name="items[0][quantity]" class="form-control quantity" style="width:62px;" value="1" required></td>
                        <td class="p-1"><input type="text" name="items[0][price_unit]" class="form-control price_unit mask-money" style="width:90px;" required></td>
                        <td class="p-1"><input type="text" name="items[0][price_total]" class="form-control price_total mask-money" style="width:100px;" readonly></td>
                        <td><button type="button" class="btn btn-danger btn-sm fs-7 p-1 button-invoices-remove-item"><i class="fa fa-times"></i></button></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <button type="button" class="btn btn-primary p-0 px-1 fs-7 button-invoices-add-item">Adicionar Item</button>

    </div>
    <hr>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-invoices-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
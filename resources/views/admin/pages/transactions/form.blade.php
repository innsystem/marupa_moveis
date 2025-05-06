<form id="form-request-transactions">
    <div class="modal-body">
        <div class="d-flex flex-wrap justify-content-between gap-3">
            <div class="col form-group mb-3">
                <label for="type" class="col-sm-12">Tipo de Entrada:</label>
                <div class="col-sm-12">
                    <select name="type" id="type" class="form-select">
                        <option value="income" @if (isset($result->type) && $result->type == 'income') selected @endif>Entrada</option>
                        <option value="expense" @if (isset($result->type) && $result->type == 'expense') selected @endif>Despesa</option>
                    </select>
                </div>
            </div>
            <div class="col form-group mb-3">
                <label for="amount" class="col-sm-12">Valor Total:</label>
                <div class="col-sm-12">
                    <div class="input-group flex-nowrap">
                        <span class="input-group-text">R$</span>
                        <input type="tel" class="form-control mask-money" id="amount" name="amount" placeholder="Digite o valor total" value="{{ isset($result->amount) ? $result->amount : '' }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="col-sm-12">Descrição:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="description" name="description" placeholder="Digite uma descrição da transação" value="{{ isset($result->description) ? $result->description : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="date" class="col-sm-12">Data da Transação:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="date" name="date" placeholder="Digite a data da transação" value="{{ isset($result->date) ? $result->date : \Carbon\Carbon::now()->format('d/m/Y H:i') }}">
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-transactions-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
<form id="form-request-bank_account">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="bank_name" class="col-sm-12">Bank name:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Digite o bank_name" value="{{ isset($result->bank_name) ? $result->bank_name : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="saldo" class="col-sm-12">Saldo:</label>
            <div class="col-sm-12">
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control mask-money" id="saldo" name="saldo" placeholder="Digite o saldo" value="{{ isset($result->saldo) ? $result->saldo : '' }}">
                </div>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="account_type" class="col-sm-12">Tipo de Conta:</label>
            <div class="col-sm-12">
                <select class="form-select" id="account_type" name="account_type">
                    <option value="Conta Pessoal" {{ (isset($result->account_type) && $result->account_type == 'Conta Pessoal') ? 'selected' : '' }}>Conta Pessoal</option>
                    <option value="Conta Investimento" {{ (isset($result->account_type) && $result->account_type == 'Conta Investimento') ? 'selected' : '' }}>Conta Investimento</option>
                    <option value="Carteira Cripto" {{ (isset($result->account_type) && $result->account_type == 'Carteira Cripto') ? 'selected' : '' }}>Carteira Cripto</option>
                    <option value="Caixinha de Viagem" {{ (isset($result->account_type) && $result->account_type == 'Caixinha de Viagem') ? 'selected' : '' }}>Caixinha de Viagem</option>
                    <option value="Reserva" {{ (isset($result->account_type) && $result->account_type == 'Reserva') ? 'selected' : '' }}>Reserva</option>
                </select>
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-bank_account-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>

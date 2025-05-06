<form id="form-request-integrations">
    <div class="modal-body">
        <div class="mb-4">
            <img src="{{ asset('/galerias/icons/payments/mercadopago.png') }}" alt="Mercado Pago" width="220" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Mercado Pago">
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                    <h5>Como obter o Access Token?</h5>
                    <div class="p-1 mb-3">
                        <p class="mb-1">1° Acesse sua conta no <a href="https://www.mercadopago.com.br/developers/panel/app" target="_Blank">Mercado Pago</a>.</p>
                        <p class="mb-1">2° Acesse a área de Desenvolvedores através do link <a href="https://www.mercadopago.com.br/developers/panel/app" target="_blank">Developers Mercado Pago</a></p>
                        <p class="mb-1">3° Crie uma nova Aplicação</p>
                        <p class="mb-1">4° Siga as instruções</p>
                        <p class="mb-1">5° Acesse as Credenciais de Produção</p>
                        <p class="mb-1">6° Copie o <b>access-token</b></p>
                        <p class="mb-1">7° Cole no campo, pronto!.</p>
                    </div>

                    <h5>Como visualizar as taxas?</h5>
                    <p>As taxas podem ser verificadas na sua conta Mercado Pago através do link <a href="https://www.mercadopago.com.br/costs-section#from-section=menu" target="_Blank">Seu Negócio → Taxas e Parcelas</a>.</p>
                </div>
            </div>

            <div class="col-12 col-md-6 ps-3">
                <div class="mb-3">
                    <label for="access_token" class="form-label">Access Token:</label>
                    <input type="text" class="form-control" id="access_token" name="access_token" placeholder="Digite o Access Token" value="{{ $result->settings['access_token'] ?? '' }}">
                </div>

                <!-- Grupo PIX -->
                <div class="mb-3">
                    <label class="form-label">PIX - Configurações</label>
                    <div class="input-group">
                        <select class="form-select" name="status_pix" id="status_pix">
                            <option value="1" {{ ($result->settings['status_pix'] ?? false) ? 'selected' : '' }}>Habilitado</option>
                            <option value="0" {{ ($result->settings['status_pix'] ?? false) ? '' : 'selected' }}>Desabilitado</option>
                        </select>
                        <span class="input-group-text">Taxa</span>
                        <input type="number" step="0.01" class="form-control" id="fee_pix" name="fee_pix" placeholder="Taxa PIX" value="{{ $result->settings['fee_pix'] ?? '' }}">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <!-- Grupo Boleto -->
                <div class="mb-3">
                    <label class="form-label">Boleto - Configurações</label>
                    <div class="input-group">
                        <select class="form-select" name="status_boleto" id="status_boleto">
                            <option value="1" {{ ($result->settings['status_boleto'] ?? false) ? 'selected' : '' }}>Habilitado</option>
                            <option value="0" {{ ($result->settings['status_boleto'] ?? false) ? '' : 'selected' }}>Desabilitado</option>
                        </select>
                        <span class="input-group-text">Taxa</span>
                        <input type="number" step="0.01" class="form-control" id="fee_boleto" name="fee_boleto" placeholder="Taxa Boleto" value="{{ $result->settings['fee_boleto'] ?? '' }}">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <!-- Grupo Cartão de Crédito -->
                <div class="mb-3">
                    <label class="form-label">Cartão de Crédito - Configurações</label>
                    <div class="input-group">
                        <select class="form-select" name="status_credit_card" id="status_credit_card">
                            <option value="1" {{ ($result->settings['status_credit_card'] ?? false) ? 'selected' : '' }}>Habilitado</option>
                            <option value="0" {{ ($result->settings['status_credit_card'] ?? false) ? '' : 'selected' }}>Desabilitado</option>
                        </select>
                        <span class="input-group-text">Taxa</span>
                        <input type="number" step="0.01" class="form-control" id="fee_credit_card" name="fee_credit_card" placeholder="Taxa Cartão" value="{{ $result->settings['fee_credit_card'] ?? '' }}">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <!-- Parcelamento -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status:</label>
                        <select name="status" id="status" class="form-select">
                            @foreach($statuses as $status)
                            <option value="{{$status->id}}" @if (isset($result->status) && $result->status == $status->id) selected @endif>{{$status->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fee_installment" class="form-label">Taxa Parcelamento (%):</label>
                        <input type="number" step="0.01" class="form-control" id="fee_installment" name="fee_installment" placeholder="Taxa Parcelamento" value="{{ $result->settings['fee_installment'] ?? '' }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="max_installments" class="form-label">Máximo de Parcelas:</label>
                        <input type="number" class="form-control" id="max_installments" name="max_installments" placeholder="Parcelas Máx." value="{{ $result->settings['max_installments'] ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="installments_free" class="form-label">Parcelas sem Juros:</label>
                        <input type="number" class="form-control" id="installments_free" name="installments_free" placeholder="Sem Juros" value="{{ $result->settings['installments_free'] ?? '' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="webhook_url" class="form-label">WebHook URL:</label>
                    <input type="text" class="form-control" id="webhook_url" name="webhook_url" placeholder="Digite o WebHook URL" value="{{ $result->settings['webhook_url'] ?? '' }}">
                </div>
            </div>
        </div><!-- row -->
    </div><!-- modal-body -->
    <hr>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-integrations-save">Salvar Alterações</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>
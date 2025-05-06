<form id="form-request-services">
    <div class="modal-body">
        <div class="form-group mb-3">
            <label for="title" class="col-sm-12">Título do Serviço:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="title" name="title" placeholder="Digite o título do serviço" value="{{ isset($result->title) ? $result->title : '' }}">
            </div>
        </div>
        <div class="form-group mb-3 d-none">
            <label for="slug" class="col-sm-12">URL Amigável:</label>
            <div class="col-sm-12">
                <input type="text" class="form-control" id="slug" name="slug" placeholder="Digite o URL Amigável" value="{{ isset($result->slug) ? $result->slug : '' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="description" class="col-sm-12">Descrição do Serviço:</label>
            <div class="col-sm-12">
                <textarea class="form-control" id="description" name="description" placeholder="Digite o description" style="display: none;">{{ isset($result->description) ? $result->description : '' }}</textarea>
                <div class="snow-editor" style="height: 250px;"></div>
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="image" class="col-sm-12">Imagem:</label>
            <div class="col-sm-12">
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                @if(isset($result->image) && $result->image)
                    <img src="{{ asset('storage/' . $result->image) }}" alt="Imagem do Serviço" class="mt-2" style="max-width: 200px;">
                @endif
            </div>
        </div>
        
        <!-- Tipo de serviço (recorrente ou pagamento único) -->
        <div class="form-group mb-3">
            <label for="is_recurring" class="col-sm-12">Tipo de Serviço:</label>
            <div class="col-sm-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_recurring" id="is_recurring_0" value="0" {{ !isset($result->is_recurring) || !$result->is_recurring ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_recurring_0">Pagamento Único</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_recurring" id="is_recurring_1" value="1" {{ isset($result->is_recurring) && $result->is_recurring ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_recurring_1">Assinatura Recorrente</label>
                </div>
            </div>
        </div>
        
        <!-- Preço de pagamento único -->
        <div class="form-group mb-3 single-payment-container" {{ isset($result->is_recurring) && $result->is_recurring ? 'style="display:none;"' : '' }}>
            <label for="single_payment_price" class="col-sm-12">Preço (Pagamento Único):</label>
            <div class="col-sm-12">
                <div class="input-group">
                    <span class="input-group-text">R$</span>
                    <input type="text" class="form-control mask-money" id="single_payment_price" name="single_payment_price" 
                        placeholder="0,00" value="{{ isset($result->single_payment_price) ? $result->single_payment_price : '' }}">
                </div>
            </div>
        </div>
        
        <!-- Preços de recorrência -->
        <div class="recurring-payment-container" {{ !isset($result->is_recurring) || !$result->is_recurring ? 'style="display:none;"' : '' }}>
            <h5 class="mt-4">Preços para Assinatura</h5>
            
            <div class="form-group mb-3">
                <label for="monthly_price" class="col-sm-12">Preço Mensal:</label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control mask-money" id="monthly_price" name="monthly_price" 
                            placeholder="0,00" value="{{ isset($result->monthly_price) ? $result->monthly_price : '' }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="quarterly_price" class="col-sm-12">Preço Trimestral:</label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control mask-money" id="quarterly_price" name="quarterly_price" 
                            placeholder="0,00" value="{{ isset($result->quarterly_price) ? $result->quarterly_price : '' }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="semiannual_price" class="col-sm-12">Preço Semestral:</label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control mask-money" id="semiannual_price" name="semiannual_price" 
                            placeholder="0,00" value="{{ isset($result->semiannual_price) ? $result->semiannual_price : '' }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="annual_price" class="col-sm-12">Preço Anual:</label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control mask-money" id="annual_price" name="annual_price" 
                            placeholder="0,00" value="{{ isset($result->annual_price) ? $result->annual_price : '' }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label for="biennial_price" class="col-sm-12">Preço Bienal:</label>
                <div class="col-sm-12">
                    <div class="input-group">
                        <span class="input-group-text">R$</span>
                        <input type="text" class="form-control mask-money" id="biennial_price" name="biennial_price" 
                            placeholder="0,00" value="{{ isset($result->biennial_price) ? $result->biennial_price : '' }}">
                    </div>
                </div>
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
        <div class="form-group mb-3">
            <label for="sort_order" class="col-sm-12">Posição de Exibição:</label>
            <div class="col-sm-12">
                <input type="number" class="form-control" id="sort_order" name="sort_order" placeholder="Digite o sort_order" value="{{ isset($result->sort_order) ? $result->sort_order : '0' }}">
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-services-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>

<script>
    // Mostrar/ocultar os campos de preço de acordo com o tipo de serviço
    $('input[name="is_recurring"]').on('change', function() {
        if ($(this).val() == '1') {
            $('.single-payment-container').hide();
            $('.recurring-payment-container').show();
        } else {
            $('.single-payment-container').show();
            $('.recurring-payment-container').hide();
        }
    });
</script>
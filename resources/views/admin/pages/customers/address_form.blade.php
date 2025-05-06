<form id="form-request-address">
    @csrf
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="zipcode" class="form-label">CEP</label>
            <input type="text" class="form-control" id="zipcode" name="zipcode" maxlength="9" value="{{ $address->zipcode ?? '' }}" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-9">
            <label for="street" class="form-label">Rua</label>
            <input type="text" class="form-control" id="street" name="street" value="{{ $address->street ?? '' }}" required>
        </div>
        <div class="col-md-3">
            <label for="number" class="form-label">Número</label>
            <input type="text" class="form-control" id="number" name="number" value="{{ $address->number ?? '' }}" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="complement" class="form-label">Complemento</label>
            <input type="text" class="form-control" id="complement" name="complement" value="{{ $address->complement ?? '' }}">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="district" class="form-label">Bairro</label>
            <input type="text" class="form-control" id="district" name="district" value="{{ $address->district ?? '' }}" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-9">
            <label for="city" class="form-label">Cidade</label>
            <input type="text" class="form-control" id="city" name="city" value="{{ $address->city ?? '' }}" required>
        </div>
        <div class="col-md-3">
            <label for="state" class="form-label">Estado</label>
            <input type="text" class="form-control" id="state" name="state" maxlength="2" value="{{ $address->state ?? '' }}" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-12 text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
            @if(isset($address))
            <button type="button" class="btn btn-primary button-address-save" data-type="update" data-customer-id="{{ $customerId }}" data-address-id="{{ $address->id }}">Atualizar</button>
            @else
            <button type="button" class="btn btn-primary button-address-save" data-type="store" data-customer-id="{{ $customerId }}">Salvar</button>
            @endif
        </div>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Inicializa máscara para o CEP
        if ($.fn.mask) {
            $('#zipcode').mask('00000-000');
        } else {
            // Implementação manual simples para formatar o CEP
            $('#zipcode').on('input', function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep.length > 5) {
                    cep = cep.substring(0, 5) + '-' + cep.substring(5, 8);
                }
                $(this).val(cep);
            });
        }

        // Consulta CEP nas APIs
        $('#zipcode').on('blur', function() {
            var cep = $(this).val().replace(/\D/g, '');

            if (cep.length === 8) {
                // Limpa classes de validação
                $(this).removeClass('is-valid is-invalid');

                // Limpa os campos do endereço para nova consulta se o CEP for diferente
                if ($(this).data('last-cep') !== cep) {
                    $('#street, #district, #city, #state').val('');
                    $(this).data('last-cep', cep);
                } else {
                    // Se for o mesmo CEP da última consulta, não faz nada
                    return;
                }

                // Desabilita os campos enquanto consulta
                $('#street, #district, #city, #state').prop('disabled', true);

                // Mostra indicador de carregamento
                let loadingSpinner = $('<i class="fas fa-spinner fa-spin ms-2 cep-spinner"></i>');
                $('.cep-spinner').remove(); // Remove spinner anterior se existir
                $(this).after(loadingSpinner);

                // Primeiro tenta a BrasilAPI
                consultarBrasilAPI(cep, loadingSpinner);
            }
        });

        // Função para consultar a BrasilAPI
        function consultarBrasilAPI(cep, loadingSpinner) {
            $.ajax({
                url: `https://brasilapi.com.br/api/cep/v1/${cep}`,
                dataType: 'json',
                timeout: 3000, // 3 segundos de timeout
                success: function(data) {
                    // Remove o spinner
                    loadingSpinner.remove();

                    // Preenche os campos com os dados retornados
                    $('#street').val(data.street);
                    $('#district').val(data.neighborhood);
                    $('#city').val(data.city);
                    $('#state').val(data.state);

                    // Habilita os campos
                    $('#street, #district, #city, #state').prop('disabled', false);

                    // Foca no campo de número para facilitar o preenchimento
                    $('#number').focus();

                    // Adiciona classe de validação
                    $('#zipcode').addClass('is-valid');
                },
                error: function() {
                    // Se falhar, tenta a ViaCEP como alternativa
                    consultarViaCEP(cep, loadingSpinner);
                }
            });
        }

        // Função para consultar a ViaCEP (fallback)
        function consultarViaCEP(cep, loadingSpinner) {
            $.ajax({
                url: `https://viacep.com.br/ws/${cep}/json/`,
                dataType: 'json',
                timeout: 3000, // 3 segundos de timeout
                success: function(data) {
                    // Remove o spinner
                    loadingSpinner.remove();

                    if (!("erro" in data)) {
                        // Preenche os campos com os dados retornados
                        $('#street').val(data.logradouro);
                        $('#district').val(data.bairro);
                        $('#city').val(data.localidade);
                        $('#state').val(data.uf);

                        // Habilita os campos
                        $('#street, #district, #city, #state').prop('disabled', false);

                        // Foca no campo de número para facilitar o preenchimento
                        $('#number').focus();

                        // Adiciona classe de validação
                        $('#zipcode').addClass('is-valid');
                    } else {
                        // CEP não encontrado
                        $('#zipcode').addClass('is-invalid');
                        $('#street, #district, #city, #state').prop('disabled', false);
                    }
                },
                error: function() {
                    // Remove o spinner
                    loadingSpinner.remove();

                    // Ambas as APIs falharam
                    $('#zipcode').addClass('is-invalid');
                    $('#street, #district, #city, #state').prop('disabled', false);
                }
            });
        }
    });
</script> 
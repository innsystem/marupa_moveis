<form id="form-request-customers">
    <div class="modal-body">
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
                <input type="tel" class="form-control" id="phone" name="phone" value="{{ isset($result->phone) ? $result->phone : '' }}" data-ddi="{{ isset($result->ddi) ? $result->ddi : '+55' }}">
                <input type="hidden" id="ddi" name="ddi" value="{{ isset($result->ddi) ? $result->ddi : '+55' }}">
            </div>
        </div>
        <div class="form-group mb-3">
            <label for="payment_default" class="col-sm-12">Método de Pagamento Padrão:</label>
            <div class="col-sm-12">
                <select class="form-control" id="payment_default" name="payment_default">
                    <option value="pix" {{ isset($preferences) && $preferences->payment_default == 'pix' ? 'selected' : '' }}>PIX</option>
                    <option value="boleto" {{ isset($preferences) && $preferences->payment_default == 'boleto' ? 'selected' : '' }}>Boleto</option>
                    <option value="credit_card" {{ isset($preferences) && $preferences->payment_default == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                </select>
            </div>
        </div>
    </div>
    <div class="bg-gray modal-footer justify-content-between">
        <button type="button" class="btn btn-success button-customers-save"><i class="fa fa-check"></i> Salvar</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="offcanvas" aria-label="Fechar">Fechar</button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.querySelector("#phone");
    var ddiInput = document.querySelector("#ddi");
    
    var iti = window.intlTelInput(input, {
        initialCountry: "br",
        separateDialCode: true,
        countrySearch: true,
        autoPlaceholder: "aggressive",
        formatOnDisplay: true,
        nationalMode: false,
        loadUtils: () => import("{{ asset('/plugins/intl-tel-input/js/utils.js') }}")
    });
    
    // Função para atualizar o input hidden com o DDI
    function atualizarDDI() {
        var dialCode = iti.getSelectedCountryData().dialCode;
        ddiInput.value = '+' + dialCode;
    }
    
    // Atualizar o DDI quando o país mudar
    input.addEventListener('countrychange', atualizarDDI);
    
    // Para edição: Definir o país com base no DDI salvo
    setTimeout(function() {
        if (ddiInput.value) {
            try {
                // Remover o '+' do início
                var savedDialCode = ddiInput.value.replace('+', '');
                
                // Função para inicializar o telefone com o DDI salvo
                if (iti.loadUtils) {
                    iti.promise.then(function() {
                        // Quando os utilitários são carregados, podemos definir o número completo
                        var fullNumber = ddiInput.value + input.value;
                        iti.setNumber(fullNumber);
                    });
                } else {
                    // Se loadUtils não estiver disponível, fazer o melhor possível
                    // Percorrer todos os países e encontrar o que tem o código de discagem igual
                    var allCountries = iti.getCountryData();
                    for (var i = 0; i < allCountries.length; i++) {
                        if (allCountries[i].dialCode === savedDialCode) {
                            iti.setCountry(allCountries[i].iso2);
                            break;
                        }
                    }
                }
            } catch (e) {
                console.log("Erro ao definir país:", e);
            }
        }
        
        // Atualizar o DDI baseado no país selecionado
        atualizarDDI();
    }, 500);
    
    // Preparar para a submissão AJAX
    $(document).on('click', '.button-customers-save', function() {
        prepararTelefoneParaEnvio();
    });
    
    // Antes de enviar o formulário, garantir que o DDI esteja atualizado
    document.querySelector('#form-request-customers').addEventListener('submit', function(e) {
        prepararTelefoneParaEnvio();
    });
    
    // Função para preparar o telefone para envio
    function prepararTelefoneParaEnvio() {
        // Atualizar o DDI
        atualizarDDI();
        
        // Utils carregado?
        if (iti.isValidNumber && iti.getNumber) {
            // Obter o número completo
            var numeroCompleto = iti.getNumber();
            
            // Remover o DDI do número para salvar apenas o número local
            var dialCode = '+' + iti.getSelectedCountryData().dialCode;
            var phoneWithoutCode = numeroCompleto.replace(dialCode, '');
            
            // Atualizar os inputs
            input.value = phoneWithoutCode.trim();
            ddiInput.value = dialCode;
        } else {
            // Fallback caso o utils não esteja carregado
            var dialCode = '+' + iti.getSelectedCountryData().dialCode;
            ddiInput.value = dialCode;
        }
    }
});
</script>
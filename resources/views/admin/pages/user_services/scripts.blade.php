<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/flatpickr.min.js') }}"></script>
<script src="{{ asset('/tpl_dashboard/vendor/flatpickr/l10n/pt.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#date_range").flatpickr({
            "mode": "range",
            "dateFormat": "d/m/Y",
            "locale": "pt",
            "firstDayOfWeek": 1,
        });
    });

    $(document).on("click", "#button-user-services-filters", function(e) {
        e.preventDefault();
        $("#filter-form").submit();
    });

    function initMasks() {
        $("#start_date").flatpickr({
            "dateFormat": "d/m/Y",
            "locale": "pt",
            "firstDayOfWeek": 1,
        });
        $("#end_date").flatpickr({
            "dateFormat": "d/m/Y",
            "locale": "pt",
            "firstDayOfWeek": 1,
        });
        $(".mask-money").mask('00000.00', {
            reverse: true,
            placeholder: "0.00"
        });
        updatePriceOptions();
    }

    // Adiciona evento para adicionar nova linha de metadata
    $(document).on('click', '#add_metadata', function() {
        var newRow = `
                <div class="row mb-2 metadata-row">
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_title[]" placeholder="Título">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_value[]" placeholder="Valor">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger btn-sm remove-metadata"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            `;
        $('#metadata_container').append(newRow);
    });

    // Remove linha de metadata
    $(document).on('click', '.remove-metadata', function() {
        $(this).closest('.metadata-row').remove();
    });

    // Adiciona os campos para conta pessoal
    $(document).on('click', '#add_conta_pessoal', function() {
        $('#metadata_container').html('');
        var camposPessoais = [
            {titulo: 'tipo', valor: 'Exemplo: Energia'},
            {titulo: 'fornecedor', valor: 'Exemplo: NET CLARO'},
            {titulo: 'is_expense', valor: 'true'},
        ];
        camposPessoais.forEach(function(campo) {
            var newRow = `
                <div class="row mb-2 metadata-row">
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_title[]" placeholder="" value="${campo.titulo}">
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" name="metadata_value[]" placeholder="${campo.valor}" value="">
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-danger btn-sm remove-metadata"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            `;
            $('#metadata_container').append(newRow);
        });
        Swal.fire({
            title: 'Campos para Conta Pessoal adicionados!',
            text: 'Preencha o "tipo" (ex: Energia, Internet) e o "fornecedor" (ex: CPFL, Vivo) para melhor organização.',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    });
</script> 
$(document).ready(function () {
    var optionsDocument = {
        onKeyPress: function (document, ev, el, op) {
            var masks = ['000.000.000-000', '00.000.000/0000-00'];
            $('.mask-document').mask((document.length > 14) ? masks[1] : masks[0], op);
        }
    }

    if ($('.mask-document').length) {
        $('.mask-document').val().length > 14 ? $('.mask-document').mask('00.000.000/0000-00', optionsDocument) : $('.mask-document').mask('000.000.000-00#', optionsDocument);
    }

    $(".mask-cpf").mask('000.000.000-00');
    $('.mask-cnpj').mask('00.000.000/0000-00');

    var cellMaskBehavior = function (val) {
        return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
    }, cellOptions = {
        onKeyPress: function (val, e, field, options) {
            field.mask(cellMaskBehavior.apply({}, arguments), options);
        }
    };

    $('.mask-phone').mask(cellMaskBehavior, cellOptions);
    $(".mask-zipcode").mask('00000-000', { reverse: true, placeholder: "00000-000" });
    $(".mask-card-number").mask('0000 0000 0000 0000');
    $(".mask-number").mask('000000');
    $(".mask-money").mask('00000.00', { reverse: true, placeholder: "0.00" });

    $(document).on('keyup', 'input[name="zipcode"]', function (e) {
        var input = 'input[name="zipcode"]';
        var cep = $(input).val();

        if (cep.length >= 8) {
            var cep = cep.replace('-', '');
            //$.getScript ("http://cep.republicavirtual.com.br/web_cep.php?formato=javascript&cep="+cep, function(){

            if (cep) {
                $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function (dados) {
                    if (!("erro" in dados)) {
                        $('input[name="address"]').val(dados.logradouro);
                        $('input[name="district"]').val(dados.bairro);
                        $('input[name="city"]').val(unescape(dados.localidade));
                        // $('input[name="state"]').val(unescape(dados.uf));
                        $('select[name="state"] option[value="' + dados.uf + '"]').prop('selected', true);
                        // $('select[name="country"]').find('option[value="br"]').attr('selected', true);
                        $('input[name="address"], input[name="number"], input[name="district"], input[name="city"], input[name="state"], select[name="country"], input[name="latitude"], input[name="longitude"]').parent().show();
                        $('input[name="number"]').focus();
                        $(input).removeClass('is-invalid');
                        $(input).addClass('is-valid');
                    } else {
                        $(input).addClass('is-invalid');
                    }
                });
            }
        }
    });

    function CPF() {
        "user_strict";

        function r(r) {
            for (var t = null, n = 0; 9 > n; ++n) t += r.toString().charAt(n) * (10 - n);
            var i = t % 11;
            return i = 2 > i ? 0 : 11 - i
        }

        function t(r) {
            for (var t = null, n = 0; 10 > n; ++n) t += r.toString().charAt(n) * (11 - n);
            var i = t % 11;
            return i = 2 > i ? 0 : 11 - i
        }
        var n = "CPF Inválido",
            i = "CPF Válido";
        this.gera = function () {
            for (var n = "", i = 0; 9 > i; ++i) n += Math.floor(9 * Math.random()) + "";
            var o = r(n),
                a = n + "-" + o + t(n + "" + o);
            return a
        }, this.valida = function (o) {
            for (var a = o.replace(/\D/g, ""), u = a.substring(0, 9), f = a.substring(9, 11), v = 0; 10 > v; v++)
                if ("" + u + f == "" + v + v + v + v + v + v + v + v + v + v + v) return n;
            var c = r(u),
                e = t(u + "" + c);
            return f.toString() === c.toString() + e.toString() ? i : n
        }
    }

    var CPF = new CPF();
    var CPFPost = $('.valid-document-cpf').val();

    let validDocument = false;

    $(document).on('change', '.valid-document-cpf', function (e) {
        if ($(this).val().length <= 14) {
            if (CPF.valida($(this).val()) != 'CPF Válido') {
                $(this).parent().find('.valited-document').html('<p class="fs-7 mb-0 text-danger fw-bold p-1">CPF Inválido</p>');
                $('#button-register').attr('disabled', true);
            } else {
                $(this).parent().find('.valited-document').html('');
                $('#button-register').attr('disabled', false);
            }
        }
    });

    function validarCNPJ(cnpj) {
        cnpj = cnpj.replace(/[^\d]+/g, '');

        if (cnpj.length != 14) {
            return false;
        }

        if (/^(\d)\1+$/.test(cnpj)) {
            return false;
        }

        var tamanho = cnpj.length - 2;
        var numeros = cnpj.substring(0, tamanho);
        var digitos = cnpj.substring(tamanho);
        var soma = 0;
        var pos = tamanho - 7;

        for (var i = tamanho; i >= 1; i--) {
            soma += numeros[tamanho - i] * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        var resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

        if (resultado != digitos.charAt(0)) {
            return false;
        }

        tamanho = tamanho + 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (var i = tamanho; i >= 1; i--) {
            soma += numeros[tamanho - i] * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;

        if (resultado != digitos.charAt(1)) {
            return false;
        }

        return true;
    }

    $(document).on('change', '.valid-document-cnpj', function (e) {
        if (validarCNPJ($(this).val())) {
            $(this).parent().find('.valited-document-cnpj').html('');
            $('#button-register').attr('disabled', false);
        } else {
            $(this).parent().find('.valited-document-cnpj').html('<p class="fs-7 mb-0 text-danger fw-bold p-1">CNPJ Inválido</p>');
            $('#button-register').attr('disabled', true);
        }
    });
});
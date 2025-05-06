@extends('auth.base')

@section('content')
<form id="form-request-login">
    <div class="mb-2">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="seuemail@gmail.com">
    </div>
    <div class="mb-2">
        <label for="password" class="form-label">Senha de Acesso</label>
        <div class="input-group">
            <input type="password" class="form-control" name="password" id="password" placeholder="********">
        </div>
    </div>
    <button type="button" class="btn btn-primary w-100 button-login">Acessar Conta</button>
    <p class="text-muted mt-2">Esqueceu sua senha? <a href="{{route('auth.passwordRecovery')}}">clique aqui</a></p>
</form>
@endsection

@section('pageCSS')
@endsection

@section('pageJS')
<script>
    // Save
    $(document).on('click', '.button-login', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);

        var url = `{{ url('/auth/login/post') }}`;

        var form = $('#form-request-login')[0];
        var formData = new FormData(form);

        $.ajax({
            url: url,
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            method: 'POST',
            beforeSend: function() {
                //disable the submit button
                button.attr("disabled", true);
                button.append('<i class="fa fa-spinner fa-spin ml-3"></i>');
            },
            complete: function() {
                button.prop("disabled", false);
                button.find('.fa-spinner').addClass('d-none');
            },
            success: function(data) {
                let timerInterval
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: data.title,
                    html: 'Você será redirecionado em alguns segundos',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }).then((result) => {
                    /* Read more about handling dismissals below */
                    if (result.dismiss === Swal.DismissReason.timer) {
                        location.href = data.href;
                    }
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        text: 'Validação: ' + xhr.responseJSON,
                        icon: 'warning',
                        showClass: {
                            popup: 'animate_animated animate_wobble'
                        }
                    });
                } else {
                    Swal.fire({
                        text: 'Erro Interno: ' + xhr.responseJSON,
                        icon: 'error',
                        showClass: {
                            popup: 'animate_animated animate_wobble'
                        }
                    });
                }
            }
        });
    });
</script>
@endsection
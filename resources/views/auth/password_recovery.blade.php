@extends('auth.base')

@section('content')
<form id="form-request-password-recovery">
    <div class="mb-2">
        <label for="email" class="form-label">E-mail de Acesso</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="seuemail@gmail.com">
    </div>

    <div class="d-flex flex-wrap justify-content-between gap-2">
        <button class="btn btn-primary button-recovery-password" type="button"> Recuperar Senha </button>
        <a href="{{ url('/auth/login') }}" class="btn btn-outline-primary"> Acessar Minha Conta </a>
    </div>
</form>
@endsection

@section('pageCSS')
@endsection

@section('pageJS')
<script>
    // Save
    $(document).on('click', '.button-recovery-password', function(e) {
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            }
        });

        let button = $(this);

        var url = `{{ url('/auth/recuperar-senha/post') }}`;

        var form = $('#form-request-password-recovery')[0];
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
                    title: data,
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }).then((result) => {
                    /* Read more about handling dismissals below */
                    if (result.dismiss === Swal.DismissReason.timer) {
                        location.href = `{{ url('/auth/redefinir-senha') }}`;
                    }
                });
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    Swal.fire({
                        text: xhr.responseJSON,
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
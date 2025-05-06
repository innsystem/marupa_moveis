<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="utf-8" />
    <title>InnSystem Dashboard</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('/favicon.ico?1') }}" type="image/x-icon">

    <!-- Theme Config Js -->
    <script src="{{ asset('/tpl_dashboard/js/config.js?2') }}"></script>

    <!-- App css -->
    <link href="{{ asset('/tpl_dashboard/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <!-- App css -->
    <link href="{{ asset('/tpl_dashboard/css/custom_template.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons css -->
    <link href="{{ asset('/tpl_dashboard/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Font Awesome -->
    <link href="{{ asset('/plugins/fontawesome/css/all.min.css') }}" rel="stylesheet">

    <!-- SwalFire -->
    <link href="{{ asset('/plugins/sweetalert/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <style>
        body {
            background-color: #2d2d2d;
            font-family: 'Arial', sans-serif;
        }

        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: #373737;
            border-radius: 15px;
            padding: 30px;
            color: #fff;
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }

        .brand-logo {
            display: block;
            margin: 0 auto 20px;
        }

        .form-label {
            font-size: 14px;
            color: #B0B3C5;
        }

        .form-control {
            background-color: #2d2d2d;
            color: #fff;
            border: 1px solid #B0B3C5;
            border-radius: 5px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #37adef;
        }

        .btn-primary {
            background-color: #37adef;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-weight: bold;
            color: #2d2d2d;
        }

        .btn-primary:hover {
            background-color: rgb(36, 155, 224);
        }

        .forgot-password,
        .terms {
            color: #B0B3C5;
            font-size: 12px;
            text-decoration: none;
        }

        .forgot-password:hover,
        .terms:hover {
            color: #37adef;
        }

        .password-strength {
            font-size: 12px;
            color: #37adef;
            margin-top: 5px;
        }
    </style>

    @yield('pageCSS')
</head>

<!-- body start -->

<body>
    <div id="particles-js" style="position: fixed; width: 100vw; height: 100vh; top: 0; left: 0; z-index: 0;"></div>
    <div class="login-container" style="position: relative; z-index: 1;">
        <img src="{{ asset('/marupa_moveis_branco.png') }}" alt="Marupa Móveis" class="brand-logo" style="max-width: 100%;">
        <h4 class="text-center mb-4">Faça seu Acesso</h4>
        @yield('content')
        <div class="text-center fs-7 mt-4">
            <script>
                document.write(new Date().getFullYear())
            </script> © Desenvolvido por <a href="https://innsystem.com.br" target="_Blank" class="fw-bold">InnSystem Inovação em Sistemas</a>
        </div>
    </div>

    <!-- Vendor js -->
    <script src="{{ asset('/tpl_dashboard/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('/tpl_dashboard/js/app.min.js') }}"></script>

    <script src="{{ asset('/plugins/sweetalert/sweetalert2.min.js') }}"></script>


    <script src="{{ asset('/plugins/jquery.mask.js') }}"></script>
    <script src="{{ asset('/plugins/jquery.mask.templates.js') }}"></script>



    @yield('pageJS')
    <script src="{{ asset('/plugins/particles.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS("particles-js", {
                "particles": {
                    "number": {
                        "value": 100,
                        "density": {
                            "enable": true,
                            "value_area": 1000
                        }
                    },
                    "color": {
                        "value": "#ffffff"
                    },
                    "shape": {
                        "type": "edge",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        }
                    },
                    "opacity": {
                        "value": 0.15,
                        "random": false,
                        "anim": {
                            "enable": false,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 3,
                        "random": true
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#1290EF",
                        "opacity": 0.2,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 2,
                        "direction": "none",
                        "random": false,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "grab"
                        },
                        "onclick": {
                            "enable": false
                        },
                        "resize": true
                    },
                    "modes": {
                        "grab": {
                            "distance": 140,
                            "line_linked": {
                                "opacity": 0.5
                            }
                        }
                    }
                },
                "retina_detect": true
            });
        });
    </script>
</body>

</html>
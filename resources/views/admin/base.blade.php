<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="utf-8" />
    <title>@yield('title') | {{ $getSettings['site_name'] }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ $getSettings['favicon'] ? asset('storage/' . $getSettings['favicon']) : asset('favicon.png') }}" type="image/x-icon">

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

    <!-- Plugin css -->
    <link rel="stylesheet" href="{{ asset('/tpl_dashboard/vendor/jquery-toast-plugin/jquery.toast.min.css') }}">

    @yield('pageCSS')
</head>

<!-- body start -->

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <!-- ========== Topbar Start ========== -->
        <div class="navbar-custom">
            <div class="topbar container-fluid">
                <div class="d-flex align-items-center gap-1">

                    <!-- Topbar Brand Logo -->
                    <div class="logo-topbar">
                        <!-- Logo light -->
                        <a href="{{ route('admin.index') }}" class="logo-light">
                            <span class="logo-lg">
                                <img src="{{ asset('/tpl_dashboard/images/logo.png') }}" alt="logo">
                            </span>
                            <span class="logo-sm">
                                <img src="{{ asset('/tpl_dashboard/images/logo-sm.png') }}" alt="small logo">
                            </span>
                        </a>

                        <!-- Logo Dark -->
                        <a href="{{ route('admin.index') }}" class="logo-dark">
                            <span class="logo-lg">
                                <img src="{{ asset('/tpl_dashboard/images/logo-dark.png') }}" alt="dark logo">
                            </span>
                            <span class="logo-sm">
                                <img src="{{ asset('/tpl_dashboard/images/logo-sm.png') }}" alt="small logo">
                            </span>
                        </a>
                    </div>

                    <!-- Sidebar Menu Toggle Button -->
                    <button class="button-toggle-menu">
                        <i class="ri-menu-line"></i>
                    </button>

                    <!-- Horizontal Menu Toggle Button -->
                    <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </button>
                </div>

                <ul class="topbar-menu d-flex align-items-center gap-3">
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <span class="d-lg-block d-none">
                                <h5 class="my-0 fw-normal">{{Auth::user()->name}} <i
                                        class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i></h5>
                            </span>
                            <span class="account-user-avatar">
                                <img src="{{ asset('/galerias/avatares/innsystem.png') }}" alt="{{Auth::user()->name}}" width="32" class="rounded-circle">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                            <!-- item-->
                            <div class=" dropdown-header noti-title">
                                <h6 class="text-overflow m-0">Bem-vindo!</h6>
                            </div>

                            <!-- item-->
                            <a href="https://innsystem.com.br" target="_Blank" class="dropdown-item">
                                <i class="ri-customer-service-2-line fs-18 align-middle me-1"></i>
                                <span>Suporte</span>
                            </a>

                            <!-- item-->
                            <a href="javascript:;" class="dropdown-item button-logout">
                                <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                                <span>Sair da Conta</span>
                            </a>
                        </div>
                    </li>

                    <li class="d-none d-sm-inline-block">
                        <div class="nav-link" id="light-dark-mode">
                            <i class="ri-moon-line fs-22"></i>
                        </div>
                    </li>

                </ul>
            </div>
        </div>
        <!-- ========== Topbar End ========== -->

        <!-- ========== Left Sidebar Start ========== -->
        <div class="leftside-menu">

            <!-- Brand Logo Light -->
            <a href="{{ route('admin.index') }}" class="logo logo-light">
                <span class="logo-lg">
                    <img src="{{ asset('/tpl_dashboard/images/logo.png') }}" alt="logo">
                </span>
                <span class="logo-sm">
                    <img src="{{ asset('/tpl_dashboard/images/logo-sm.png') }}" alt="small logo">
                </span>
            </a>

            <!-- Brand Logo Dark -->
            <a href="{{ route('admin.index') }}" class="logo logo-dark">
                <span class="logo-lg">
                    <img src="{{ asset('/tpl_dashboard/images/logo-dark.png') }}" alt="dark logo">
                </span>
                <span class="logo-sm">
                    <img src="{{ asset('/tpl_dashboard/images/logo-sm.png') }}" alt="small logo">
                </span>
            </a>

            <!-- Sidebar -left -->
            <div class="h-100" id="leftside-menu-container" data-simplebar>
                <!--- Sidemenu -->
                <ul class="side-nav">
                    @include('admin.includes.sidebar')
                </ul>

                <!--- End Sidemenu -->

                <div class="clearfix"></div>
            </div>
        </div>
        <!-- ========== Left Sidebar End ========== -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                @if (session('error'))
                <div class="container">
                    <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                @endif

                <!-- Start Content-->
                @yield('content')
                <!-- container -->

            </div>
            <!-- content -->

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-end fs-7">
                            Desenvolvido por <a href="https://innsystem.com.br" target="_Blank" class="fw-bold">InnSystem</a>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->


        @yield('pageMODAL')


    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <script src="{{ asset('/tpl_dashboard/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('/tpl_dashboard/js/app.min.js') }}"></script>

    <!-- Others Js -->
    <script src="{{ asset('/plugins/sweetalert/sweetalert2.min.js') }}"></script>

    <script src="{{ asset('/plugins/jquery.mask.js') }}"></script>
    <script src="{{ asset('/plugins/jquery.mask.templates.js') }}"></script>

    <script src="{{ asset('/tpl_dashboard/vendor/jquery-toast-plugin/jquery.toast.min.js') }}"></script>

    @yield('pageJS')

    <script>
        function sendNotification(heading, text, position, loaderBg, icon, hideAfter = 3000, stack = 1, transition = "fade") {
            $.toast({
                heading: heading,
                text: text,
                position: position,
                loaderBg: loaderBg,
                icon: icon,
                hideAfter: hideAfter,
                stack: stack,
                showHideTransition: transition
            });
        }
    </script>

    <script>
        // Logout
        $(document).on('click', '.button-logout', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Deseja sair do Painel?',
                icon: 'error',
                showCancelButton: true,
                // confirmButtonColor: '#d33',
                cancelButtonColor: '#333',
                confirmButtonText: 'Sim, sair!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        }
                    });

                    $.ajax({
                        url: `{{ url('/auth/logout') }}`,
                        method: 'POST',
                        success: function(data) {
                            location.href = '/auth/login';
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                Swal.fire({
                                    text: xhr.responseJSON,
                                    icon: 'warning',
                                    showClass: {
                                        popup: 'animate__animated animate__headShake'
                                    }
                                });
                            } else {
                                Swal.fire({
                                    text: xhr.responseJSON,
                                    icon: 'error',
                                    showClass: {
                                        popup: 'animate__animated animate__headShake'
                                    }
                                });
                            }
                        }
                    });
                }
            })
        });
    </script>

    <script>
        function loadLazyImages() {
            const images = document.querySelectorAll('img.lazy-load');

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.getAttribute('data-src'); // Defina o src com o valor de data-src
                        img.classList.remove('lazy-load'); // Remova a classe de lazy-load
                        observer.unobserve(img); // Pare de observar a imagem
                    }
                });
            }, {
                threshold: 0.1 // A imagem será carregada quando 10% da sua área for visível
            });

            images.forEach(image => {
                observer.observe(image); // Comece a observar as imagens
            });
        }
        document.addEventListener("DOMContentLoaded", function() {
            loadLazyImages();
        });
    </script>

</body>

</html>
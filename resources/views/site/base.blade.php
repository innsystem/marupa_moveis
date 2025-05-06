<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('title')">
    <!-- Title-->
    <title>@yield('title') {{$getSettings['site_name']}}</title>
    <!-- Favicon-->
    <link rel="shortcut icon" href="{{asset('/marupa_icon.png') }}" type="image/x-icon">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('/tpl_site/css/bootstrap.min.css') }}">
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="{{ asset('/tpl_site/css/fontawesome.min.css') }}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('/tpl_site/css/magnific-popup.min.css') }}">
    <!-- Slick Slider -->
    <link rel="stylesheet" href="{{ asset('/tpl_site/css/slick.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('/tpl_site/css/style.css') }}">

    @yield('pageCSS')
</head>

<body>
    <!--==============================
    Mobile Menu
  ============================== -->
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
            <div class="mobile-logo">
                <a href="{{ asset('/') }}"><img src="{{ asset('/marupa_moveis_branco.png') }}" alt="Artraz"></a>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li>
                        <a href="{{ route('site.index') }}#header">Início</a>
                    </li>
                    <li>
                        <a href="{{ route('site.index') }}#about-sec">Sobre Nós</a>
                    </li>
                    <li>
                        <a href="{{ route('site.index') }}#section-services">Serviços</a>
                    </li>                    
                    <li>
                        <a href="{{ route('site.projects.index') }}">Projetos</a>
                    </li>                    
                    <li>
                        <a href="{{ route('site.index') }}">Contato</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--==============================
    Header Area
    ==============================-->
    <header id="header" class="th-header header-layout4">
        <div class="container">
            <div class="sticky-wrapper">
                <div class="sticky-active">
                    <!-- Main Menu Area -->
                    <div class="menu-area">
                        <div class="container">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-auto">
                                    <div class="header-logo">
                                        <a href="{{ asset('/') }}"><img src="{{ asset('/marupa_moveis_branco.png') }}" alt="Marupa Móveis" style="max-width: 260px;"></a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <nav class="main-menu d-none d-lg-inline-block">
                                        <ul>
                                            <li>
                                                <a href="{{ route('site.index') }}#header">Início</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('site.index') }}#about-sec">Sobre Nós</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('site.index') }}#section-services">Serviços</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('site.projects.index') }}">Projetos</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('site.index') }}">Contato</a>
                                            </li>
                                        </ul>
                                    </nav>
                                    <button type="button" class="th-menu-toggle d-inline-block d-lg-none"><i class="far fa-bars"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    @yield('content')

    @yield('pageMODAL')

    <!--==============================
    Footer Area
    ==============================-->
    <footer class="footer-wrapper footer-layout4">
        <div class="widget-area">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-lg-5 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="widget footer-widget">
                            <div class="footer-logo">
                                <img src="{{ asset('/marupa_moveis_branco.png') }}" alt="Marupa Móveis" style="max-width: 260px;">
                            </div>
                            <div class="th-widget-about w-100" style="max-width: 90%;">
                                <p class="footer-text">A Marupa Móveis é uma empresa que oferece serviços de design e fabricação de móveis personalizados.</p>

                                <p class="footer-text mt-3"><b>Responsabilidade Ambiental</b> </p>
                                <!-- Espaço para imagem do selo FSC -->
                                <p class="footer-text"><b>Certificação FSC&reg;</b><br>
                                    A Marupa Móveis é certificada pelo FSC&reg;, selo reconhecido mundialmente por garantir a origem de produtos comprometidos com a preservação da floresta e a sustentabilidade ambiental.
                                </p>
                                <img src="{{ asset('/galerias/logo-fsc.png') }}" alt="Selo FSC" style="max-width:90px; margin: 10px 0; border-radius:10px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 wow fadeInUp" data-wow-delay="0.2s">
                        <div class="widget widget_nav_menu footer-widget style2">
                            <h3 class="widget_title">Serviços</h3>
                            <div class="menu-all-pages-container">
                                <div class="list-two-column">
                                    <ul class="menu">
                                        @foreach($getServices as $service)
                                        <li><a href="{{ route('site.index') }}#section-services">{{ $service->title }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="widget footer-widget style2">
                            <h3 class="widget_title">Contato</h3>
                            <div class="th-widget-contact">
                                <div class="info-box">
                                    <div class="info-box_icon">
                                        <i class="fal fa-location-dot"></i>
                                    </div>
                                    <div class="media-body">
                                        <span class="info-box_label">Endereço</span>
                                        <p class="info-box_text">Rua General Tito, 123 - São Paulo/SP</p>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box_icon">
                                        <i class="fal fa-phone"></i>
                                    </div>
                                    <div class="media-body">
                                        <span class="info-box_label">Telefone</span>
                                        <a href="tel:+11234567890" class="info-box_link">(11) 99999-9999</a>
                                    </div>
                                </div>
                                <div class="info-box">
                                    <div class="info-box_icon">
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                    <div class="media-body">
                                        <span class="info-box_label">E-mail</span>
                                        <a href="mailto:info@marupamoveis.com" class="info-box_link">contato@marupamoveis.com</a>
                                    </div>
                                </div>
                                <h6 class="text-theme mb-2">Redes Sociais:</h6>
                                <div class="th-social">
                                    <a target="_blank" href="https://facebook.com/"><i class="fab fa-facebook-f"></i></a>
                                    <a target="_blank" href="https://skype.com/"><i class="fab fa-skype"></i></a>
                                    <a target="_blank" href="https://twitter.com/"><i class="fab fa-twitter"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <p class="copyright-text text-center mb-0">Copyright By © <a href="{{ asset('/') }}">Marupa Móveis</a> - {{ date('Y')}}</p>
                    <p class="copyright-text text-center mb-0">Desenvolvido por <a href="https://innsystem.com.br" target="_Blank" class="text-reset fw-semibold"><img src="{{ asset('/innsystem-logo-light.png') }}" alt="InnSystem" style="max-width: 100px;"></a></p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Jquery -->
    <script src="{{ asset('/tpl_site/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <!-- Slick Slider -->
    <script src="{{ asset('/tpl_site/js/slick.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('/tpl_site/js/bootstrap.min.js') }}"></script>
    <!-- Magnific Popup -->
    <script src="{{ asset('/tpl_site/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Counter Up -->
    <script src="{{ asset('/tpl_site/js/jquery.counterup.min.js') }}"></script>
    <!-- Wow Animation -->
    <script src="{{ asset('/tpl_site/js/wow.min.js') }}"></script>
    <!-- Main Js File -->
    <script src="{{ asset('/tpl_site/js/main.js') }}"></script>

    @yield('pageJS')
</body>

</html>
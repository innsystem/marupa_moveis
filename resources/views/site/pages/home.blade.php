@extends('site.base')

@section('content')
<!--==============================-->
<!-- Hero Area -->
<div class="th-hero-wrapper hero-4">
    <div class="hero-slider-4 th-carousel" id="heroSlide4" data-fade="false" data-slide-show="1" data-autoplay="true" data-autoplay-speed="15000">
        <div>
            <div class="th-hero-slide">
                <div class="th-hero-bg" data-bg-src="{{ asset('/storage/sliders/slide_1.webp?4') }}" data-overlay="black" data-opacity="3"></div>
                <div class="container">
                    <div class="hero-style4"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="th-hero-slide">
                <div class="th-hero-bg" data-bg-src="{{ asset('/storage/sliders/slide_2.webp?4') }}" data-overlay="black" data-opacity="3"></div>
                <div class="container">
                    <div class="hero-style4"></div>
                </div>
            </div>
        </div>
        <div>
            <div class="th-hero-slide">
                <div class="th-hero-bg" data-bg-src="{{ asset('/storage/sliders/slide_3.webp?4') }}" data-overlay="black" data-opacity="3"></div>
                <div class="container">
                    <div class="hero-style4"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="icon-box">
        <button data-slick-prev="#heroSlide4" class="slick-arrow default cursor-btn"><i class="fal fa-long-arrow-left"></i></button>
        <button data-slick-next="#heroSlide4" class="slick-arrow default cursor-btn"><i class="fal fa-long-arrow-right"></i></button>
    </div>
</div>
<!--======== / Hero Section ========-->

<div class="overflow-hidden space">
    <div class="container">
        <div class="row">
            <div class="col-xl-6 mb-40 mb-xl-0 wow fadeInUp" data-wow-delay="0.2s">
                <div class="pe-xxl-5">
                    <h2 class="sec-title mb-40">Sobre <span class="text-gradient">Marupa Móveis</span></h2>
                    <p class="fs-5 mb-30">Com uma fábrica de 8000 m2 e mais de 100 colaboradores, 37 anos de história, somos referência nacional em marcenaria de alto padrão reconhecida pela excelência, sofisticação e atenção aos detalhes. Atuamos em parceria com os mais renomados arquitetos e designers do país, transformando projetos em obras únicas e personalizadas, no Brasil e no exterior. Nossa expertise se estende por diversos segmentos, incluindo hotéis, lojas, shoppings e residências de alto padrão. Qualidade, inovação e compromisso definem o nosso trabalho.</p>
                    <a href="{{ url('/') }}#section-services" class="th-btn"><span class="line left"></span> Nossos Serviços <span class="line"></span></a>
                </div>
            </div>
            <div class="col-xl-6 align-self-center wow fadeInUp" data-wow-delay="0.2s">
                <div class="img-box8">
                    <div class="img-row">
                        <div class="experience-card wow fadeInLeft" data-wow-delay="0.2s">
                            <span class="year text-gradient">
                                45
                            </span>
                            <div class="content">
                                <h3 class="title">Anos</h3>
                            </div>
                            <h4 class="title2 text-gradient">Experiência</h4>
                        </div>
                        <div class="img1 wow fadeInDown animated" data-wow-delay="0.2s"><img src="{{ asset('/storage/abouts/about_6_1.jpg') }}" alt="About"></div>
                    </div>
                    <div class="img-row">
                        <div class="img2 wow fadeInUp" data-wow-delay="0.2s"><img src="{{ asset('/storage/abouts/about_6_2.jpg') }}" alt="About"></div>
                        <div class="img3 wow fadeInRight" data-wow-delay="0.2s"><img src="{{ asset('/storage/abouts/about_6_3.jpg') }}" alt="About"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--==============================-->
<!-- Service Area  -->
<section id="section-services" class="overflow-hidden space">
    <div class="container">
        <div class="row justify-content-lg-between align-items-end">
            <div class="col-lg-8 wow fadeInUp" data-wow-delay="0.1s">
                <div class="title-area">
                    <h2 class="sec-title">Serviços
                        <span class="text-gradient">Marupa Móveis</span>
                    </h2>
                </div>
            </div>
        </div>
        <div class="service-grid-area">
            <div class="service-list-slide">
                <ul class="service-list-fixed">
                    @foreach($services as $index => $service)
                    <li class="service-list @if($loop->first) active @endif" data-index="{{ $loop->index }}">
                        <span class="service-list_number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h4 class="service-list_title">{{ $service->title }}</h4>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="service-grid-slide">
                <div class="th-carousel" id="sr-grid" data-slide-show="1" data-md-slide-show="1" data-autoplay="true" data-asnavfor="#sr-img">
                    @foreach($services as $index => $service)
                    <div>
                        <div class="service-card style2">
                            <div class="service-card_icon">
                                <img class="svg-img" src="{{ asset('/tpl_site/img/icon/service_1_' . ($loop->iteration) . '.svg') }}" alt="{{ $service->title }}">
                            </div>
                            <p class="service-card_num text-gradient">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                            <h3 class="service-card_title">{{ $service->title }}</h3>
                            <p class="service-card_text">{{ $service->description }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="service-grid-img">
                <div class="th-carousel" id="sr-img" data-slide-show="1" data-md-slide-show="1" data-asnavfor="#sr-grid">
                    @foreach($services as $index => $service)
                    <div>
                        <div class="img">
                            <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->title }}">
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!--==============================-->
<!-- Project Area  -->
<section id="section-projects" class="space-bottom overflow-hidden">
    <div class="container">
        <div class="row justify-content-md-between align-items-end">
            <div class="col-md-8 wow fadeInUp" data-wow-delay="0.2s">
                <div class="title-area">
                    <h2 class="sec-title">Projetos em
                        <span class="text-gradient">Destaque</span>
                    </h2>
                </div>
            </div>
            <div class="col-auto mt-n4 mt-lg-0 wow fadeInUp" data-wow-delay="0.3s">
                <div class="sec-btn">
                    <a href="{{ route('site.projects.index') }}" class="link-btn">Ver Todos os Projetos</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container th-container3">
        <div class="project-slide-wrap">
            <div class="row" id="projectSlide3">
                @foreach($projects as $project)
                <div class="col-lg-6">
                    <div class="project-card">
                        <div class="project-img">
                            <a href="{{ route('site.projects.show', $project->slug) }}">
                                <img src="{{ asset($project->cover ?? '/storage/portfolios/portfolio_1.png') }}" alt="{{ $project->title }}">
                            </a>
                        </div>
                        <h3 class="h5 project-title"><a href="{{ route('site.projects.show', $project->slug) }}">{{ $project->title }}</a></h3>
                        <p class="project-map"><i class="fal fa-location-dot"></i>{{ $project->location ?? '' }}</p>
                        <div class="project-number">{{ $loop->iteration < 10 ? '0'.$loop->iteration : $loop->iteration }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="slider-nav-wrap">
                <div class="slider-nav">
                    <button data-slick-prev="#projectSlide3" class="nav-btn"><i class="fal fa-long-arrow-left"></i></button>
                    <div class="custom-dots"></div>
                    <button data-slick-next="#projectSlide3" class="nav-btn"><i class="fal fa-long-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>
<!--==============================-->
<!-- Counter Area  --
<section class="d-none">
    <div class="container">
        <div class="video-counter">
            <div class="th-video">
                <img src="{{ asset('/storage/imgs/preview-video.png') }}" alt="Vídeo Marupa Móveis">
                <a href="https://www.youtube.com/watch?v=_sI_Ps7JSEk" class="play-btn style2 popup-video"><i class="fas fa-play"></i></a>
            </div>
            <div class="counter-card-video">
                <h2 class="sec-title mb-4 wow fadeInUp" data-wow-delay="0.2s">Simplicidade é o <span class="text-gradient">máximo</span></h2>
                <div class="counter-card-wrap">
                    <div class="counter-card wow fadeInUp" data-wow-delay="0.2s">
                        <h3 class="counter-card_number"><span class="counter-number">600</span></h3>
                        <p class="counter-card_text">Projetos Realizados</p>
                    </div>
                    <div class="counter-card wow fadeInUp" data-wow-delay="0.3s">
                        <h3 class="counter-card_number"><span class="counter-number">60</span></h3>
                        <p class="counter-card_text">Colaboradores</p>
                    </div>
                    <div class="counter-card wow fadeInUp" data-wow-delay="0.4s">
                        <h3 class="counter-card_number"><span class="counter-number">200</span></h3>
                        <p class="counter-card_text">Parceiros</p>
                    </div>
                    <div class="counter-card wow fadeInUp" data-wow-delay="0.5s">
                        <h3 class="counter-card_number"><span class="counter-number">10000</span></h3>
                        <p class="counter-card_text">Publicações na Imprensa</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
-->

<!--==============================-->
<!-- About Area  -->
<div id="about-sec" class="space">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 mb-5 mb-xl-0 wow fadeInLeft" data-wow-delay="0.2s">
                <div class="img-box1">
                    <div class="img1">
                        <img src="{{ asset('/storage/abouts/about.png') }}" alt="Sobre a Marupa Móveis">
                    </div>
                </div>
            </div>
            <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                <p class="sub-title">Marupa Móveis</p>
                <h2 class="sec-title mb-40">Nossa História</h2>
                <p class="mb-30">A nossa trajetória teve início no final dos anos 80, quando Reinaldo, ao lado de seu pai Sr. Aparecido, uniram paixão, talento e dedicação para fundar uma marcenaria com um único propósito: transformar madeira em arte, com excelência e precisão características de ambos que sempre fizeram tudo com muito capricho. O que começou como uma pequena marcenaria ao longo dos anos foi se destacando pela qualidade incomparável e atenção aos detalhes — marcas que nos acompanham até hoje.</p>
                <p class="mb-30">Com quase quatro décadas de história, nos tornamos uma referência nacional em marcenaria de alto padrão. Atualmente, contamos com uma estrutura de 8.000 m², equipada com máquinas de última geração, e um time de mais de 100 profissionais altamente qualificados. Nosso portfólio reúne projetos assinados pelos mais renomados arquitetos do Brasil, sempre com foco em sofisticação, inovação e funcionalidade.</p>
                <p class="mb-40">Nossos diferenciais estão no compromisso com a qualidade superior, no cumprimento rigoroso dos prazos e na entrega de produtos que estão entre os melhores do mercado. Utilizamos matéria-prima de excelência, selecionada entre o que há de melhor no mercado mundial, sempre com responsabilidade ambiental. Temos e seguimos rigorosamente as normas do certificado FSC (Forest Stewardship Council), assegurando que toda a madeira utilizada em nossos projetos provém de fontes responsáveis e sustentáveis. Mais do que móveis, criamos experiências únicas, feitas sob medida para clientes exigentes que valorizam o que há de melhor.</p>
            </div>
        </div>
    </div>
    <div class="shape-mockup jump" data-top="0" data-right="0"><img src="{{ asset('/tpl_site/img/shape/shape_3.png?1') }}" alt="shape"></div>
</div>
@endsection

@section('pageMODAL')
@endsection

@section('pageJS')
@endsection

@section('pageCSS')
@endsection
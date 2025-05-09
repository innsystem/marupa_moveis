@extends('site.base')

@section('title', 'Nossa História')

@section('content')
<div class="container th-container2" style="min-height:160px;"></div>

<section id="about-sec" class="space">
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
                <!-- <p class="sub-title">Marupa Móveis</p> -->
                <h2 class="sec-title mb-40">Nossa História</h2>
                <p class="mb-30">A nossa trajetória teve início no final dos anos 80, quando Reinaldo, ao lado de seu pai Sr. Aparecido, uniram paixão, talento e dedicação para fundar uma marcenaria com um único propósito: transformar madeira em arte, com excelência e precisão características de ambos que sempre fizeram tudo com muito capricho. O que começou como uma pequena marcenaria ao longo dos anos foi se destacando pela qualidade incomparável e atenção aos detalhes — marcas que nos acompanham até hoje.</p>
                <p class="mb-30">Com quase quatro décadas de história, nos tornamos uma referência nacional em marcenaria de alto padrão. Atualmente, contamos com uma estrutura de 8.000 m², equipada com máquinas de última geração, e um time de mais de 100 profissionais altamente qualificados. Nosso portfólio reúne projetos assinados pelos mais renomados arquitetos do Brasil, sempre com foco em sofisticação, inovação e funcionalidade.</p>
                <p class="mb-40">Nossos diferenciais estão no compromisso com a qualidade superior, no cumprimento rigoroso dos prazos e na entrega de produtos que estão entre os melhores do mercado. Utilizamos matéria-prima de excelência, selecionada entre o que há de melhor no mercado mundial, sempre com responsabilidade ambiental. Temos e seguimos rigorosamente as normas do certificado FSC (Forest Stewardship Council), assegurando que toda a madeira utilizada em nossos projetos provém de fontes responsáveis e sustentáveis. Mais do que móveis, criamos experiências únicas, feitas sob medida para clientes exigentes que valorizam o que há de melhor.</p>
            </div>
        </div>
    </div>
    <div class="shape-mockup jump" data-top="0" data-right="0"><img src="{{ asset('/tpl_site/img/shape/shape_3.png?1') }}" alt="shape"></div>
</section>
@endsection

@section('pageMODAL')
@endsection

@section('pageJS')
@endsection

@section('pageCSS')
@endsection 
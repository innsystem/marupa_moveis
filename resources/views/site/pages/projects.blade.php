@extends('site.base')

@section('title', 'Nossos Projetos')

@section('content')
<div class="container th-container2" style="min-height:160px;"></div>

<section id="section-projects" class="space-bottom overflow-hidden">
    <div class="container">
        <div class="row justify-content-md-between align-items-end">
            <div class="col-md-8 wow fadeInUp" data-wow-delay="0.2s">
                <div class="title-area">
                    <h2 class="sec-title">Nossos
                        <span class="text-gradient">Projetos</span>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                @if(isset($portfolioCategories) && $portfolioCategories->count())
                <div id="portfolio-filters" class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-theme active" data-filter="*">Todos</button>
                    @foreach($portfolioCategories as $category)
                    <button type="button" class="btn btn-outline-theme" data-filter=".cat-{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="container th-container">
        <div class="row portfolio-grid">
            @foreach($projects as $key => $project)
            <div class="col-12 col-md-6 mb-5 pb-3 portfolio-item @foreach($project->categories as $cat) cat-{{ $cat->id }} @endforeach">
                <div class="project-card">
                    <div class="project-img">
                        <a href="{{ route('site.projects.show', $project->slug) }}">
                            <img src="{{ asset($project->cover ?? '/storage/portfolios/portfolio_1.png') }}" alt="{{ $project->title }}">
                        </a>
                    </div>
                    <h3 class="h5 project-title"><a href="{{ route('site.projects.show', $project->slug) }}" tabindex="0">{{ $project->title }}</a></h3>
                    {!! $project->description ?? '' !!}
                    <!-- <div class="project-number">{{ $key + 1 < 10 ? '0'.$key + 1 : $key + 1 }}</div> -->
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@section('pageMODAL')
@endsection

@section('pageJS')
<script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
<script>
    $(document).ready(function() {
        var $grid = $('.portfolio-grid').isotope({
            itemSelector: '.portfolio-item',
            layoutMode: 'fitRows'
        });
        $('#portfolio-filters').on('click', 'button', function() {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({
                filter: filterValue
            });
            $('#portfolio-filters button').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>
@endsection

@section('pageCSS')
@endsection
@extends('site.base')

@section('title', $category->name)

@section('content')
<div class="container th-container2" style="min-height:160px;"></div>

<section id="section-projects" class="space-bottom overflow-hidden">
    <div class="container">
        <div class="row justify-content-md-between align-items-end">
            <div class="col-md-8 wow fadeInUp" data-wow-delay="0.2s">
                <div class="title-area">
                    <h2 class="sec-title">Projetos
                        <span class="text-gradient">{{$category->name}}</span>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container th-container">
        <div class="row">
            @foreach($portfolios as $key => $project)
            <div class="col-12 col-md-6 mb-5 pb-3">
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
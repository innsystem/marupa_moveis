@extends('site.base')

@section('title', 'Detalhes do Projeto')

@section('content')
<div class="container th-container2" style="min-height:160px;"></div>

<section id="section-projects" class="space-bottom overflow-hidden">
    <div class="container th-container">
        <div class="row ">
            <div class="col-12 col-md-8 col-lg-9">
                <div class="project-details">
                    <h2 class="sec-title fw-normal mb-2">{{ $project->title }}</h2>
                    <!-- <p class="project-map mb-40"><i class="fal fa-location-dot"></i>{{ $project->location ?? '' }}</p> -->
                    <div class="mb-4">
                        <img class="w-75" src="{{ asset($project->cover ?? '/storage/portfolios/portfolio_1.png') }}" alt="project" class="" style="border-radius: 10px;">
                    </div>
                    <div class="project-content">
                        <p class="mb-20">{!! $project->description !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('pageMODAL')
@endsection

@section('pageJS')
@endsection

@section('pageCSS')
@endsection
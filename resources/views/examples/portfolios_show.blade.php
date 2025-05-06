@extends('site.base')

@section('title', $portfolio->title)

@section('content')
<div>
    <h1>{{ $portfolio->title }}</h1>
    <p>{{ $portfolio->description }}</p>
    <a href="{{route('site.index')}}">Volta ao inicio</a>
</div>
@endsection
@extends('site.base')

@section('title', $service->title)

@section('content')
<div>
    <h1>{{ $service->title }}</h1>
    <p>{{ $service->description }}</p>
    <a href="{{route('site.index')}}">Volta ao inicio</a>
</div>
@endsection
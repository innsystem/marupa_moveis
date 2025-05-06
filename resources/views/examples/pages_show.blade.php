@extends('site.base')

@section('title', $page->title)

@section('content')
<div>
    <h1>{{ $page->title }}</h1>
    <p>{{ $page->content }}</p>
    <a href="{{route('site.index')}}">Volta ao inicio</a>
</div>
@endsection
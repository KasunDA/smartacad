@extends('front.layout.default')

@section('title', 'Home')

@section('page-title')
    <div class="page-title">
        <h1>Blank Page </h1>
    </div>
@endsection

@section('breadcrumb')
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ url('/') }}">Home</a>
            <i class="fa fa-home"></i>
        </li>
    </ul>
@stop


@section('content')
    <div class="page-content-inner">
        <div class="note note-info">
            <p> A black page template with a minimal dependency assets to use as a base for any custom page you create </p>
        </div>
    </div>
@endsection


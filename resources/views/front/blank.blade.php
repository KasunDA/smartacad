@extends('admin.layout.default')

@section('page-level-css')

@endsection
@section('layout-style')

@endsection

@section('title', 'Blank Page')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <span>Page Layouts</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Blank Page Layout
        <small>blank page layout</small>
    </h3>
@endsection

@section('page-level-js')

@endsection

@section('layout-script')
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
@endsection

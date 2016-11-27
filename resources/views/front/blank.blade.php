@extends('front.layout.default')

@section('page-level-css')
<!-- BEGIN PAGE LEVEL STYLES -->
    <link href="{{ asset('assets/pages/css/profile-2.min.css') }}" rel="stylesheet" type="text/css"/>
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'View Profile')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>View Profile</span>
    </li>
@stop

@section('page-title')
    <h1> Blank Page Layout
        <small>blank page layout</small>
    </h1>
@endsection

@section('content')
    <div class="col-md-12">
        <small>blank page layout</small>
    </div>
@endsection

@section('page-level-js')

@endsection

@section('layout-script')
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
@endsection

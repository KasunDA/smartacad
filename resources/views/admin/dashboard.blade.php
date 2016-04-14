@extends('admin.layout.default')

@section('layout-style')

@endsection

@section('title', 'Dashboard')

@section('breadcrumb')
    <li>
        <a href="/">Home</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <a href="#">Blank Page</a>
        <i class="fa fa-circle"></i>
    </li>
    <li>
        <span>Page Layouts</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Blank Page Layout
        <small>blank page layout</small>
    </h3>
    <!-- END PAGE TITLE-->
    <div class="note note-info">
        <p> A black page template with a minimal dependency assets to use as a base for any custom page you create </p>
    </div>
@endsection


@section('layout-script')
    <script src="assets/layouts/layout/scripts/layout.min.js" type="text/javascript"></script>
    <script src="assets/layouts/layout/scripts/demo.min.js" type="text/javascript"></script>
    <script src="assets/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
@endsection

@extends('admin.layout.default')


@section('title', 'Change Password')

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
        <span>Change Password</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Change Password</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-lock font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Change Password</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                    <form method="POST" action="/users/change" accept-charset="UTF-8" role="form">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label class="control-label">Current Password</label>
                            <input name="password" type="password" class="form-control" required /> </div>
                        <div class="form-group">
                            <label class="control-label">New Password</label>
                            <input name="new_password" type="password" class="form-control" required /> </div>
                        <div class="form-group">
                            <label class="control-label">Re-type New Password</label>
                            <input name="password_confirmation" type="password" class="form-control" required /> </div>
                        <div class="margin-top-10">
                            <button class="btn green"> Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>

@endsection

@section('layout-script')
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/users/change"]');
        });
    </script>
@endsection

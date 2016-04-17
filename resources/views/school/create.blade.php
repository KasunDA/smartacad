@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Create User')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Create School</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Create School</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Create School</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                    <form method="POST" action="{{ url('/schools/create') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                        {!! csrf_field() !!}
                        <div class="form-body">

                            <div class="form-group">
                                <label>School Name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required name="name" placeholder="School Name" value="{{ old('name') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>School Full Name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required name="full_name" placeholder="School Full Name" value="{{ old('full_name') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>School Email</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" class="form-control input-lg" required placeholder="Email" name="email" value="{{ old('email') }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>School Phone</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-phone"></i>
                                    <input type="text" class="form-control input-lg" placeholder="Mobile" name="phone_no" value="{{ old('phone_no') }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>Admin</label>
                                <div>
                                    <select name="user_type_id" class="form-control input-lg selectpicker">
                                        <option value="">Admin User List</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue pull-right">Create</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/guardians/create"]');
        });
    </script>
@endsection

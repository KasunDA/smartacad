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
        <span>Add Guardian</span>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Add Guardian</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Add Guardian</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                    <form method="POST" action="{{ url('/sponsors/create') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                        {!! csrf_field() !!}
                        <div class="form-body">
                            <div class="form-group">
                                <label>Title</label>
                                <div>
                                    <select name="user_type_id" class="form-control input-lg selectpicker">
                                        <option value="">Noting Selected</option>
                                        @foreach($titles as $title)
                                            <option value="{{$title->title}}">{{$title->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>First name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-user"></i>
                                    <input type="text" class="form-control input-lg" required name="first_name" placeholder="First name" value="{{ old('first_name') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>Last name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-user"></i>
                                    <input type="text" class="form-control input-lg" required name="last_name" placeholder="Last name" value="{{ old('last_name') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" class="form-control input-lg" required placeholder="Email" name="email" value="{{ old('email') }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>Mobile</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-phone"></i>
                                    <input type="text" class="form-control input-lg" placeholder="Mobile" name="phone_no" value="{{ old('phone_no') }}"> </div>
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
            setTabActive('[href="/sponsors/create"]');
        });
    </script>
@endsection

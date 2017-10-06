@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Create Account')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Add Account</span>
    </li>
@stop

@section('content')
    <h3 class="page-title"> Add Account</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Add Account</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                    <form method="POST" action="{{ url('/accounts/create') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                        {!! csrf_field() !!}
                        <input type="hidden" name="user_type_id" value="3"/>
                        <div class="form-body">
                            <div class="form-group">
                                <label>Title</label>
                                <div>
                                    <select name="salutation_id" class="form-control input-lg selectpicker">
                                        <option value="">Noting Selected</option>

                                        @foreach($salutations as $salutation)
                                            <option value="{{$salutation->salutation_id}}">{{$salutation->salutation}}</option>
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
                                    <input type="text" class="form-control input-lg" required name="other_name" placeholder="Other name" value="{{ old('other_name') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>Active Mobile Number</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-phone"></i>
                                    <input type="text" class="form-control input-lg" required placeholder="Active Mobile Number" name="phone_no" value="{{ old('phone_no') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" class="form-control input-lg" placeholder="Email" name="email" value="{{ old('email') }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>User Type</label>
                                <div>
                                    <select name="user_type_id" class="form-control input-lg selectpicker">
                                        <option value="">Noting Selected</option>
                                        @foreach($user_types as $user_type)
                                            <option value="{{$user_type->user_type_id}}">{{$user_type->user_type}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue pull-right btn-lg">Create</button>
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
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/accounts/create"]');
        });
    </script>
@endsection

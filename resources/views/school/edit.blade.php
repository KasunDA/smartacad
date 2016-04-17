@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Edit School')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Edit School</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">Edit School</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-user font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> Edit School Record</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                    <form method="POST" action="{{ url('/schools/edit/' . $hashIds->encode($school->schools_id)) }}" accept-charset="UTF-8" class="form-horizontal" role="form" enctype="multipart/form-data">
                        <input name="_method" type="hidden" value="PATCH">
                        {!! csrf_field() !!}
                        <div class="form-body">

                            <div class="form-group">
                                <label>School Name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required name="name" placeholder="School Name" value="{{ $school->name }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>School Full Name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required name="full_name" placeholder="School Full Name" value="{{ $school->full_name }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>School Email</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-envelope"></i>
                                    <input type="email" class="form-control input-lg" placeholder="Email" name="email" value="{{ $school->email }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>School Phone</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-phone"></i>
                                    <input type="text" class="form-control input-lg" required placeholder="Mobile" name="phone_no" value="{{ $school->phone_no }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>School Motto</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" placeholder="School Motto" name="motto" value="{{ $school->motto }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>School Website</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-sitemap"></i>
                                    <input type="text" class="form-control input-lg" placeholder="School Website" name="website" value="{{ $school->website }}"> </div>
                            </div>

                            <div class="form-group">
                                <label>School Address</label>
                                    <textarea class="form-control input-lg" rows="3" required placeholder="School Address" name="address">{{ $school->address }}</textarea>
                            </div>

                            <div class="form-group">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-new thumbnail" style="width: 100px; height: 75px;">
                                        <img src="{{ ($school->getLogoPath()) ? $school->getLogoPath() : 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image' }}" alt="" />
                                    </div>
                                    <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 100px; max-height: 75px;"> </div>
                                    <div>
                                        <span class="btn default btn-file">
                                        <span class="fileinput-new"> Select Logo </span>
                                        <span class="fileinput-exists"> Change </span>
                                        <input type="file" name="logo"></span>
                                        <a href="javascript:;" class="btn default fileinput-exists" data-dismiss="fileinput"> Remove </a>
                                    </div>
                                </div>
                            </div>
                            {{--<div class="form-group">--}}
                            {{--<label>Admin</label>--}}
                            {{--<div>--}}
                            {{--<select name="user_type_id" class="form-control input-lg selectpicker">--}}
                            {{--<option value="1">Admin User List</option>--}}
                            {{--</select>--}}
                            {{--</div>--}}
                            {{--</div>--}}
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue pull-right">Update</button>
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
    <script src="{{ asset('assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/schools/create"]');
        });
    </script>
@endsection

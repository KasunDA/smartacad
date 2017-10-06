@extends('admin.layout.default')

@section('title', 'School Database Config')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <span>Manage School DB</span>
    </li>
@stop


@section('content')
    <h3 class="page-title">{{$school->name}} School Database Config</h3>

    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="fa fa-gears font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase">{{$school->name}} School Database Config</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @include('errors.errors')
                    <form method="POST" action="{{ url('/schools/db-config') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                        {!! csrf_field() !!}
                        <input type="hidden" name="school_id" value="{{$school->school_id}}">
                        <div class="form-body">

                            <div class="form-group">
                                <label>Database Host</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required name="host" placeholder="Database Host" value="{{ ($db) ? $db->host : '' }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>Database Name</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required name="database" placeholder="Database Name" value="{{ ($db) ? $db->database : '' }}"> </div>
                            </div>


                            <div class="form-group">
                                <label>Database Username</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" required placeholder="Database Username" name="username" value="{{ ($db) ? $db->username : '' }}"> </div>
                            </div>
                            <div class="form-group">
                                <label>Database Password</label>
                                <div class="input-icon input-icon-lg">
                                    <i class="fa fa-edit"></i>
                                    <input type="text" class="form-control input-lg" placeholder="Database Password" name="password" value="{{ ($db) ? $db->password : '' }}"> </div>
                            </div>
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


@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/schools"]');
        });
    </script>
@endsection

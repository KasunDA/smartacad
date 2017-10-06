@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Create School')

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
                    <form method="POST" action="{{ url('/schools/search') }}" accept-charset="UTF-8" class="form-horizontal" role="form"  enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-body">

                            <div class="form-group">
                                <label>Schools</label>
                                <select name="schools_id" class="form-control selectpicker">
                                    <option value="">Nothing Selected</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->schools_id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue pull-right">Create</button>
                        </div>
                    </form>
                </div>
                @if(isset($users))
                    <div class="portlet-body">
                        <div class="row">
                            <table class="table table-striped table-bordered table-hover" id="schools_datatable">
                                <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 39%;">Name</th>
                                    <th style="width: 10%;">Mobile No.</th>
                                    <th style="width: 15%;">Email</th>
                                    <th style="width: 15%;">Gender</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 5%;">View</th>
                                    <th style="width: 5%;">Edit</th>
                                    <th style="width: 5%;">Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($users) > 0)
                                    <?php $i = 1; ?>
                                    @foreach($users as $user)
                                        <tr class="odd gradeX">
                                            <td class="center">{{$i++}}</td>
                                            <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                            <td>{{ $user->phone_no }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{!! ($user->gender) ? $user->gender : '<span class="label label-danger">nil</span>' !!}</td>
                                            <td>
                                                @if($user->status === 1)
                                                    <button value="{{ $user->user_id }}" rel="2" class="btn btn-success btn-rounded btn-condensed btn-xs user_status">
                                                        Deactivate
                                                    </button>
                                                @else
                                                    <button value="{{ $user->user_id }}" rel="1" class="btn btn-danger btn-rounded btn-condensed btn-xs user_status">
                                                        Activate
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <a target="_blank" href="{{ url('/users/view/'.$hashIds->encode($user->user_id)) }}" class="btn btn-info btn-rounded btn-condensed btn-xs">
                                                    <span class="fa fa-eye-slash"></span>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{ url('/users/edit/'.$hashIds->encode($user->user_id)) }}" class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                    <span class="fa fa-edit"></span>
                                                </a>
                                            </td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 39%;">Name</th>
                                    <th style="width: 10%;">Mobile No.</th>
                                    <th style="width: 15%;">Email</th>
                                    <th style="width: 15%;">Gender</th>
                                    <th style="width: 10%;">Status</th>
                                    <th style="width: 5%;">View</th>
                                    <th style="width: 5%;">Edit</th>
                                    <th style="width: 5%;">Delete</th>
                                </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('assets/global/plugins/datatables/media/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js') }}"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/custom/js/schools/school.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/schools"]');
            TableManaged.init();
        });
    </script>
    @end
@endsection

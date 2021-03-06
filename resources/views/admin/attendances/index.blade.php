@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Manage Attendances')

@section('breadcrumb')
    <li>
        <i class="fa fa-home"></i>
        <a href="{{ url('/dashboard') }}">Home</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <i class="fa fa-money"></i>
        <a href="{{ url('/attendances') }}">Attendances</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Attendance Management</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="active">
                            <li class="{{ (session('attendance-tab') == 'initiate') ? 'active' : ((!session()->has('attendance-tab')) ? 'active' : '') }}">
                                <a href="#initiate_attendance_tab" data-toggle="tab"> <i class="fa fa-check"></i> Initiate / <i class="fa fa-edit"></i> Adjust Attendance</a>
                            </li>
                            <li class="{{ (session('attendance-tab') == 'classroom') ? 'active' : '' }}">
                                <a href="#classroom_summary_tab" data-toggle="tab"> <i class="fa fa-th"></i> View Summary (By Class)</a>
                            </li>
                            <li class="{{ (session('attendance-tab') == 'student') ? 'active' : '' }}">
                                <a href="#student_summary_tab" data-toggle="tab"> <i class="fa fa-users"></i> View Summary (By Student)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane {{ (session('attendance-tab') == 'initiate') ? 'active' : ((!session()->has('attendance-tab')) ? 'active' : '') }}" id="initiate_attendance_tab">
                                <div class="panel-body">
                                    <div class="col-md-12">
                                        <div class="alert alert-info"> Take/Initiate Attendance</div>
                                        <div class="table-container">
                                                <div class="table-actions-wrapper">
                                                    <span> </span>
                                                    Search: <input type="text" class="form-control input-inline input-small input-sm" id="search_param"/>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover" id="take_attendance_datatable">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th>#</th>
                                                        <th>Class Name</th>
                                                        <th>Academic Term</th>
                                                        <th>Head Tutor</th>
                                                        <th>Taken</th>
                                                        <th>Initiate</th>
                                                        <th>Adjust</th>
                                                    </tr>
                                                    </thead>
                                                    <tfoot>
                                                    <tr role="row" class="heading">
                                                        <th>#</th>
                                                        <th>Class Name</th>
                                                        <th>Academic Term</th>
                                                        <th>Head Tutor</th>
                                                        <th>Taken</th>
                                                        <th>Initiate</th>
                                                        <th>Adjust</th>
                                                    </tr>
                                                    </tfoot>
                                                    <tbody>
                                                    <?php $i=1; ?>
                                                    @foreach($classrooms as $classroom)
                                                        @if(isset($classroom->classroom))
                                                            <tr role="row" class="heading">
                                                                <td>{{ $i++ }}</td>
                                                                <td>
                                                                    {!! isset($classroom->classroom) ? $classroom->classroom->classroom : LabelHelper::default() !!} ::
                                                                    {!! isset($classroom->classroom)
                                                                        ? $classroom->classroom->studentClasses->where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)->count() . ' Student(s)'
                                                                        : LabelHelper::default() !!}
                                                                </td>
                                                                <td>{{ AcademicTerm::activeTerm()->academic_term }}</td>
                                                                <td>
                                                                    {!! isset($classroom->user) ? $classroom->user->simpleNameNSalutation() : LabelHelper::default() !!} ::
                                                                </td>
                                                                <td>
                                                                    {!! isset($classroom->classroom)
                                                                        ? $classroom->classroom->attendances()->where('academic_term_id', AcademicTerm::activeTerm()->academic_term_id)->count()
                                                                        : LabelHelper::default() !!}
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('initiateAttendance', ['classId'=>$hashIds->encode($classroom->classroom_id)]) }}" class="btn btn-info btn-xs">
                                                                        <i class="fa fa-check-square-o"></i>
                                                                    </a>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('adjustAttendance', ['classId'=>$hashIds->encode($classroom->classroom_id)]) }}" class="btn btn-warning btn-xs">
                                                                        <i class="fa fa-edit"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                    </tbody>


                                                </table>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ (session('attendance-tab') == 'classroom') ? 'active' : '' }}" id="classroom_summary_tab">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room To View Class Summary</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'classroom_summary_form'
                                    ])
                                !!}
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Academic Year <small class="font-red">*</small></label>
                                                <div>
                                                    {!! Form::select('academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id,
                                                        ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required'])
                                                     !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Academic Term <small class="font-red">*</small></label>
                                                {!! Form::select('academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                    ->pluck('academic_term', 'academic_term_id')
                                                    ->prepend('- Academic Term -', ''),
                                                    AcademicTerm::activeTerm()->academic_term_id,
                                                    ['class'=>'form-control', 'id'=>'academic_term_id', 'required'=>'required'])
                                                 !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Class Level <small class="font-red">*</small></label>
                                                <div>
                                                    {!! Form::select('classlevel_id', $classlevels, old('classlevel_id'),
                                                        ['class'=>'form-control', 'id'=>'classlevel_id', 'required'=>'required'])
                                                     !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Class Room <small class="font-red">*</small></label>
                                                {!! Form::select('classroom_id', [], '',
                                                    ['class'=>'form-control', 'id'=>'classroom_id', 'required'=>'required'])
                                                 !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions noborder">
                                    <button type="submit" class="btn blue pull-right">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                                {!! Form::close() !!}
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <table class="table table-striped table-bordered table-hover" id="classroom_summary_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane {{ (session('attendance-tab') == 'student') ? 'active' : '' }}" id="student_summary_tab">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room To View Student Summary</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'student_summary_form'
                                    ])
                                !!}
                                <div class="form-body">
                                    <div class="form-group">
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Academic Year <small class="font-red">*</small></label>
                                                <div>
                                                    {!! Form::select('view_academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id,
                                                        ['class'=>'form-control', 'id'=>'view_academic_year_id', 'required'=>'required'])
                                                     !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Academic Term <small class="font-red">*</small></label>
                                                {!! Form::select('view_academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                    ->pluck('academic_term', 'academic_term_id')
                                                    ->prepend('- Academic Term -', ''),
                                                    AcademicTerm::activeTerm()->academic_term_id,
                                                    ['class'=>'form-control', 'id'=>'view_academic_term_id', 'required'=>'required'])
                                                 !!}
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-md-offset-1">
                                            <div class="form-group">
                                                <label class="control-label">Class Level <small class="font-red">*</small></label>
                                                <div>
                                                    {!! Form::select('view_classlevel_id', $classlevels, old('classlevel_id'),
                                                        ['class'=>'form-control', 'id'=>'view_classlevel_id', 'required'=>'required'])
                                                     !!}
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Class Room <small class="font-red">*</small></label>
                                                {!! Form::select('view_classroom_id', [], '',
                                                    ['class'=>'form-control', 'id'=>'view_classroom_id', 'required'=>'required'])
                                                 !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions noborder">
                                    <button type="submit" class="btn blue pull-right">
                                        <i class="fa fa-search"></i> Search
                                    </button>
                                </div>
                                {!! Form::close() !!}
                                <div class="row">
                                    <div class="col-md-10">
                                        <div class="portlet-body">
                                            <div class="row">
                                                <table class="table table-striped table-bordered table-hover" id="student_summary_datatable">

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/custom/js/attendances/attendance.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/attendances"]');

            setTableData($('#take_attendance_datatable')).init();
        });
    </script>
@endsection

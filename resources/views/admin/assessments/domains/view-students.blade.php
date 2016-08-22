@extends('admin.layout.default')

@section('layout-style')
        <!-- BEGIN PAGE LEVEL STYLES -->
<link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
<!-- END PAGE LEVEL STYLES -->
@endsection

@section('title', 'Affective Domains Assessment')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/domains') }}">Domain Assessments</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Students Affective Domain Assessments</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-10 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gears font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            List of Students in {{ $classroom->classroom }} for {{$term->academic_term}} Academic Term
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="students_datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student No.</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Assess</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($studentClasses)
                                <?php $i = 1; ?>
                                @foreach($studentClasses as $studentClass)
                                    @if($studentClass->student()->first() and $studentClass->student()->first()->status_id == 1)
                                        <?php
                                            $student_id = $studentClass->student()->first()->student_id;
                                            $assessed = $studentClass->student()->first()->domainAssessment()->where('academic_term_id', $term->academic_term_id)->count();
                                        ?>
                                        <tr class="odd gradeX">
                                            <td class="center">{{$i++}}</td>
                                            <td>{{ $studentClass->student()->first()->student_no }}</td>
                                            <td>
                                                <a target="_blank" href="/students/view/{{$hashIds->encode($student_id)}}" class="btn btn-link">
                                                    {{ $studentClass->student()->first()->fullNames() }}
                                                </a>
                                            </td>
                                            <td>{{ $studentClass->student()->first()->gender }}</td>
                                            <td>
                                                {!! ($assessed > 0) ? '<label class="label label-success label-sm">Assessed</label>' : '<label class="label label-danger label-sm">Not Assess</label>' !!}
                                            </td>
                                            <td>
                                                <a target="_blank" href="/domains/assess/{{$hashIds->encode($student_id).'/'.$hashIds->encode($term->academic_term_id)}}" class="btn btn-link">
                                                    {!! ($assessed > 0) ? '<i class="fa fa-edit"></i> Modify' : '<i class="fa fa-check-circle-o"></i> Proceed' !!}
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Student No.</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection

    @section('page-level-js')
            <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    @endsection

    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/domains"]');

            setTableData($('#students_datatable')).init();
        });
    </script>
@endsection

@extends('admin.layout.default')

@section('title', 'Terminal Class Room Assessments')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <i class="fa fa-th"></i>
        <a href="{{ url('/exams') }}">Exams</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="#">Class Level Broad Sheet Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Class Level Broad Sheet Details</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-book font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Class Level Summary</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Class Group </th>
                                <td> {{ $classLevel->classGroup->classgroup }} </td>
                                <th> Class Level </th>
                                <td> {{ $classLevel->classlevel }} </td>
                            </tr>
                            <tr>
                                <th> Academic Year </th>
                                <td>{{ $academicYear->academic_year }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
        <div class="col-md-10 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gears font-green"></i>
                        <span class="caption-subject font-green bold uppercase">List of Students, Terminal Subjects.</span>
                    </div>
                </div>
                <div id="error-div"></div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Total Score</th>
                                    <th>Position</th>
                                    <th>View Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($examsView))
                                @foreach($examsView as $view)
                                    <tr class="odd gradeX">
                                        <td class="center">{{ $loop->iteration }}</td>
                                        <td>{{ $view->student_no }}</td>
                                        <td>
                                            <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($view->student->student_id)) }}" class="btn btn-link">
                                                {{ $view->fullname }}
                                            </a>
                                        </td>
                                        <td>{{ $view->student->gender }}</td>
                                        <td>{{ $view->subject->subject }}</td>
                                        <td>{{ $view->student_total }}</td>
                                        <td>
                                            <a target="_blank" href="{{ url('/exams/student-terminal-result/'.$hashIds->encode($view->student->student_id).'/'.$hashIds->encode($view->academic_year_id)) }}" class="btn btn-link">
                                                <span class="fa fa-eye"></span> Print
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><th colspan="5">No Record Found</th></tr>
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Gender</th>
                                    <th>Total Score</th>
                                    <th>Position</th>
                                    <th>View Detail</th>
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


@section('layout-script')
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/exams"]');
        });
    </script>
@endsection

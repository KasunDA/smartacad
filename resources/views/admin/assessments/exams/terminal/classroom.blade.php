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
        <a href="#">Terminal Class Room Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page">Terminal Class Room Assessments Details</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-book font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Terminal Class Room Summary</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Class Room </th>
                                <td> {{ $classroom->classroom }} </td>
                                <th> Class Level </th>
                                <td> {{ $classroom->classLevel->classlevel }} </td>
                            </tr>
                            @if($exam)
                                <tr>
                                    <th> Class Average </th>
                                    <td> {{ $exam->class_average }}</td>
                                    <th> Number of Students (Out of) </th>
                                    <td>{{ $exam->class_size }}</td>
                                </tr>
                                <tr>
                                    <th> Perfect Score </th>
                                    <td> {{ $exam->exam_perfect_score }}</td>
                                    <th> Academic Term </th>
                                    <td>{{ $exam->academic_term }}</td>
                                </tr>
                            @endif
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
                        <span class="caption-subject font-green bold uppercase">List of Students Terminal Class Positions.</span>
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
                            @if(!empty($results))
                                <?php $i = 1; ?>
                                @foreach($results as $result)
                                    <tr class="odd gradeX">
                                        <td class="center">{{$i++}}</td>
                                        <td>{{ $result->student_no }}</td>
                                        <td>
                                            <a target="_blank" href="{{ url('/students/view/'.$hashIds->encode($result->student_id)) }}" class="btn btn-link">
                                                {{ $result->full_name }}
                                            </a>
                                        </td>
                                        <td>{{ $result->gender }}</td>
                                        <td>{{ $result->student_sum_total }}</td>
                                        <td>{{ Assessment::formatPosition($result->class_position) }}</td>
                                        <td>
                                            <a target="_blank" href="{{ url('/exams/student-terminal-result/'.$hashIds->encode($result->student_id).'/'.$hashIds->encode($result->academic_term_id)) }}" class="btn btn-link">
                                                <span class="fa fa-eye"></span> Proceed
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

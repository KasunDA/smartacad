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
                                    @foreach($subjects as $subject)
                                        <th>{{ str_replace('_', ' ',$subject) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($examsStudents))
                                @foreach($examsStudents as $view)
                                    <?php $records = (array) $view; ?>
                                    <tr>
                                        @foreach($subjects as $subject)
                                            <td>{{ $records[$subject] }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @else
                                <tr><th colspan="5">No Record Found</th></tr>
                            @endif
                            </tbody>
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

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
                    <div class="pull-right">
                        <a target="_blank" href="{{ url("/exams/broad-sheet/{$hashIds->encode($classRoom->classroom_id)}/{$hashIds->encode($academicYear->academic_year_id)}/show") }}" class="btn btn-warning btn-xs sbold">
                            <i class="fa fa-file"></i> View
                        </a>
                        <a target="_blank" href="{{ url("/exams/broad-sheet/{$hashIds->encode($classRoom->classroom_id)}/{$hashIds->encode($academicYear->academic_year_id)}") }}" class="btn btn-default btn-xs sbold">
                            <i class="fa fa-file"></i> PDF
                        </a>
                        <a target="_blank" href="{{ url("/exams/broad-sheet/{$hashIds->encode($classRoom->classroom_id)}/{$hashIds->encode($academicYear->academic_year_id)}/download") }}" class="btn btn-primary btn-xs sbold">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-scrollable">
                        <table class="table table-hover table-striped">
                            <tr>
                                <th> Class Level </th>
                                <td> {{ $classRoom->classLevel->classlevel }} </td>
                                <th> Class Room </th>
                                <td> {{ $classRoom->classroom }} </td>
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
        <div class="col-md-12 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-gears font-green"></i>
                        <span class="caption-subject font-green bold uppercase">List of Students, Terminal Subjects.</span>
                    </div>
                    <div class="pull-right">
                        <a target="_blank" href="{{ url("/exams/broad-sheet/{$hashIds->encode($classRoom->classroom_id)}/{$hashIds->encode($academicYear->academic_year_id)}/show") }}" class="btn btn-warning btn-xs sbold">
                            <i class="fa fa-file"></i> View
                        </a>
                        <a target="_blank" href="{{ url("/exams/broad-sheet/{$hashIds->encode($classRoom->classroom_id)}/{$hashIds->encode($academicYear->academic_year_id)}") }}" class="btn btn-default btn-xs sbold">
                            <i class="fa fa-file"></i> PDF
                        </a>
                        <a target="_blank" href="{{ url("/exams/broad-sheet/{$hashIds->encode($classRoom->classroom_id)}/{$hashIds->encode($academicYear->academic_year_id)}/download") }}" class="btn btn-primary btn-xs sbold">
                            <i class="fa fa-download"></i> Download
                        </a>
                    </div>
                </div>
                <div id="error-div"></div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th></th>
                                    @foreach($subjects as $subject)
                                        @if($loop->iteration == 4)
                                            <th>Subjects</th>
                                        @endif
                                        @if($loop->iteration > 4)
                                            <th colspan="4" class="text-center">{{ str_replace('_', ' ',$subject) }}</th>
                                        @endif
                                    @endforeach
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    @for($i = 0; $i < count($subjects) - 4; $i++)
                                        <th>1st</th>
                                        <th>2nd</th>
                                        <th>3rd</th>
                                        <th>&Sigma;</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($examsStudents as $student)
                                    <?php $first = $second = $third = null; ?>
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{!! $student[0]->Name !!}</td>
                                        @foreach($student as $exam)
                                            <?php if($exam->term_type_id == 1) $first = $exam; ?>
                                            <?php if($exam->term_type_id == 2) $second = $exam; ?>
                                            <?php if($exam->term_type_id == 3) $third = $exam; ?>
                                        @endforeach
                                        @foreach($subjects as $subject)
                                            @if($loop->iteration > 4)
                                                <td>{!! !empty($first) && $first->{$subject} ? number_format($first->{$subject}, 1) : LabelHelper::danger() !!}</td>
                                                <td>{!! !empty($second) && $second->{$subject} ? number_format($second->{$subject}, 1) : LabelHelper::danger() !!}</td>
                                                <td>{!! !empty($third) && $third->{$subject} ? number_format($third->{$subject}, 1) : LabelHelper::danger() !!}</td>
                                                <td>{{ number_format(($first->{$subject} ?? 0) + ($second->{$subject} ?? 0) + ($third->{$subject} ?? 0), 1) }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr><th colspan="5" class="text-center">No Record Found</th></tr>
                                @endforelse
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

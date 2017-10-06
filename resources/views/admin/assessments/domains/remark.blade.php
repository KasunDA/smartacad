@extends('admin.layout.default')

@section('title', 'Remark Assessment')

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
    <h3 class="page">Students Remark Assessments</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-comments font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            List of Students in {{ $classroom->classroom }} for {{$term->academic_term}} Academic Term
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    {!! Form::open([
                            'method'=>'POST',
                            'class'=>'form',
                            'role'=>'form',
                        ])
                    !!}
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="text-center uppercase">Student Information</th>
                                        <th colspan="2" class="text-center uppercase">Remarks</th>
                                    </tr>
                                    <tr>
                                        <th width="2%">#</th>
                                        <th width="10%">No.</th>
                                        <th width="24%">Name</th>
                                        <th width="10%">Gender</th>
                                        <th width="27%">Class Teacher</th>
                                        <th width="27%">Principal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if($studentClasses)
                                    <?php $i = 1; ?>
                                    {!! Form::hidden('academic_term_id', $term->academic_term_id) !!}
                                    @foreach($studentClasses as $studentClass)
                                        @if($studentClass->student()->first() and $studentClass->student()->first()->status_id == 1)
                                            {!! Form::hidden('student_id[]', $studentClass->student()->first()->student_id) !!}

                                            <?php $remark = $studentClass->student()->first()->remarks()->where('academic_term_id', $term->academic_term_id);?>
                                            @if($remark->count() > 0)
                                                {!! Form::hidden('remark_id[]', $remark->first()->remark_id) !!}
                                                <tr class="odd gradeX">
                                                    <td class="center">{{$i++}}</td>
                                                    <td>{{ $studentClass->student()->first()->student_no }}</td>
                                                    <td>{{ $studentClass->student()->first()->fullNames() }}</td>
                                                    <td>{{ $studentClass->student()->first()->gender }}</td>
                                                    <td>{!! Form::textarea('class_teacher[]', $remark->first()->class_teacher, ['class'=>'form-control', 'placeholder'=>'Class Teacher\'s Remark', 'rows'=>'3']) !!}</td>
                                                    <td>{!! Form::textarea('principal[]', $remark->first()->principal, ['class'=>'form-control', 'placeholder'=>'Principal\'s Remark', 'rows'=>'3']) !!}</td>
                                                </tr>
                                            @else
                                                {!! Form::hidden('remark_id[]', -1) !!}
                                                <tr class="odd gradeX">
                                                    <td class="center">{{$i++}}</td>
                                                    <td>{{ $studentClass->student()->first()->student_no }}</td>
                                                    <td>{{ $studentClass->student()->first()->fullNames() }}</td>
                                                    <td>{{ $studentClass->student()->first()->gender }}</td>
                                                    <td>{!! Form::textarea('class_teacher[]', '', ['class'=>'form-control', 'placeholder'=>'Class Teacher\'s Remark', 'rows'=>'3']) !!}</td>
                                                    <td>{!! Form::textarea('principal[]', '', ['class'=>'form-control', 'placeholder'=>'Principal\'s Remark', 'rows'=>'3']) !!}</td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="2%">#</th>
                                        <th width="10%">No.</th>
                                        <th width="24%">Name</th>
                                        <th width="10%">Gender</th>
                                        <th width="27%">Class Teacher</th>
                                        <th width="27%">Principal</th>
                                    </tr>
                                </tfoot>
                            </table>
                            <div class="form-actions noborder">
                                <button type="submit" class="btn blue pull-right">Save Scores</button>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- END SAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection

    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/domains"]');
        });
    </script>
@endsection

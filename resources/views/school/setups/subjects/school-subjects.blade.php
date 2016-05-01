@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'School Subjects')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/school-subjects') }}">School Subjects</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> School Subjects</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">School Subjects</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green add_school_subjects"> Add New
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form'
                            ])
                        !!}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-actions" id="school_subjects_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">School Subjects</th>
                                        <th style="width: 20%;">Subjects Abbr</th>
                                        <th style="width: 20%;">Subjects Group</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </thead>
                                    @if(count($school_subjects) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($school_subjects as $school_subject)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('school_subject[]', $school_subject->school_subject, ['placeholder'=>'School Subjects', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('school_subject_id[]', $school_subject->school_subject_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('school_subject_abbr[]', $school_subject->school_subject_abbr, ['placeholder'=>'Subject Abbr.', 'class'=>'form-control']) !!}</td>
                                                <td>{!! Form::select('subject_group_id[]', $subject_groups, $school_subject->subject_group_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_school_subjects">
                                                        <span class="fa fa-trash-o"></span> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    @else
                                        <tr>
                                            <td class="text-center">1</td>
                                            <td>
                                                {!! Form::text('school_subject[]', '', ['placeholder'=>'School Subjects', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('school_subject_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('school_subject_abbr[]', '', ['placeholder'=>'Subject Abbr.', 'class'=>'form-control']) !!}</td>
                                            <td>{!! Form::select('subject_group_id[]', $subject_groups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">School Subjects</th>
                                        <th style="width: 20%;">Subjects Abbr</th>
                                        <th style="width: 20%;">Subjects Group</th>
                                        <th style="width: 20%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="form-actions noborder">
                                    <button type="submit" class="btn blue pull-right">Submit</button>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection


@section('layout-script')
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/setups/subjects/school-subjects.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/school-subjects"]');
        });
    </script>
@endsection

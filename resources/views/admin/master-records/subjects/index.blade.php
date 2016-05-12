@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/jquery-multi-select/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <style type="text/css">
        .multi-select-subjects{
            width: 800px;
            /*min-height: 500px;*/
        }
    </style>
@endsection

@section('title', 'School Subjects')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/academic-years') }}">School Subjects</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">School Subjects</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-academic_year">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Subjects</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open([
                            'method'=>'POST',
                            'class'=>'form-horizontal form-row-seperated',
                        ])
                    !!}
                        <div class="form-body">
                            <div class="form-group last">
                                <label class="control-label col-md-2">Select Subjects</label>
                                <div class="col-md-10">
                                    <select multiple="multiple" class="multi-select" id="subject_multi_select" name="subject_id[]">
                                        @foreach($subject_groups as $subject_group)
                                            <optgroup label="{{ $subject_group->subject_group }}">
                                                @if($subject_group->subjects()->count() > 0)
                                                    @foreach($subject_group->subjects()->orderBy('subject')->get() as $subject )
                                                        @if(in_array($subject->subject_id, $mySchool->subjects()->lists('schools_subjects.subject_id')->toArray()))
                                                            <option selected value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
                                                        @else
                                                            <option value="{{ $subject->subject_id }}">{{ $subject->subject }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn green">
                                        <i class="fa fa-check"></i> Submit</button>
                                    <button type="button" class="btn grey-salsa btn-outline">Cancel</button>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/master-records/school-subject.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/school-subjects"]');
            ComponentsDropdowns.init();
        });
    </script>
@endsection

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
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/school-subjects') }}">Manage School Subjects</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-academic_year">Manage School Subjects</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        {{--<div class="caption">--}}
                            {{--<i class="fa fa-book font-green"></i>--}}
                            {{--<span class="caption-subject font-green bold uppercase">Manage School Subjects</span>--}}
                        {{--</div>--}}
                        <ul class="nav nav-pills">
                            <li class="active">
                                <a href="#add_subject" data-toggle="tab"> Add / Remove Subjects </a>
                            </li>
                            <li>
                                <a href="#rename_subject" data-toggle="tab"> Subjects Rename (Alias) </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane active" id="add_subject">
                                <div class="alert alert-info"> Move Subjects From Left To Right To Add Them and Right To Left To Remove</div>
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
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn blue pull-right">
                                            <i class="fa fa-check"></i> Submit</button>
                                    </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane" id="rename_subject">
                                <div class="alert alert-info"> Rename an Assigned Subject if it does not conform with the School Standard</div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-1">
                                        <form method="POST" action="/school-subjects/rename" class="form" role="form">
                                            {!! csrf_field() !!}
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-actions" id="academic_term_table">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 1%;">s/no</th>
                                                        <th style="width: 24%;">Subject Name</th>
                                                        <th style="width: 24%;">New Subject Name (Alias)</th>
                                                    </tr>
                                                    </thead>
                                                    @if(count($mySchool->subjects()->get()) > 0)
                                                        <tbody>
                                                        <?php $i = 1; ?>
                                                        @foreach($mySchool->subjects()->orderBy('subject')->get() as $subject)
                                                            <tr>
                                                                <td class="text-center">{{$i++}} </td>
                                                                <td>
                                                                    <div class="list-item-content">
                                                                        <span class="big"> {{$subject->subject}} </span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    {!! Form::text('subject_alias[]', $subject->pivot->subject_alias, ['placeholder'=>'Subject Alias', 'class'=>'form-control']) !!}
                                                                    {!! Form::hidden('subject_id[]', $subject->subject_id, ['class'=>'form-control']) !!}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    @endif
                                                    <tfoot>
                                                    <tr>
                                                        <th style="width: 1%;">s/no</th>
                                                        <th style="width: 24%;">Subject Name</th>
                                                        <th style="width: 24%;">New Subject Name (Alias)</th>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                                <div class="form-actions noborder">
                                                    <button type="submit" class="btn blue pull-right">
                                                        <i class="fa fa-edit"></i> Rename</button>
                                                    </button>
                                                </div>
                                            </div>
                                        {!! Form::close() !!}
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
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js') }}" type="text/javascript"></script>
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

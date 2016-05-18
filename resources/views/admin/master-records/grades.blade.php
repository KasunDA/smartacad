@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Grades')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/grades') }}">Grades</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Grades</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Grades Setup</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green add_grade"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="grade_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Grades</th>
                                        <th style="width: 20%;">Class Group</th>
                                        <th style="width: 13%;">Grades Abbr.</th>
                                        <th style="width: 13%;">Upper Bound</th>
                                        <th style="width: 13%;">Lower Bound</th>
                                        <th style="width: 8%;">Actions</th>
                                    </tr>
                                    </thead>
                                    @if(count($classgroups) > 1)
                                        @if(count($grades) > 0)
                                            <tbody>
                                            <?php $i = 1; ?>
                                            @foreach($grades as $grade)
                                                <tr>
                                                    <td class="text-center">{{$i++}} </td>
                                                    <td>
                                                        {!! Form::text('grade[]', $grade->grade, ['placeholder'=>'Grade', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('grade_id[]', $grade->grade_id, ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::select('classgroup_id[]', $classgroups, $grade->classgroup_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('grade_abbr[]', $grade->grade_abbr, ['placeholder'=>'Grade Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('upper_bound[]', $grade->upper_bound, ['placeholder'=>'Upper Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('lower_bound[]', $grade->lower_bound, ['placeholder'=>'Lower Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_grade">
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
                                                    {!! Form::text('grade[]', '', ['placeholder'=>'Grade', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('grade_id[]', '-1', ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::select('classgroup_id[]', $classgroups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('grade_abbr[]', '', ['placeholder'=>'Grade Abbr.', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('upper_bound[]', '', ['placeholder'=>'Upper Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('lower_bound[]', '', ['placeholder'=>'Lower Bound', 'class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr><td colspan="7" class="text-center"><label class="label label-danger"><strong>An Academic Years Record Must Be Inserted Before Inserting Grade</strong></label></td></tr>
                                    @endif
                                    <tfoot>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Grades</th>
                                        <th style="width: 20%;">Class Group</th>
                                        <th style="width: 13%;">Grades Abbr.</th>
                                        <th style="width: 13%;">Upper Bound</th>
                                        <th style="width: 13%;">Lower Bound</th>
                                        <th style="width: 8%;">Actions</th>
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


@section('page-level-js')
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/master-records/grade.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/grades"]');
            setTableData($('#grade_table')).init();
        });
    </script>
@endsection

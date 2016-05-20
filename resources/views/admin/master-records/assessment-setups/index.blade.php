@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Assessment Setups')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/assessment-setups') }}">Assessment Setups</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Assessment Setups</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Assessment Setups</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green add_assessment_setup"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="assessment_setup_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Number of Assessments</th>
                                        <th style="width: 30%;">Class Group</th>
                                        <th style="width: 30%;">Academic Term</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </thead>
                                        @if(count($assessment_setups) > 0)
                                            <tbody>
                                            <?php $j = 1; ?>
                                            @foreach($assessment_setups as $assessment_setup)
                                                <tr>
                                                    <td class="text-center">{{$j++}} </td>
                                                    <td>
                                                        <select name="assessment_no[]" class="form-control" required>
                                                            <option value="">Select No.</option>
                                                            @for($i=1; $i < 11; $i++)
                                                                @if($assessment_setup->assessment_no == $i)
                                                                    <option selected value="{{ $i }}">{{ $i }}</option>
                                                                @else
                                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                                @endif
                                                            @endfor
                                                        </select>
                                                        {!! Form::hidden('assessment_setup_id[]', $assessment_setup->assessment_setup_id, ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::select('classgroup_id[]', $classgroups, $assessment_setup->classgroup_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::select('academic_term_id[]', $academic_terms, $assessment_setup->academic_term_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>
                                                        <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_assessment_setup">
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
                                                    <select name="assessment_no[]" class="form-control" required>
                                                        <option value="">Select No.</option>
                                                        @for($i=1; $i < 11; $i++)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                    {!! Form::hidden('assessment_setup_id[]', '-1', ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::select('classgroup_id[]', $classgroups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('academic_term_id[]', $academic_terms, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    <tfoot>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Number of Assessments</th>
                                        <th style="width: 30%;">Class Group</th>
                                        <th style="width: 30%;">Academic Term</th>
                                        <th style="width: 10%;">Actions</th>
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
    <script src="{{ asset('assets/custom/js/master-records/assessment-setup.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/assessment-setups"]');
            setTableData($('#assessment_setup_table')).init();
        });
    </script>
@endsection

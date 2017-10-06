@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Academic Terms')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/academic-terms') }}">Academic Terms</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Academic Terms</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 col-xs-12 col-md-offset-2 margin-bottom-10">
            <form method="post" action="/academic-terms/academic-years" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Academic Year</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="academic_year_id" id="academic_year_id">
                                @foreach($academic_years as $key => $value)
                                    @if($academic_year && $academic_year->academic_year_id === $key)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit">Filter</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-10">
                        <h3 class="text-center">Academic Terms in:
                            <span class="text-primary">{{ ($academic_year) ? $academic_year->academic_year : 'All' }}</span> Academic Year
                        </h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">Academic Terms</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn green btn-sm add_academic_term"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="academic_term_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Academic Terms</th>
                                        <th style="width: 13%;">Status</th>
                                        <th style="width: 15%;">Academic Year</th>
                                        <th style="width: 13%;">Type</th>
                                        <th style="width: 12%;">Term Begins</th>
                                        <th style="width: 12%;">Term Ends</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </thead>
                                    @if(count($academic_years) > 1)
                                        @if(count($academic_terms) > 0)
                                            <tbody>
                                            <?php $i = 1; ?>
                                            @foreach($academic_terms as $academic_term)
                                                <tr>
                                                    <td class="text-center">{{$i++}} </td>
                                                    <td>
                                                        {!! Form::text('academic_term[]', $academic_term->academic_term, ['placeholder'=>'Academic Term', 'class'=>'form-control', 'required'=>'required']) !!}
                                                        {!! Form::hidden('academic_term_id[]', $academic_term->academic_term_id, ['class'=>'form-control']) !!}
                                                    </td>
                                                    <td>{!! Form::select('status[]', [''=>'- Term Status- ', 1=>'Active', 2=>'Inactive'], $academic_term->status, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::select('academic_year_id[]', $academic_years, $academic_term->academic_year_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::select('term_type_id[]', [''=>'Term Type', 1=>'First Term', 2=>'Second Term', 3=>'Third Term'], $academic_term->term_type_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                    <td>{!! Form::text('term_begins[]', $academic_term->term_begins->format('Y-m-d'), ['placeholder'=>'Term Begins', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                                    <td>{!! Form::text('term_ends[]', $academic_term->term_ends->format('Y-m-d'), ['placeholder'=>'Term Ends', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                                    <td>
                                                        <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$academic_term->academic_term}}" data-title="Delete Confirmation"
                                                                 data-message="Are you sure you want to delete <b>{{$academic_term->academic_term}}?</b>"
                                                                 data-action="/academic-terms/delete/{{$academic_term->academic_term_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                    {!! Form::text('academic_term[]', '', ['placeholder'=>'Academic Term', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('academic_term_id[]', '-1', ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::select('status[]', [''=>'- Term Status -', 1=>'Active', 2=>'Inactive'],'', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('academic_year_id[]', $academic_years, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('term_type_id[]', [''=>'Term Type', 1=>'First Term', 2=>'Second Term', 3=>'Third Term'], '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('term_begins[]', '', ['placeholder'=>'Term Begins', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                                <td>{!! Form::text('term_ends[]', '', ['placeholder'=>'Term Ends', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-xs">
                                                        <span class="fa fa-times"></span> Remove
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @else
                                        <tr><td colspan="8" class="text-center"><label class="label label-danger"><strong>An Academic Years Record Must Be Inserted Before Inserting Academic Term</strong></label></td></tr>
                                    @endif
                                    <tfoot>
                                    <tr>
                                        <th style="width: 1%;">s/no</th>
                                        <th style="width: 24%;">Academic Terms</th>
                                        <th style="width: 13%;">Status</th>
                                        <th style="width: 15%;">Academic Year</th>
                                        <th style="width: 13%;">Type</th>
                                        <th style="width: 12%;">Term Begins</th>
                                        <th style="width: 12%;">Term Ends</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                </table>
                                <div class="col-md-12 margin-bottom-10 pull-left">
                                    <div class="btn-group">
                                        <button class="btn green btn-sm add_academic_term"> Add New
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
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
    <script src="{{ asset('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection

@section('layout-script')
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/master-records/academic-term.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/academic-terms"]');
            setTableData($('#academic_term_table')).init();
        });
    </script>
@endsection

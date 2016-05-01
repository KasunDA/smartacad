@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Academic Terms')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/academic-terms') }}">Academic Terms</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Academic Terms</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
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
                                <button class="btn green add_academic_term"> Add New
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
                                                <td>{!! Form::select('status[]', [''=>'Term Status', 1=>'Active', 2=>'Inactive'], $academic_term->status, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('academic_year_id[]', $academic_years, $academic_term->academic_year_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::select('term_type_id[]', [''=>'Term Type', 1=>'First Term', 2=>'Second Term', 3=>'Third Term'], $academic_term->term_type_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>{!! Form::text('term_begins[]', $academic_term->term_begins, ['placeholder'=>'Term Begins', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                                <td>{!! Form::text('term_ends[]', $academic_term->term_ends, ['placeholder'=>'Term Ends', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_academic_term">
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
                                            <td>{!! Form::select('status[]', [''=>'Term Status', 1=>'Active', 2=>'Inactive'],'', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('academic_year_id[]', $academic_years, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::select('term_type_id[]', [''=>'Term Type', 1=>'First Term', 2=>'Second Term', 3=>'Third Term'], '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>{!! Form::text('term_begins[]', '', ['placeholder'=>'Term Begins', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
                                            <td>{!! Form::text('term_ends[]', '', ['placeholder'=>'Term Ends', 'class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd']) !!}</td>
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
@endsection

@section('layout-script')
    <script src="{{ asset('assets/pages/scripts/components-date-time-pickers.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/custom/js/master-records/academic-term.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/academic-terms"]');
        });
    </script>
@endsection

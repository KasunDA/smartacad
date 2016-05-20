@extends('admin.layout.default')

@section('page-level-css')
        <!-- BEGIN PAGE LEVEL PLUGINS -->
<link href="{{ asset('assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Assessment Setups Details')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/assessment-setups/details') }}">Assessment Setups Details</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Assessment Setups</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-10 col-md-offset-1 margin-bottom-10">
            <form method="post" action="/assessment-setups/terms" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Academic Term</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="academic_term_id" id="academic_term_id">
                                @foreach($academic_terms as $key => $value)
                                    @if($academic_term && $academic_term->academic_term_id === $key)
                                        <option selected value="{{$key}}">{{$value}}</option>
                                    @else
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary pull-right" type="submit">Filter</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-10">
                        <h3 class="text-center">Assessments Details in:
                            <span class="text-danger">{{ ($academic_term) ? $academic_term->academic_term : 'All' }}</span> Academic Year</h3>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">
                            Assessment Setups Details: Note Columns With <span class="label label-danger">*</span> Must Be Filled
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form'
                            ])
                        !!}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-actions" id="assessment_detail_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%;">#</th>
                                            <th style="width: 17%;">Weight Point <span class="label label-danger">*</span></th>
                                            <th style="width: 20%;">Assessment No. <span class="label label-danger">*</span></th>
                                            <th style="width: 20%;">Percentage (%) <span class="label label-danger">*</span></th>
                                            <th style="width: 22%;">Description <span class="label label-danger">*</span></th>
                                            <th style="width: 15%;">Submission Date</th>
                                            <th style="width: 5%;">Actions</th>
                                        </tr>
                                    </thead>
                                    @if(count($assessment_setups) > 0)
                                        <tbody>

                                        @foreach($assessment_setups as $assessment_setup)
                                            <tr>
                                                <th colspan="4" class="text-center">{{ $assessment_setup->classGroup()->first()->classgroup }}</th>
                                                <th colspan="3" class="text-center">{{ $academic_term->academic_term }}</th>
                                            </tr>
                                            <?php
                                                $details = $assessment_setup->assessmentSetupDetails()->get();
                                                $no = $assessment_setup->assessment_no;
                                                $count = $details->count();
                                                $diff = $no - $count;
//                                                    dd($diff);
                                            ?>
                                            <?php $j = 0; ?>
                                            @if($count > 0)
                                                @foreach($details as $detail)
                                                    <tr>
                                                        <td class="text-center">{{++$j}} </td>
                                                        <td>
                                                            <select name="weight_point[]" class="form-control" required>
                                                                <option value="">Select W.P</option>
                                                                @for($i=5; $i <= 100; $i+=5)
                                                                    {!! ($i == $detail->weight_point) ? '<option selected value="'.$i.'">'.$i.'</option>' : '<option value="'.$i.'">'.$i.'</option>' !!}
                                                                @endfor
                                                            </select>
                                                            {!! Form::hidden('assessment_setup_detail_id[]', $detail->assessment_setup_detail_id, ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::text('number[]', $detail->number, ['class'=>'form-control', 'required'=>'required', 'readonly'=>'readonly']) !!}</td>
                                                        <td>
                                                            <select name="percentage[]" class="form-control" required>
                                                                <option value="">Select C.A (%)</option>
                                                                @for($g=5; $g <= 100; $g+=5)
                                                                    {!! ($g == $detail->percentage) ? '<option selected value="'.$g.'">'.$g.' %</option>' : '<option value="'.$g.'">'.$g.' %</option>' !!}
                                                                @endfor
                                                            </select>
                                                            {!! Form::hidden('assessment_setup_id[]', $detail->assessment_setup_id, ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::textarea('description[]', $detail->description, ['class'=>'form-control', 'required'=>'required', 'rows'=>'3']) !!}</td>
                                                        <td>{!! Form::text('submission_date[]', $detail->submission_date->format('Y-m-d'), ['class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd', 'required'=>'required']) !!}</td>
                                                        <td>
                                                            @if($count == ($j))
                                                                <button class="btn btn-danger btn-rounded btn-condensed btn-sm delete_assessment_detail" value="{{ $assessment_setup->classGroup()->first()->classgroup_id }}">
                                                                    <span class="fa fa-trash-o"></span> Delete
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            @if( $diff > 0)
                                                @for($a=1; $a <= $diff; $a++)
                                                    <tr>
                                                        <td class="text-center">{{$a + $j}} </td>
                                                        <td>
                                                            <select name="weight_point[]" class="form-control" required>
                                                                <option value="">Select W.P</option>
                                                                @for($i=5; $i <= 100; $i+=5)
                                                                    <option value="{{$i}}">{{$i}}</option>
                                                                @endfor
                                                            </select>
                                                            {!! Form::hidden('assessment_setup_detail_id[]', '-1', ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::text('number[]', ($a + $j), ['class'=>'form-control', 'required'=>'required', 'readonly'=>'readonly']) !!}</td>
                                                        <td>
                                                            <select name="percentage[]" class="form-control" required>
                                                                <option value="">Select C.A (%)</option>
                                                                @for($g=5; $g <= 100; $g+=5)
                                                                    <option value="{{$g}}">{{$g}} %</option>
                                                                @endfor
                                                            </select>
                                                            {!! Form::hidden('assessment_setup_id[]', $assessment_setup->assessment_setup_id, ['class'=>'form-control']) !!}
                                                        </td>
                                                        <td>{!! Form::textarea('description[]', '', ['class'=>'form-control', 'required'=>'required', 'rows'=>'3']) !!}</td>
                                                        <td>{!! Form::text('submission_date[]', '', ['class'=>'form-control date-picker', 'data-date-format'=>'yyyy-mm-dd', 'required'=>'required']) !!}</td>
                                                        <td></td>
                                                    </tr>
                                                @endfor
                                            @endif
                                        @endforeach
                                        </tbody>
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <label class="label label-danger">
                                                    <strong>An Assessment Setup Record for {{$academic_term->academic_term}} Must Be Inserted Before Inserting Assessment Details</strong>
                                                </label>
                                            </td>
                                        </tr>
                                    @endif
                                    <tfoot>
                                        <tr>
                                            <th style="width: 1%;">#</th>
                                            <th style="width: 17%;">Weight Point <span class="label label-danger">*</span></th>
                                            <th style="width: 20%;">Assessment No. <span class="label label-danger">*</span></th>
                                            <th style="width: 20%;">Percentage (%) <span class="label label-danger">*</span></th>
                                            <th style="width: 22%;">Description <span class="label label-danger">*</span></th>
                                            <th style="width: 15%;">Submission Date</th>
                                            <th style="width: 5%;">Actions</th>
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
            setTabActive('[href="/assessment-setups/details"]');
        });
    </script>
@endsection

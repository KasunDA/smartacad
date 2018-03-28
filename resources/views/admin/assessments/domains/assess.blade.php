@extends('admin.layout.default')

@section('title', 'Affective Domains Student Assessment')

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
    <h3 class="page">Students Affective Domain Assessments</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-10 margin-bottom-10">
            <!-- BEGIN SAMPLE TABLE PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-check font-green"></i>
                        <span class="caption-subject font-green bold">
                            {{ $student->fullNames() }} Assessment Form for {{$term->academic_term}} Academic Term
                        </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="table-responsive">
                        {!! Form::open([
                                'method'=>'POST',
                                'class'=>'form',
                                'role'=>'form'
                            ])
                        !!}
                            <div class="form-body">
                                @if($domains->count() > 0)
                                    <?php $i=1;?>
                                    {!! Form::hidden('domain_assessment_id', $assessment->domain_assessment_id) !!}
                                    @foreach($domains as $domain)
                                        <div class="row margin-bottom-12">
                                            <div class="form-group form-md-line-input">
                                                <label class="col-lg-5 col-md-9 control-label text-right bold">{{ $domain->domain }}</label>
                                                <div class="col-lg-7 col-md-3">
                                                    @if($assessment->domainDetails()->count() > 0)
                                                        <?php $detail = $assessment->domainDetails()->where('domain_id', $domain->domain_id)->first();?>

                                                        {!! Form::hidden('domain_detail_id[]', $detail->domain_detail_id) !!}
                                                        <div class="radio-list">
                                                            <label class="radio-inline">
                                                                <input type="radio" value="5" {{($detail->option == 5) ? 'checked' : ''}} required="required" name="optionsRadios{{ $domain->domain_id }}"/> 5
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="4" {{($detail->option == 4) ? 'checked' : ''}} required="required" name="optionsRadios{{ $domain->domain_id }}"/> 4
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="3" {{($detail->option == 3) ? 'checked' : ''}} required="required" name="optionsRadios{{ $domain->domain_id }}"/> 3
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="2" {{($detail->option == 2) ? 'checked' : ''}} required="required" name="optionsRadios{{ $domain->domain_id }}"/> 2
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="1" {{($detail->option == 1) ? 'checked' : ''}} required="required" name="optionsRadios{{ $domain->domain_id }}"/> 1
                                                            </label>
                                                        </div>
                                                    @else
                                                        {!! Form::hidden('domain_detail_id[]', -1) !!}
                                                        <div class="radio-list">
                                                            <label class="radio-inline">
                                                                <input type="radio" value="5" required="required" name="optionsRadios{{ $domain->domain_id }}"/> 5
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="4" required="required" name="optionsRadios{{ $domain->domain_id }}"/> 4
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="3" required="required" name="optionsRadios{{ $domain->domain_id }}"/> 3
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="2" required="required" name="optionsRadios{{ $domain->domain_id }}"/> 2
                                                            </label>
                                                            <label class="radio-inline">
                                                                <input type="radio" value="1" required="required" name="optionsRadios{{ $domain->domain_id }}"/> 1
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="form-actions noborder">
                                <button type="submit" class="btn blue pull-right">
                                    <i class="fa fa-save"></i> Submit
                                </button>
                            </div>
                        {!! Form::close() !!}
                    </div>
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

@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-editable/bootstrap-editable/css/bootstrap-editable.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'Messaging')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <i class="fa fa-chevron-right"></i>
    </li>
    <li>
        <a href="{{ url('/messages') }}">Messaging</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"><i class="fa fa-envelope fa-2x"></i>  Messaging (S.M.S)</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet light bordered">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-pills">
                            <li class="active">
                                <a href="#sponsors" data-toggle="tab"><i class="fa fa-user"></i> Individual / <i class="fa fa-users"></i>  Group Sponsors</a>
                            </li>
                            <li>
                                <a class="btn btn-outline sbold" data-toggle="modal" href="#sponsor" id="all_sposnors"><i class="fa fa-gears"></i> S.M.S All Sponsors</a>
                            </li>
                            <li>
                                <a href="#staffs" data-toggle="tab"><i class="fa fa-user"></i> Individual / <i class="fa fa-users"></i>  Selected Staffs</a>
                            </li>
                            <li>
                                <a class="btn btn-outline sbold" data-toggle="modal" href="#staff" id="all_staffs"><i class="fa fa-chrome"></i> S.M.S All Staffs</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body form">
                        <div class="tab-content">
                            <div class="tab-pane active" id="sponsors">
                                <div class="alert alert-info"> Search for <strong>Sponsors By Student In Class Level / Room </strong>  For An <strong> Academic Term</strong></div>
                                {!! Form::open([
                                        'method'=>'POST',
                                        'class'=>'form-horizontal',
                                        'id' => 'search_student_form'
                                    ])
                                !!}
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Academic Year <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('academic_year_id', $academic_years,  AcademicYear::activeYear()->academic_year_id, ['class'=>'form-control', 'id'=>'academic_year_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label">Academic Term <span class="text-danger">*</span></label>
                                                    {!! Form::select('academic_term_id', AcademicTerm::where('academic_year_id', AcademicTerm::activeTerm()->academic_year_id)
                                                    ->orderBy('term_type_id')->lists('academic_term', 'academic_term_id')->prepend('Select Academic Term', ''),
                                                    AcademicTerm::activeTerm()->academic_term_id, ['class'=>'form-control', 'id'=>'academic_term_id', 'required'=>'required']) !!}
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-md-offset-1">
                                                <div class="form-group">
                                                    <label class="control-label">Class Level <span class="text-danger">*</span></label>
                                                    <div>
                                                        {!! Form::select('classlevel_id', $classlevels, old('classlevel_id'), ['class'=>'form-control', 'id'=>'classlevel_id', 'required'=>'required']) !!}
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label">Class Room </label>
                                                    {!! Form::select('classroom_id', [], '', ['class'=>'form-control', 'id'=>'classroom_id']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn blue pull-right">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                {!! Form::close() !!}
                                <div class="row">
                                    <div class="col-md-12">
                                        <form action="#" role="form" method="post" class="form" id="message_sponsor_form">
                                            {{ csrf_field() }}
                                            <div class="portlet-body">
                                                <div class="row">
                                                    <table class="table table-striped table-bordered table-hover table-checkable" id="search_student_datatable">

                                                    </table>
                                                </div>
                                            </div>
                                            <div class="form-actions noborder hide" id="search_student_button">
                                                <button type="submit" class="btn btn-primary pull-right">
                                                    <i class="fa fa-envelope"></i> Message Marked Sponsors
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="staffs">
                                <div class="alert alert-info"> Search by <strong>Academic Term</strong> and <strong>Class Room</strong> To View Subjects</div>
                                <form action="#" role="form" method="post" class="form" id="message_staffs_form">
                                    {{ csrf_field() }}
                                    <div class="form-body">
                                        <div class="portlet-body">
                                            <div class="table-container">
                                                <div class="table-actions-wrapper">
                                                    <span> </span>
                                                    Search: <input type="text" class="form-control input-inline input-small input-sm" id="search_param"/><br>
                                                </div>
                                                <table class="table table-striped table-bordered table-hover table-checkable" id="staff_tabledata">
                                                    <thead>
                                                    <tr role="row" class="heading">
                                                        <th width="2%"><input type="checkbox" class="group-checkable"> </th>
                                                        <th width="2%">#</th>
                                                        <th width="32%">Full Name</th>
                                                        <th width="12%">Phone No.</th>
                                                        <th width="25%">Email</th>
                                                        <th width="13%">Gender</th>
                                                        <th width="7%">View</th>
                                                        <th width="7%">Send</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr role="row" class="heading">
                                                        <th width="2%"><input type="checkbox" class="group-checkable"> </th>
                                                        <th width="2%">#</th>
                                                        <th width="32%">Full Name</th>
                                                        <th width="12%">Phone No.</th>
                                                        <th width="25%">Email</th>
                                                        <th width="13%">Gender</th>
                                                        <th width="7%">View</th>
                                                        <th width="7%">Send</th>
                                                    </tr>
                                                    </tfoot>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions noborder">
                                        <button type="submit" class="btn blue pull-right">
                                            <i class="fa fa-envelope"></i> Message Marked Staffs
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->

    <div class="modal fade" id="message_selected_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <form action="/messages/send" role="form" method="post" class="form">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" id="modal_title"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Message Content:</label>
                            <textarea class="form-control input-lg" rows="4" required placeholder="Message Content" name="message" id="message_content"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {!! Form::hidden('phone_no', '', ['id'=>'phone_nos']) !!}
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn blue"><i class="fa fa-send"></i> Send Message</button>
                    </div>
                </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->

    <div class="modal fade" id="message_all_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <form action="/messages/send-all" role="form" method="post" class="form">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h4 class="modal-title" id="modal_title_all"></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Message Content:</label>
                            <textarea class="form-control input-lg" rows="4" required placeholder="Message Content" name="message" id="message_content_all"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {!! Form::hidden('message_type', '', ['id'=>'message_type']) !!}
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn blue"><i class="fa fa-send"></i> Send Message To All</button>
                    </div>
                </div>
            </form>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
    @endsection


    @section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-tabdrop/js/bootstrap-tabdrop.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/custom/js/messages/message.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/messages"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            TableDatatablesAjax.init();
            UIBlockUI.init();
        });
    </script>
@endsection

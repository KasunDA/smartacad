@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/pages/css/profile.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('title', 'My Subjects')

@section('breadcrumb')
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/profiles') }}">Profile</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>My Subjects</span>
    </li>
@stop

@section('content')
    <h3 class="page-title">My Profile | Subjects</h3>

    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        @include('admin.layout.partials.profile-nav', ['active' => 'subject'])
        <!-- END BEGIN PROFILE SIDEBAR -->
        <div class="profile-content">
            <?php $j = 1; ?>
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN CHART PORTLET-->
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa fa-book font-green"></i>
                                <span class="caption-subject font-green bold uppercase">Lists of Subjects Assigned.</span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <div class="table-actions-wrapper">
                                    <span> </span>
                                    Search: <input type="text" class="form-control input-inline input-small input-sm" id="search_param"/>
                                </div>
                                <table class="table table-striped table-bordered table-hover" id="subject_tabledata">
                                    <thead>
                                    <tr role="row" class="heading">
                                        <th>#</th>
                                        <th>Academic Term</th>
                                        <th>Subject Name</th>
                                        <th>Exam Status</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr role="row" class="heading">
                                        <th>#</th>
                                        <th>Academic Term</th>
                                        <th>Subject Name</th>
                                        <th>Exam Status</th>
                                        <th>Details</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END CHART PORTLET-->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-level-js')
    <script src="{{ asset('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endsection


@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/profiles"]');

            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN' : '{{ csrf_token() }}' } });

            var url = '/staffs/staff-subjects?<?= $conditions ?>';

            setTableDatatablesAjax($('#subject_tabledata'), url).init();
        });
    </script>
@endsection

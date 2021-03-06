@extends('admin.layout.default')

@section('title', 'School Subjects')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li><i class="fa fa-chevron-right"></i></li>
    <li>
        <a href="{{ url('/school-subjects/view') }}">School Subjects</a>
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
                    <div class="form-body">
                        <div class="col-md-6">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1">
                                    <ul>
                                        <li class="mt-list-item done">
                                            <div class="list-icon-container">#</div>
                                            <div class="list-item-content">
                                                <span class="big"><strong>Old Subject Name</strong> </span>
                                            </div>
                                        </li>
                                        <?php $i=1;?>
                                        @foreach($mySchool->subjects()->orderBy('subject')->get() as $subject)
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">{{ $i++ }}</div>
                                                <div class="list-item-content">
                                                    <span class="big"> {{$subject->subject}} </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-element-list">
                                <div class="mt-list-container list-simple ext-1">
                                    <ul>
                                        <li class="mt-list-item done">
                                            <div class="list-icon-container">#</div>
                                            <div class="list-item-content">
                                                <span class="big"><strong>New Subject Name (Alias)</strong> </span>
                                            </div>
                                        </li>
                                        <?php $i=1;?>
                                        @foreach($mySchool->subjects()->orderBy('subject')->get() as $subject)
                                            <li class="mt-list-item done">
                                                <div class="list-icon-container">{{ $i++ }}</div>
                                                <div class="list-item-content">
                                                    <span class="big">
                                                        {!! ($subject->pivot->subject_alias) ? $subject->pivot->subject_alias : '<span class="label label-danger">nill</span>' !!}
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
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
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/school-subjects/view"]');
        });
    </script>
@endsection

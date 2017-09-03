@extends('admin.layout.default')

@section('layout-style')
    <link href="{{ asset('assets/global/plugins/bootstrap-select/css/bootstrap-select.css') }}" rel="stylesheet" type="text/css"/>
@endsection

@section('title', 'Subjects')

@section('breadcrumb')
    <li>
        <i class="fa fa-dashboard"></i>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
    </li>
    <li>
        <a href="{{ url('/subjects') }}">Subjects</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page"> Subjects</h3>
    <!-- END PAGE HEADER-->
    <div class="row">
        <div class="col-md-8 col-xs-12 col-md-offset-2 margin-bottom-10">
            <form method="post" action="/subjects/subject-groups" role="form" class="form-horizontal">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label class="col-md-3 control-label">Subject Groups</label>

                    <div class="col-md-6">
                        <div class="col-md-9">
                            <select class="form-control selectpicker" name="subject_group_id" id="subject_group_id">
                                @foreach($subject_groups as $key => $value)
                                    @if($subject_group && $subject_group->subject_group_id === $key)
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
                        <h3 class="text-center">Subjects in:
                            <span class="text-primary">{{ ($subject_group) ? $subject_group->subject_group : 'All' }}</span> Subject Groups
                        </h3>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-10">
            <div class="portlet light bordered">
                <div class="portlet">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase"> Subjects</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-10">
                            <div class="btn-group">
                                <button class="btn btn-sm green add_subjects"> Add New
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
                                <table class="table table-bordered table-striped table-actions" id="subjects_table">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">Name</th>
                                        <th style="width: 15%;">Abbr.</th>
                                        <th style="width: 30%;">Group</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th style="width: 5%;">s/no</th>
                                        <th style="width: 40%;">Name</th>
                                        <th style="width: 15%;">Abbr.</th>
                                        <th style="width: 30%;">Group</th>
                                        <th style="width: 10%;">Actions</th>
                                    </tr>
                                    </tfoot>
                                    @if(count($subjects) > 0)
                                        <tbody>
                                        <?php $i = 1; ?>
                                        @foreach($subjects as $subject)
                                            <tr>
                                                <td class="text-center">{{$i++}} </td>
                                                <td>
                                                    {!! Form::text('subject[]', $subject->subject, ['placeholder'=>'Subjects', 'class'=>'form-control', 'required'=>'required']) !!}
                                                    {!! Form::hidden('subject_id[]', $subject->subject_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('subject_abbr[]', $subject->subject_abbr, ['placeholder'=>'Subject Abbr.', 'class'=>'form-control']) !!}</td>
                                                <td>{!! Form::select('subject_group_id[]', $subject_groups, $subject->subject_group_id, ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                                <td>
                                                    <button  data-confirm-text="Yes, Delete it!!!" data-name="{{$subject->subject}}" data-title="Delete Confirmation"
                                                             data-message="Are you sure you want to delete <b>{{$subject->subject}}?</b>"
                                                             data-action="/subjects/delete/{{$subject->subject_id}}" class="btn btn-danger btn-xs btn-condensed btn-sm confirm-delete-btn">
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
                                                {!! Form::text('subject[]', '', ['placeholder'=>'Subjects', 'class'=>'form-control', 'required'=>'required']) !!}
                                                {!! Form::hidden('subject_id[]', '-1', ['class'=>'form-control']) !!}
                                            </td>
                                            <td>{!! Form::text('subject_abbr[]', '', ['placeholder'=>'Subject Abbr.', 'class'=>'form-control']) !!}</td>
                                            <td>{!! Form::select('subject_group_id[]', $subject_groups, '', ['class'=>'form-control', 'required'=>'required']) !!}</td>
                                            <td>
                                                <button class="btn btn-danger btn-rounded btn-condensed btn-sm">
                                                    <span class="fa fa-times"></span> Remove
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                                <div class="col-md-12 margin-bottom-10">
                                    <div class="btn-group pull-left">
                                        <button class="btn btn-sm green add_subjects"> Add New
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


@section('layout-script')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/bootstrap-select/js/bootstrap-select.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="{{ asset('assets/global/scripts/app.min.js') }}" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="{{ asset('assets/pages/scripts/ui-bootbox.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL SCRIPTS -->
    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="{{ asset('assets/layouts/layout/scripts/layout.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/layouts/global/scripts/quick-sidebar.min.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {

            $('.add_subjects').click(function(e){
                e.preventDefault();
                var clone_row = $('#subjects_table tbody tr:last-child').clone();

                $('#subjects_table tbody').append(clone_row);

                clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
                clone_row.children(':nth-child(2)').children('input').val('');
                clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
                clone_row.children(':nth-child(3)').children('input').val('');
                clone_row.children(':nth-child(4)').children('select').val('');
                clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_subjects"><span class="fa fa-times"></span> Remove</button>');
            });

            $(document.body).on('click','.remove_subjects',function(){
                $(this).parent().parent().remove();
            });

            setTabActive('[href="/subjects"]');
        });
    </script>
@endsection

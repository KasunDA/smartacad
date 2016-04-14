@extends('admin.layout.default')

@section('layout-style')
@endsection

@section('title', 'Permissions')

@section('breadcrumb')
    <li>
        <a href="{{ url('/') }}">Home</a>
        <i class="fa fa-home"></i>
    </li>
    <li>
        <a href="{{ url('/dashboard') }}">Dashboard</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/menu-headers') }}">Menu Item</a>
        <i class="fa fa-circle"></i>
    </li>
@stop


@section('content')
    <h3 class="page-title"> Permissions</h3>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-list font-green"></i>
                        <span class="caption-subject font-green bold uppercase">List of Permissions</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <form method="post" action="/permissions" role="form" class="form-horizontal">
                        {!! csrf_field() !!}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-actions">
                                <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 19%;">Controller</th>
                                    <th style="width: 10%;">Action</th>
                                    <th style="width: 10%;">URI</th>
                                    <th style="width: 20%;">Display Name</th>
                                    <th style="width: 40%;">Description</th>
                                </tr>
                                </thead>
                                @if(count($controllers) > 0)
                                    <tbody>
                                    <?php $i = 0; ?>
                                    @foreach($controllers as $controller)
                                        {{--{{dd($controller)}}--}}
                                        @if(isset($permissions[$i]) and trim($controller->name) === trim($permissions[$i]->name))
                                            <tr>
                                                <td class="text-center">{{$i + 1}}</td>
                                                <td>{{ explode('@', $controller->name)[0]}}</td>
                                                <td>
                                                    {{ explode('@', $controller->name)[1]}}
                                                    {!! Form::hidden('name[]', $controller->name, ['class'=>'form-control']) !!}
                                                    {!! Form::hidden('permission_id[]', $permissions[$i]->permission_id, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>
                                                    {{ $controller->uri }}
                                                    {!! Form::hidden('uri[]', $controller->uri, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('display_name[]', $permissions[$i]->display_name  , ['placeholder'=>'Permission Display Name', 'class'=>'form-control']) !!} </td>
                                                <td>{!! Form::text('description[]', $permissions[$i]->description  , ['placeholder'=>'Permission Description', 'class'=>'form-control']) !!} </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="text-center">{{$i + 1}}</td>
                                                <td>{{ explode('@', $controller->name)[0]}}</td>
                                                <td>
                                                    {{ explode('@', $controller->name)[1]}}
                                                    {!! Form::hidden('name[]', $controller->name, ['class'=>'form-control']) !!}
                                                    {!! Form::hidden('permission_id[]', ($i+1), ['class'=>'form-control']) !!}
                                                </td>
                                                <td>
                                                    {{ $controller->uri }}
                                                    {!! Form::hidden('uri[]', $controller->uri, ['class'=>'form-control']) !!}
                                                </td>
                                                <td>{!! Form::text('display_name[]', '' , ['placeholder'=>'Permission Display Name', 'class'=>'form-control']) !!} </td>
                                                <td>{!! Form::text('description[]', '', ['placeholder'=>'Permission Description', 'class'=>'form-control']) !!} </td>
                                            </tr>
                                        @endif
                                        <?php $i++; ?>
                                    @endforeach
                                    </tbody>
                                @endif
                                <tfoot>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 19%;">Controller</th>
                                    <th style="width: 10%;">Action</th>
                                    <th style="width: 10%;">URI</th>
                                    <th style="width: 20%;">Display Name</th>
                                    <th style="width: 40%;">Description</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary pull-right" type="submit">Save record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE HEADER-->
@endsection


    @section('layout-script')
            <!-- BEGIN PAGE LEVEL PLUGINS -->
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
    <script src="{{ asset('assets/custom/js/roles-permissions/permissions.js') }}" type="text/javascript"></script>
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/permissions"]');
        });
    </script>
@endsection

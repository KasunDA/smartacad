@extends('front.layout.default')

@section('title', 'Students Billings')

@section('breadcrumb')
    <li>
        <a href="{{ url('/home') }}">Home</a>
        <i class="fa fa-dashboard"></i>
    </li>
    <li>
        <a href="{{ url('/wards') }}">Students</a>
        <i class="fa fa-users"></i>
    </li>
    <li>
        <span>Assessments</span>
    </li>
@stop

@section('page-title')
    <h1> Billings Summary</h1>
@endsection

@section('content')
    <div class="row widget-row" style="margin-top: 20px;">
        @if(count($students) > 0)
            <div class="row">
                <div class="col-md-10">
                    <!-- BEGIN ACCORDION PORTLET-->
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="icon-user"> </i>
                                <span class="caption-subject font-white bold uppercase">Available Students (Billing)</span>
                            </div>
                            <div class="tools">
                                <a href="javascript:;" class="collapse"> </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="panel-group accordion scrollable" id="accordion1">
                                <?php $i = 1;?>
                                @foreach($students as $student)
                                    <?php $collapse = ($i == 1) ? 'in' : 'collapse'; ?>
                                    <?php
                                        $j = 1;
                                        $orders = OrderView::where('student_id', $student->student_id)
                                                ->groupBy(['academic_term_id'])
                                                ->orderBy('academic_term_id', 'DESC')
                                                ->get();
                                    ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle accordion-toggle-styled" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1_{{$i}}">
                                                    ({{$i}}) {{ $student->fullNames() }}
                                                    {{ ($student->currentClass(AcademicTerm::activeTerm()->academic_year_id))
                                                        ? 'in: ' . $student->currentClass(AcademicTerm::activeTerm()->academic_year_id)->classroom : ''
                                                    }}
                                                    {{ ' || Billing Records' }}
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapse_1_{{$i++}}" class="panel-collapse {{ $collapse }}">
                                            <div class="panel-body" style="height:300px; overflow-y:auto;">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <thead>
                                                        <tr role="row" class="heading">
                                                            <th>#</th>
                                                            <th>Academic Term</th>
                                                            <th>Class Room</th>
                                                            <th>Item(s)</th>
                                                            <th>Details</th>
                                                        </tr>
                                                        </thead>
                                                        <tfoot>
                                                        <tr role="row" class="heading">
                                                            <th>#</th>
                                                            <th>Academic Term</th>
                                                            <th>Class Room</th>
                                                            <th>Item(s)</th>
                                                            <th>Action</th>
                                                        </tr>
                                                        </tfoot>
                                                        <tbody>
                                                        <?php $i = 1; ?>
                                                        @foreach($orders as $order)
                                                            <tr>
                                                                <td>{{$i++}} </td>
                                                                <td>{{ $order->academic_term }}</td>
                                                                <td>{{ $order->classroom }}</td>
                                                                <td>{{ $order->item_count }}</td>
                                                                <td>
                                                                    <a href="{{ url('/wards-billings/details/'.$hashIds->encode($student->student_id)).'/'.$hashIds->encode($order->order_id) }}"
                                                                       class="btn btn-warning btn-rounded btn-condensed btn-xs">
                                                                        <span class="fa fa-eye"></span> Details
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        @if(empty($orders))
                                                            <tr>
                                                                <th colspan="5">No Record Found</th>
                                                            </tr>
                                                        @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- END ACCORDION PORTLET-->
                </div>
            </div>
        @else
            <div class="col-md-6 col-md-offset-5">
                <h2>No Record</h2>
            </div>
        @endif
    </div>
<!-- END CONTENT BODY -->
@endsection

@section('page-level-js')

@endsection

@section('layout-script')
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script>
        jQuery(document).ready(function () {
            setTabActive('[href="/wards-billings"]');
        });
    </script>
@endsection

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice for {{$order->student->fullNames()}} in {{ $order->academicTerm->academic_term }}</title>
    <link href="{{ asset('assets/global/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/custom/css/invoice.css') }}" rel="stylesheet" type="text/css" media="all" />

    <link rel="shortcut icon" href="{{ $mySchool->getLogoPath() }}" />
</head>

<body>
<!-- export button -->
<div class="text-center" style="margin-bottom: 20px; margin-top: 20px">
    @include('admin.partials.orders.pdf', ['type'=>'print'])
</div>
<!-- / export button -->

@if(session()->has('message'))
    <div class="text-center alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times</span>
        </button>
        {{session('message')}}
    </div>
@endif
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <!-- School Details-->
        <tr class="top">
            <td colspan="5">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{$mySchool->getLogoPath()}}" style="max-width:100px; max-height:100px;">
                        </td>

                        <td class="invoice-right">
                            <div style="color:#666; font-size: 26px;">
                                {{ strtoupper($mySchool->full_name) }}
                            </div>
                            {!! ($mySchool->address) ? '<div style="font-size: 12px; font-weight: bold;">'.$mySchool->address.'</div>' : '' !!}
                            {!! ($mySchool->email) ? '<small>email: '.$mySchool->email.'</small>' : '' !!}
                            {!! ($mySchool->website) ? '<small>website: '.$mySchool->website.'</small>' : '' !!}<br>
                            {!! ($mySchool->phone_no) ? '<small>phone no.: ' . $mySchool->phone_no . ', ' . $mySchool->phone_no2 ?? '' . '</small>' : '' !!}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- / School Details-->

        <!-- Order Details-->
        <tr class="information">
            <td colspan="5">
                <table>
                    <tr>
                        <td>
                            Invoice To:<br>
                            <strong>{{ $order->student->fullNames() }}</strong><br>
                            <strong>{{ $order->classroom->classroom }}<strong><br>
                            <strong>{{ $order->academicTerm->academic_term }}<strong><br>
                        </td>

                        <td class="invoice-right">
                            <strong>Invoice #: {{$order->number}}</strong><br>
                            Items: {{ count($items) }}<br>
                            Status: {{ strtoupper($order->status) }}<br>
                        </td>

                    </tr>
                </table>
            </td>
        </tr>
        <!-- / Order Details-->
        <!-- Items Details-->
        <tr class="heading">
            <td width="1%">#</td>
            <td width="22%">Item</td>
            <td width="62%">Item Description</td>
            <td width="15%" style="text-align: right">&#8358; Amount</td>
        </tr>
        <?php $total = 0; $i = 1; ?>
        @if($items)
            @foreach($items as $item)
                <tr class="item">
                    <td width="1%">{{ $i++ }}</td>
                    <td width="22%">{{ $item->item->name }}</td>
                    <td width="62%">{{ $item->item->description }}</td>
                    <td width="15%">{{ CurrencyHelper::format($item->amount)  }}</td>
                </tr>
                <?php $total += $item->amount; ?>
            @endforeach
        @endif
        <tr class="total">
            <td colspan="3"><strong>Total</strong></td>
            <td>{{ CurrencyHelper::format($total, 0, true)  }}</td>
        </tr>
        <!-- / Items Details-->
        <tr>
            <!-- Part Payment-->
            @if($order->is_part_payment)
                <td colspan="2">
                    <table>
                        <tr class="heading">
                            <td colspan="2" style="text-align: center">Part Payments</td>
                        </tr>
                        <?php $total2 = 0; $j = 1; ?>
                            @foreach($order->partPayments as $part)
                                <tr class="item">
                                    <td>{{ $j++ }}</td>
                                    <td style="text-align: right">{{ CurrencyHelper::format($part->amount, 0) }}</td>
                                </tr>
                                <?php $total2 += $part->amount; ?>
                            @endforeach
                            <tr class="total">
                                <td><strong>Total</strong></td>
                                <td>{{ CurrencyHelper::format($total2, 0, true)  }}</td>
                            </tr>
                    </table>
                </td>
            @endif
            <!-- /Part Payment-->
            <!-- Payment Summary-->
            <td colspan="{{ ($order->is_part_payment) ? 2 : 3}}">
                <table>
                    <tr class="heading">
                        <td colspan="4" style="text-align: center">Payments Details</td>
                    </tr>
                    <tr class="total">
                        <td>Total Amount</td>
                        <td class="amount">{{ CurrencyHelper::format($order->total_amount, 2, true) }}</th>
                        <td>Amount Payable </td>
                        <td class="amount">{{ CurrencyHelper::format($order->amount, 2, true) }}</th>
                    </tr>
                    <tr class="total">
                        <td>Discount</td>
                        <td class="amount">{{CurrencyHelper::format($order->total_amount - $order->amount, 1)}} ({{$order->discount}}%)</td>
                        <td>Amount Paid</td>
                        <td class="amount">{{ CurrencyHelper::format($order->amount_paid, 2, true) }}</th>
                    </tr>
                    @if($order->is_part_payment)
                        <tr class="total">
                            <td>Installments</td>
                            <td>{{ $order->partPayments->count() }}</td>
                            <td>Outstanding: </td>
                            <td class="amount">{{ CurrencyHelper::format($order->amount -$order->partPayments()->lists('amount')->sum(), 2, true) }}</td>
                        </tr>
                    @endif
                </table>
            </td>
            <!-- / Payment Summary-->
        </tr>
    </table><br>
    <!-- stamp logo (Paid/Not-Paid) -->
    <!-- TODO $accounts display account ->
    <div class="row">
        <div class="col-md-6 col-md-offset-4">
            <img alt="{{ $order->status }}" src="/assets/custom/img/{{strtolower($order->status)}}.png" style="height:100px; width:100px" />
        </div>
    </div>
    <!-- / stamp logo (Paid/Not-Paid) -->
</div>
<script src="{{ asset('assets/global/plugins/jquery.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/global/plugins/bootstrap/js/bootstrap.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/global/scripts/jquery.PrintArea.js')}}" type="text/javascript"></script>

<script>
    $(document).ready(function () {
        $('.print-button').click(function () {
            $('.invoice-box').printArea()
        })
    })
</script>

</body>
</html>

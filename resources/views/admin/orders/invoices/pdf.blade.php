<!doctype html>
<html><head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice for {{$order->student->fullNames()}} in {{ $order->academicTerm->academic_term }}</title>
    <link rel="shortcut icon" href="{{ public_path($mySchool->getLogoPath()) }}" />
    <style>
        .invoice-box{
            max-width:800px;
            margin:auto;
            padding:30px;
            border:1px solid #eee;
            box-shadow:0 0 10px rgba(0, 0, 0, .15);
            font-size:15px;
            line-height:20px;
            font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color:#555;
        }

        .invoice-box table{
            width:100%;
            line-height:inherit;
            text-align:left;
        }

        .invoice-box table td{
            padding:5px;
            vertical-align:top;
        }

        .invoice-box table tr td:nth-child(2){
            text-align:left;
        }

        .invoice-box table tr.top table td{
            padding-bottom:0px;
        }

        .invoice-box table tr.top table td.title{
            font-size:35px;
            line-height:40px;
            color:#333;
        }

        .invoice-box table tr.information table td{
            padding-bottom:0px;
        }

        .invoice-box table tr.heading td{
            background:#eee;
            font-size:14px;
            border-bottom:1px solid #ddd;
            font-weight:bold;
        }

        .invoice-box table tr.details td{
            padding-bottom:20px;
        }

        .invoice-box table tr.item td{
            border-bottom:1px solid #eee;
            font-size:12px;
        }

        .invoice-box table tr.item td:last-child{
            text-align: right;
            font-size:13px;
        }

        .invoice-box table tr.item.last td{
            border-bottom:none;
        }

        .invoice-box table tr.total td{
            border-top:2px solid #eee;
            font-weight:bold;
            font-size:14px;
        }

        .invoice-box table tr.total td:last-child{
            text-align: right;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .invoice-box * {
                visibility: visible;
            }
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td{
                width:100%;
                display:block;
                text-align:center;
            }

            .invoice-box table tr.information table td{
                width:100%;
                display:block;
                text-align:center;
            }
        }
        .invoice-right{
            text-align:right !important;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head><body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
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
        <tr>
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
                            <td class="amount">{{ CurrencyHelper::format($order->amount -$order->partPayments()->pluck('amount')->sum(), 2, true) }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table>
                    <tr class="heading">
                        <td colspan="4" style="text-align: center">
                            School Account ({{ (!empty($accounts[0]) && !empty($accounts[0]->classGroup)) ? $accounts[0]->classGroup->classgroup : 's' }})
                        </td>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Bank</th>
                    </tr>
                    <?php $k = 1; ?>
                    @foreach($accounts as $account)
                        <tr>
                            <td>{{ $k++ }}</td>
                            <td>{{ $account->account_name }}</td>
                            <td>{{ $account->account_number }}</td>
                            <td>{{ $account->bank->name }}</td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td colspan="1">
                <div class="col-md-6 col-md-offset-1">
                    <img alt="{{ $order->status }}" src="/assets/custom/img/{{strtolower(Order::ORDER_STATUSES[$order->paid])}}.png" style="height:100px; width:100px" />
                </div>
            </td>
        </tr>
    </table>
</div>
</body></html>

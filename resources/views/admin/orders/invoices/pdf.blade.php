<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice for {{$order->student->fullNames()}} in {{ $order->academicTerm->academic_term }}</title>
    <link rel="shortcut icon" href="{{ public_path($mySchool->getLogoPath()) }}" />

    <style>
        .invoice-box{
            max-width:800px;
            margin:auto;
            padding:2px;
            border:1px solid #eee;
            box-shadow:0 0 10px rgba(0, 0, 0, .15);
            font-size:16px;
            font-family:'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color:#555;
        }

        .invoice-box table{
            width:100%;
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
            padding-bottom:5px;
        }

        .invoice-box table tr.top table td.title{
            font-size:45px;
            color:#333;
        }

        .invoice-box table tr.information table td{
            padding-bottom:2px;
        }

        .invoice-box table tr.heading td{
            background:#eee;
            border-bottom:1px solid #ddd;
            font-weight:bold;
        }

        .invoice-box table tr.details td{
            padding-bottom:2px;
        }

        .invoice-box table tr.item td{
            border-bottom:1px solid #eee;
        }

        .invoice-box table tr.item.last td{
            border-bottom:none;
        }

        .invoice-box table tr.total td:nth-child(2){
            border-top:2px solid #eee;
            font-weight:bold;
        }

        .page-break {
            page-break-after: always;
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
    </style>

</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="5">
                <table>
                    <tr>
                        <td class="title">
                            <img src="{{public_path($mySchool->getLogoPath())}}" style="max-width:100px; max-height:100px;">
                        </td>

                        <td class="invoice-right">
                            <div style="color:#666; font-size: 26px;">
                                {{ strtoupper($mySchool->full_name) }}
                            </div>
                            {!! ($mySchool->address) ? '<div style="font-size: 12px; font-weight: bold;">'.$mySchool->address.'</div>' : '' !!}
                            {!! ($mySchool->email) ? '<small>'.$mySchool->email.'</small>' : '' !!}<br>
                            {!! ($mySchool->website) ? '<small>'.$mySchool->website.'</small>' : '' !!}
                        </td>
                    </tr>
                </table><br>
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
                </table><br>
            </td>
        </tr>
        <tr class="heading">
            <td width="1%">#</td>
            <td width="30%">Item</td>
            <td width="44%">Item Description</td>
            <td width="25%">Amount</td>
        </tr>
        <?php $total = 0; $i = 1; ?>
        @if($items)
            @foreach($items as $item)
                <tr>
                    <td width="1%">{{ $i++ }}</td>
                    <td width="30%">{{ $item->item->name }}</td>
                    <td width="44%">{{ $item->item->description }}</td>
                    <td width="25%">{{ CurrencyHelper::format($item->amount)  }}</td>
                </tr>
                <?php $total += $item->amount; ?>
            @endforeach
        @endif
        <tr class="total">
            <td colspan="3"><strong>Total</strong></td>
            <th>N {{ CurrencyHelper::format($total, 0)  }}</th>
        </tr>
    </table><br><br>
    <div class="row">
        <div class="col-md-6 col-md-offset-4">
            <img alt="{{ $order->status }}" src="{{public_path('/assets/custom/img/' . strtolower($order->status))}}.png"
                 style="height:160px; width:160px" />
        </div>
    </div>
</div>
</body>
</html>

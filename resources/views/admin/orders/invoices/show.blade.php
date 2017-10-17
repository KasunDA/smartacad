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
<div class="text-center" style="margin-bottom: 20px; margin-top: 20px">
    <a class="btn btn-default print-button">Print</a>
    <a class="btn btn-danger" href="{{ url('/invoices/download/'.$hashIds->encode($order->id)) }}">Download</a>
    <a class="btn btn-primary" href="{{ url('/invoices/pdf/'.$hashIds->encode($order->id)) }}">View in PDF</a>
    {{--<a class="btn btn-success" href="{{ url('/invoices/send-pdf/'.$hashIds->encode($order->id)) }}">Send to {{$order->student->sponsor->simpleName()}}</a>--}}
</div>
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
            <td width="27%">Item</td>
            <td width="50%">Item Description</td>
            <td width="22%">&#8358; Amount</td>
        </tr>
        <?php $total = 0; $i = 1; ?>
        @if($items)
            @foreach($items as $item)
                <tr class="item">
                    <td width="1%">{{ $i++ }}</td>
                    <td width="27%">{{ $item->item->name }}</td>
                    <td width="50%">{{ $item->item->description }}</td>
                    <td width="22%">{{ CurrencyHelper::format($item->amount)  }}</td>
                </tr>
                <?php $total += $item->amount; ?>
            @endforeach
        @endif
        <tr class="total">
            <td colspan="3"><strong>Total</strong></td>
            <td>{{ CurrencyHelper::format($total, 0, true)  }}</td>
        </tr>
    </table><br><br>
    <div class="row">
        <div class="col-md-6 col-md-offset-4">
            <img alt="{{ $order->status }}" src="/assets/custom/img/{{strtolower($order->status)}}.png" style="height:160px; width:160px" />
        </div>
    </div>
</div>
<div class="text-center" style="margin-bottom: 20px; margin-top: 20px">
    <a class="btn btn-default print-button">Print</a>
    <a class="btn btn-danger" href="{{ url('/invoices/download/'.$hashIds->encode($order->id)) }}">Download</a>
    <a class="btn btn-primary" href="{{ url('/invoices/pdf/'.$hashIds->encode($order->id)) }}">View in PDF</a>
    {{--<a class="btn btn-success" href="{{ url('/invoices/send-pdf/'.$hashIds->encode($order->id)) }}">Send to {{$order->student->sponsor->simpleName()}}</a>--}}
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

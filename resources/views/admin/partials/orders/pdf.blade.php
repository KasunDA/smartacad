@if($type == 'page')
    <div class="pull-right">
        <a target="_blank" href="{{ url('/invoices/order/'.$hashIds->encode($order->id)) }}" class="btn btn-default btn-xs sbold">
            <i class="fa fa-print"></i> View / Print
        </a>
        <a target="_blank" href="{{ url('/invoices/pdf/'.$hashIds->encode($order->id)) }}" class="btn btn-info btn-xs sbold">
            <i class="fa fa-file"></i> PDF
        </a>
        <a target="_blank" href="{{ url('/invoices/download/'.$hashIds->encode($order->id)) }}" class="btn btn-primary btn-xs sbold">
            <i class="fa fa-download"></i> Download
        </a>
    </div>
@endif

@if($type == 'print')
    <a class="btn btn-info btn-xs print-button"><i class="fa fa-print"></i> Print</a>
    <a class="btn btn-warning btn-xs" href="{{ url('/invoices/download/'.$hashIds->encode($order->id)) }}"><i class="fa fa-download"></i> Download</a>
    <a class="btn btn-primary btn-xs" href="{{ url('/invoices/pdf/'.$hashIds->encode($order->id)) }}"><i class="fa fa-file"></i> View in PDF</a>
    {{--<a class="btn btn-success" href="{{ url('/invoices/send-pdf/'.$hashIds->encode($order->id)) }}">Send to {{$order->student->sponsor->simpleName()}}</a>--}}
@endif

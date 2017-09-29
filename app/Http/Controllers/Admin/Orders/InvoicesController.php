<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Admin\Orders\Order;
use Barryvdh\DomPDF\PDF;

class InvoicesController extends Controller
{
    /**
     * Details of a student orders and items in an academic term
     *
     * @param String $orderId
     * @return Response
     */
    public function order($orderId)
    {
        $order = Order::findOrFail($this->decode($orderId));
        $items = !empty($order) ? $order->orderItems()->get() : false;

        return view('admin.orders.invoices.show', compact('order', 'items'));
    }

    /**
     * Print Details of a student orders and items in an academic term as pdf
     *
     * @param String $orderId
     * @return Response
     */
    public function printPDF($orderId){
        $order = Order::findOrFail($this->decode($orderId));
//            $invoice_items_total=$invoice->items()->selectRaw('sum(price*quantity) as grand_total')->value('grand_total');
//            $invoice_items_total=$invoice_items_total > 0 ? $invoice_items_total: '';
//
//            $pdf = PDF::loadView('invoice_pages.invoice_in_pdf', compact('invoice','invoice_items_total'));
//            return $pdf->download('invoice_'.$invoice->invoice_number.'.pdf');
    }

    /**
     * View Details of a student orders and items in an academic term as pdf
     *
     * @param String $orderId
     * @return Response
     */
    public function viewPDF($orderId){
        $order = Order::findOrFail($this->decode($orderId));
//            $invoice_items_total=$invoice->items()->selectRaw('sum(price*quantity) as grand_total')->value('grand_total');
//            $invoice_items_total=$invoice_items_total > 0 ? $invoice_items_total: '';
//
//            $pdf = PDF::loadView('invoice_pages.invoice_in_pdf', compact('invoice','invoice_items_total'));
//            return $pdf->stream('invoice_'.$invoice->invoice_number.'.pdf');
    }
}

<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Admin\Orders\Order;
use PDF;

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
    public function download($orderId){
        $order = Order::findOrFail($this->decode($orderId));
        $items = !empty($order) ? $order->orderItems()->get() : false;
        $pdf = PDF::loadView('admin.orders.invoices.pdf', compact('order', 'items'));
        
        return $pdf->download('invoice_'.$order->number.'.pdf');
    }

    /**
     * View Details of a student orders and items in an academic term as pdf
     *
     * @param String $orderId
     * @return Response
     */
    public function pdf($orderId){
        $order = Order::findOrFail($this->decode($orderId));
        $items = !empty($order) ? $order->orderItems()->get() : false;
        $pdf = PDF::loadView('admin.orders.invoices.pdf', compact('order', 'items'));
        
        return $pdf->stream('invoice_'.$order->number.'.pdf');
    }
}

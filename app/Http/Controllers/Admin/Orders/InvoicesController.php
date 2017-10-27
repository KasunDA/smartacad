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
        $group_id = !empty($order) ? $order->classRoom->classLevel->classgroup_id : false;
        $accounts = $this->school_profile->schoolBanks()->where('classgroup_id', $group_id)->active()->get();

        return view('admin.orders.invoices.show', compact('order', 'items', 'accounts'));
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
        $group_id = !empty($order) ? $order->classRoom->classLevel->classgroup_id : false;
        $accounts = $this->school_profile->schoolBanks()->where('classgroup_id', $group_id)->active()->get();
        $pdf = PDF::loadView('admin.orders.invoices.pdf', compact('order', 'items', 'accounts'));
        
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
        $group_id = !empty($order) ? $order->classRoom->classLevel->classgroup_id : false;
        $accounts = $this->school_profile->schoolBanks()->where('classgroup_id', $group_id)->active()->get();
        $pdf = PDF::loadView('admin.orders.invoices.pdf', compact('order', 'items', 'accounts'));
        
        return $pdf->stream('invoice_'.$order->number.'.pdf');
    }
}

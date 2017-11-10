<?php

namespace App\Http\Controllers\Front\Orders;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Orders\Order;
use App\Models\Admin\Views\OrderView;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BillingsController extends Controller
{
    /**
     * Displays the Students of the sponsor
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $students = Auth::user()->students()->get();

        return view('front.orders.index', compact('students'));
    }
    
    /**
     * Displays the Student billings header
     * @param String $encodeId
     * @return \Illuminate\View\View
     */
    public function view($encodeId)
    {
        $student = Student::findOrFail($this->decode($encodeId));
        $orders = OrderView::where('student_id', $student->student_id)
            ->groupBy(['academic_term_id'])
            ->orderBy('academic_term_id', 'DESC')
            ->get();
        
        return view('front.orders.view', compact('student', 'orders'));
    }

    /**
     * Displays the Student billings details
     * @param String $encodeStud
     * @param String $encodeOrder
     * @return \Illuminate\View\View
     */
    public function details($encodeStud, $encodeOrder)
    {
        $student = Student::findOrFail($this->decode($encodeStud));
        $order = Order::where('id', $this->decode($encodeOrder))->first();
        $items = !empty($order) ? $order->orderItems()->get() : false;

        return view('front.orders.details', compact('order', 'student', 'items'));
    }
}

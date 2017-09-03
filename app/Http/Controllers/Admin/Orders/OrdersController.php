<?php

namespace App\Http\Controllers\Admin\Orders;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrdersController extends Controller
{
    /**
     * Display a listing of the Orders.
     *
     * @return Response
     */
    public function getIndex()
    {
        return view('admin.orders.index', compact());
    }
}

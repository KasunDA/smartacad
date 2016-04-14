<?php

namespace App\Http\Controllers\Admin\Parish;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ParishController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex(){
        return view('admin.parish.index');
    }

    public function getCreate(){
        return view('admin.parish.create');
    }
}

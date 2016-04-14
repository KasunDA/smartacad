<?php

namespace App\Http\Controllers\Admin\Records\Sacraments;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class BaptismController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex(){
        return view('admin.records.sacraments.baptisms.index');
    }
    public function getCreate(){
        return view('admin.records.sacraments.baptisms.create');
    }
}

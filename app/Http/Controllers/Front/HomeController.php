<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private $colors;

    /**
     * A list of colors for representing charts
     */
    public function __construct()
    {
        $this->colors = [
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000'
        ];

        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $students = Auth::user()->students()->get();
        $students_count = $students->count();
        $active_students_count = Auth::user()->students()
            ->where('status_id', 1)
            ->count();
        $inactive_students_count = Auth::user()
            ->students()
            ->where('status_id', '<>', 1)
            ->count();

        return view('front.dashboards.home', compact('students', 'students_count', 'active_students_count', 'inactive_students_count'));
    }
}

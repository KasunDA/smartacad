<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\Orders\Order;
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

    /**
     * Process Billings
     *
     * @return Response
     */
    public function getBillings()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('- Class Level -', '');

        return view('admin.orders.billings', compact('academic_years', 'classlevels'));
    }

    /**
     * Initiate Billings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postInitiateBillings(Request $request) {
        $inputs = $request->all();

        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        if($term){
            //Update
            Order::processBillings($term->academic_term_id);
            session()->put('active', 'terminal');
            $this->setFlashMessage('Billings for ' . $term->academic_term . ' Academic Term has been successfully initiated.', 1);
        }

        return response()->json($term);
    }
}

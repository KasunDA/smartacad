<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemQuote;
use App\Models\Admin\Items\ItemType;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Orders\Order;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use stdClass;

class BillingsController extends Controller
{
    /**
     * Display a listing of the Orders.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('- Class Level -', '');
        $items = Item::where('status', 1)
            ->where('item_type_id', '<>', ItemType::UNIVERSAL)
            ->lists('name', 'id')
            ->prepend('- Select Item -', '');

        return view('admin.orders.billings.index', compact('academic_years', 'classlevels', 'items'));
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
            session()->put('billing-tab', 'terminal');
            $this->setFlashMessage('Billings for ' . $term->academic_term . ' Academic Term has been successfully initiated.', 1);
        }

        return response()->json($term);
    }

    /**
     * Search For Students in a classroom for an academic term
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchResults(Request $request)
    {
        session()->put('billing-tab', 'student');
        $inputs = $request->all();
        $students = $classrooms = $output = $items = [];
        $term = AcademicTerm::findOrFail($inputs['view_academic_term_id']);

        if(!empty($inputs['view_classroom_id'])){
            $students = StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])
                ->whereIn('student_id', Student::where('status_id', 1)->lists('student_id')->toArray())
                ->get();
        }else{
            $classrooms = ClassRoom::where('classlevel_id', $inputs['view_classlevel_id'])->get();
        }

        $quotes = ItemQuote::where('classlevel_id', $inputs['view_classlevel_id'])
                ->where('academic_year_id', $term->academic_year_id)
                ->whereIn('item_id', 
                    Item::where('status', 1)
                        ->where('item_type_id', '<>', ItemType::UNIVERSAL)
                        ->lists('id')
                        ->toArray()
                )
                ->get();
        
        foreach ($quotes as $quote){
            $items[] = array(
                "id"=>$quote->item_id,
                "name"=>$quote->item->name,
                "amount"=>$quote->amount
            );
        }
        
        $response = array();
        $response['flag'] = 0;
        $response['term_id'] = $term->academic_term_id;
            $response['Items'] = $items;

        if(!empty($students)){
            //All the students in the class room for the academic year
            foreach($students as $student){
                $object = new stdClass();
                $object->student_id = $student->student_id;
                $object->student_no = $student->student()->first()->student_no;
                $object->name = $student->student()->first()->fullNames();
                $object->gender = $student->student()->first()->gender;
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });
            $response['flag'] = 1;
            $response['Students'] = $output;
        }

        if(!empty($classrooms)){
            //All the class rooms in the class level for the academic year
            foreach($classrooms as $classroom){
                $res[] = array(
                    "classroom_id"=>$classroom->classroom_id,
                    "classroom"=>$classroom->classroom,
                    "academic_term"=>$term->academic_term,
                    "student_count"=>$classroom->studentClasses()
                        ->where('academic_year_id', $inputs['view_academic_year_id'])
                        ->whereIn('student_id', Student::where('status_id', 1)->lists('student_id')->toArray())
                        ->count()
                );
            }
            $response['flag'] = 2;
            $response['Classrooms'] = isset($res) ? $res : [];
        }
        echo json_encode($response);
    }

    /**
     * Bill Student or Class Room for an Item(s)
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postItemVariables(Request $request)
    {
        //type_id 1 for student and 2 for class room
        $inputs = $request->all();
        $term = AcademicTerm::findOrFail($inputs['term_id']);
        $inputs['ids'] = trim($inputs['ids']);
        $ids = !empty($inputs['ids']) ? substr($inputs['ids'], 0, strlen($inputs['ids']) -1) : $inputs['ids'];
        $items = implode(',', $inputs['item_id']);

        dd($inputs);
        if($term){
            session()->put('billing-tab', 'student');
            Order::processItemVariables($inputs['term_id'], $ids, $items, $inputs['type_id']);
            $this->setFlashMessage('Billings for ' . $term->academic_term . ' Academic Term has been successfully charged.', 1);
        }
    }

    /**
     * Get Item Quotes based on an item
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getItemQuotes(Request $request)
    {
        $inputs = $request->all();
        $term = AcademicTerm::findOrFail($inputs['term_id']);
        $quote = ItemQuote::where('item_id', $inputs['item_id'])
            ->where('academic_year_id', $term->academic_year_id)
            ->first();

        echo json_encode($quote->amount);
    }
}

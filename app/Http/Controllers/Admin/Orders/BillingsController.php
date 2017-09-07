<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemQuote;
use App\Models\Admin\Items\ItemType;
use App\Models\Admin\Items\ItemVariable;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Orders\Order;
use App\Models\Admin\Orders\OrderInitiate;
use App\Models\Admin\Orders\OrderItem;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Null_;
use stdClass;

class BillingsController extends Controller
{
    /**
     * Display a Form for billing.
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
     * Display a form for viewing bills records.
     *
     * @return Response
     */
    public function getView()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')->prepend('- Class Level -', '');
        $items = Item::where('status', 1)
            ->where('item_type_id', '<>', ItemType::UNIVERSAL)
            ->lists('name', 'id')
            ->prepend('- Select Item -', '');

        return view('admin.orders.billings.view', compact('academic_years', 'classlevels', 'items'));
    }

    /**
     * Initiate Billings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postInitiateBillings(Request $request) {
        $inputs = $request->all();

        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);
        $stat = 'Updated';
        
        if(!empty($term)){
            
            $initiate = OrderInitiate::where('academic_term_id', $term->academic_term_id)->first();
            if(empty($initiate)){
                $initiate = OrderInitiate::create([
                    'academic_term_id' => $term->academic_term_id,
                    'user_id' => Auth::id()
                ]);
                $stat = 'Initiated';
            }
            
            Order::processBillings($initiate->id);
            session()->put('billing-tab', 'terminal');
            $this->setFlashMessage('Billings for ' . $term->academic_term . ' Academic Term has been successfully ' . $stat, 1);
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
        $ids = !empty($inputs['ids'] && strlen($inputs['ids']) > 1) ? substr($inputs['ids'], 0, strlen($inputs['ids']) -1) : $inputs['ids'];
        $type_id = $inputs['type_id'];
        $stat = 'Updated';
        $items = array_unique($inputs['item_id']);

        if($term){

            $variableIds = ItemVariable::where('academic_term_id', $term->academic_term_id)
                ->whereIn('item_id', $items)
                ->where( function($query) use($type_id, $ids){
                    if($type_id == 1)
                        $query->whereIn('student_id', explode(',', $ids));
                    else if($type_id == 2)
                        $query->whereIn('classroom_id', explode(',', $ids));
                })
                ->lists('id')
                ->toArray();

            if(empty($variableIds)){
                $records = array_unique(explode(',', $ids));
                $variableIds = [];

                for ($i = 0; $i < count($items); $i++){
                    for ($j = 0; $j < count($records); $j++){
                        $variable = new ItemVariable();
                        $variable->academic_term_id = $term->academic_term_id;
                        $variable->item_id = $items[$i];
                        $variable->student_id = ($type_id == 1) ? $records[$j] : Null;
                        $variable->classroom_id = ($type_id == 2) ? $records[$j] : Null;
                        if($variable->save()){
                            $variableIds[] = $variable->id;
                        }
                    }
                }
                $stat = 'Initiated';
            }

            $variableIds = implode(',', $variableIds);

            Order::processItemVariables($variableIds);
            session()->put('billing-tab', 'student');
            $this->setFlashMessage('Item Billings for ' . $term->academic_term . ' has been successfully ' . $stat, 1);
        }
        
        return response()->json($term);
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

    /**
     * Search For Students in a classroom for an academic term for view/adjust billings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchStudents(Request $request)
    {
        $inputs = $request->all();
        $students = $output = [];
        $term = AcademicTerm::findOrFail($inputs['view_academic_term_id']);

        if(!empty($inputs['view_classroom_id'])){
            $students = StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])
                ->whereIn('student_id', Student::where('status_id', 1)->lists('student_id')->toArray())
                ->get();
        }

        $response = array();
        $response['flag'] = 0;

        if(!empty($students)){
            //All the students in the class room for the academic year
            foreach($students as $student){
                $object = new stdClass();
                $object->student_id = $this->encode($student->student_id);
                $object->term_id = $this->encode($inputs['view_academic_term_id']);
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
        echo json_encode($response);
    }

    /**
     * Details of a student orders and items in an academic term
     *
     * @param String $studentId
     * @param String $termId
     * @return Response
     */
    public function getItems($studentId, $termId)
    {
        $term = AcademicTerm::findOrFail($this->decode($termId));
        $student = Student::findOrFail($this->decode($studentId));

        $order = Order::where('academic_term_id', $term->academic_term_id)
            ->where('student_id', $student->student_id)
            ->first();
        
        $items = !empty($order) ? $order->orderItems()->get() : false;

        return view('admin.orders.billings.items', compact('order', 'term', 'student', 'items'));
    }

    /**
     * Soft Delete an Item billed to a student
     *
     * @param Int $itemId
     * @return Response
     */
    public function getDeleteItem($itemId)
    {
        $orderItem = OrderItem::findOrFail($itemId);

        //Delete The Role Record
        $delete = !empty($orderItem) ? $orderItem->delete() : false;

        if ($delete) {
            $this->setFlashMessage('  Deleted!!! ' . $orderItem->item->name . ' deleted from the student billings.', 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

}

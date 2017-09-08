<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemType;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\Orders\Order;
use App\Models\Admin\Orders\OrderItem;
use Illuminate\Http\Request;
use stdClass;

class OrdersController extends Controller
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

        return view('admin.orders.index', compact('academic_years', 'classlevels', 'items'));
    }

    /**
     * Search For Students in a classroom for an academic term for order adjustments
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
            session()->put('order-tab', 'adjust-order');
            //All the students in the class room for the academic year
            foreach($students as $student){
                $object = new stdClass();
                $object->student_id = $this->encode($student->student_id);
                $object->term_id = $this->encode($term->academic_term_id);
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

        return view('admin.orders.items', compact('order', 'term', 'student', 'items'));
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

    /**
     * Update an order item amount
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postItemUpdateAmount(Request $request)
    {
        $inputs = $request->all();
        $item = OrderItem::findOrFail($inputs['order_item_id']);
        $item->amount = $inputs['amount'];

        ($item->save())
            ? $this->setFlashMessage('  Updated!!! ' . $item->item->name . ' Amount successfully updated.', 1)
            : $this->setFlashMessage('Error!!! Unable to adjust record.', 2);

        echo json_encode($item);
    }
    
}

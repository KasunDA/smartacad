<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Helpers\CurrencyHelper;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Admin\Accounts\Students\Student;
use App\Models\Admin\Accounts\Students\StudentClass;
use App\Models\Admin\Items\Item;
use App\Models\Admin\Items\ItemType;
use App\Models\Admin\MasterRecords\AcademicTerm;
use App\Models\Admin\MasterRecords\AcademicYear;
use App\Models\Admin\MasterRecords\Classes\ClassLevel;
use App\Models\Admin\MasterRecords\Classes\ClassRoom;
use App\Models\Admin\Orders\Order;
use App\Models\Admin\Orders\OrderItem;
use App\Models\Admin\Orders\OrderLog;
use App\Models\Admin\Orders\OrderView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Search For Students in a classroom for an academic term to view Orders
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearch(Request $request)
    {
        session()->put('order-tab', 'view-order');
        $inputs = $request->all();
        $output = [];
        $response['flag'] = 0;
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);

        if(!empty($inputs['classroom_id'])){
            $orders = OrderView::where('academic_term_id', $inputs['academic_term_id'])
                ->where('classroom_id', $inputs['classroom_id'])
                ->get();
        }else{
            $orders = OrderView::where('academic_term_id', $inputs['academic_term_id'])
                ->whereIn(
                    'classroom_id',
                    ClassRoom::where('classlevel_id', $inputs['classlevel_id'])
                        ->lists('classroom_id')
                        ->toArray()
                )
                ->get();
        }


        if(!empty($orders)){
            //All the students in the class room for the academic year
            foreach($orders as $order){
                $object = new stdClass();
                $status = ($order->paid) ? 'success' : 'danger';
                
                $object->student_id = $this->encode($order->student_id);
                $object->term_id = $this->encode($term->academic_term_id);
                $object->order_id = $order->order_id;
                $object->number = $order->number;
                $object->status = '<span class="label label-'.$status.'">'.$order->status.'</span>';
                $object->amount = CurrencyHelper::format($order->amount);
                $object->paid = $order->paid;
                $object->student_no = $order->student_no;
                $object->name = $order->fullname;
                $object->classroom = $order->classroom;
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b)
            {
                return strcmp($a->name, $b->name);
            });
            $response['flag'] = 1;
            $response['Orders'] = $output;
        }
        echo json_encode($response);
    }

    /**
     * Search For Students in a classroom for an academic term for order adjustments
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postSearchStudents(Request $request)
    {
        session()->put('order-tab', 'adjust-order');
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

        $delete = !empty($orderItem) ? $orderItem->delete() : false;

        ($delete)
            ? $this->setFlashMessage('  Deleted!!! ' . $orderItem->item->name . ' deleted from the student billings.', 1)
            : $this->setFlashMessage('Error!!! Unable to delete record.', 2);
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

    /**
     * Update Order Status
     *
     * @param Int $orderId
     * @param Int $paid
     * @return Response
     */
    public function getStatus($orderId, $paid)
    {
        $order = Order::findOrFail($orderId);
        if(empty($paid)){
            $order->paid = 1;
            $order->backend = 1;
            $order->status = Order::PAID;
            $comment = 'Status changed from ' . Order::NOT_PAID . ' to ' . Order::PAID;
        }else{
            $order->paid = 0;
            $order->status = Order::NOT_PAID;
            $comment = 'Status changed from ' . Order::PAID . ' to ' . Order::NOT_PAID;
        }

        if($order->save()){
            OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$order->id, 'comment'=>$comment]);
            $this->setFlashMessage('Updated!!! ' . $comment, 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to updating record.', 2);
        }

        return response()->json($order);
    }
    
}

<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Helpers\CurrencyHelper;
use App\Helpers\LabelHelper;
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
use App\Models\Admin\Orders\PartPayment;
use App\Models\Admin\Views\ItemView;
use App\Models\Admin\Views\OrderView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class OrdersController extends Controller
{
    protected $colors;

    /**
     * A list of colors for representing charts
     */
    public function __construct()
    {
        $this->colors = [
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000',
            '#FF0F00', '#FF6600', '#FF9E01', '#FCD202', '#F8FF01', '#B0DE09', '#04D215', '#0D8ECF', '#0D52D1', '#2A0CD0', '#8A0CCF',
            '#CD0D74', '#754DEB', '#DDDDDD', '#CCCCCC', '#999999', '#333333', '#000000',
        ];

        parent::__construct();
    }
    
    /**
     * Display a listing of the Orders.
     *
     * @return Response
     */
    public function getIndex()
    {
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');
        $classlevels = ClassLevel::lists('classlevel', 'classlevel_id')
            ->prepend('- Class Level -', '');
        $items = Item::where('status', 1)
            ->where('item_type_id', '<>', ItemType::TERMLY)
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
                $object->term = $term->academic_term;
                $object->order_id = $order->order_id;
                $object->fullname = $order->fullname;
                $object->number = $order->number;
                $object->status = '<span class="label label-'.$status.'">'.$order->status.'</span>';
                $object->amount = CurrencyHelper::format($order->amount);
                $object->paid = $order->paid;
                $object->student_no = $order->student_no;
                $object->name = $order->fullname;
                $object->backend = ($order->backend) ? LabelHelper::info('Admin') : LabelHelper::default('Sponsor') ;
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
     * Update an order
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postOrderUpdate(Request $request)
    {
        $discount = $paid = $is_part_payment = null;
        $inputs = $request->all();
        $order = Order::findOrFail($inputs['order_id']);

        if($order->backend == Order::FRONTEND && $order->paid == Order::PAID){
            $this->setFlashMessage('Warning!!! Order: '.$order->number.' status cannot be updated, kindly contact systems admin.', 2);

            return response()->json($order);
        }

        if($order->paid != intval($inputs['paid'])){
            $paid = ' Status changed from ' . Order::ORDER_STATUSES[$order->paid] . ' to ' . Order::ORDER_STATUSES[$inputs['paid']] . ', ';
            $order->paid = $inputs['paid'];
            $order->status = Order::ORDER_STATUSES[intval($inputs['paid'])];
            $order->backend = ($order->paid == Order::PAID) ? Order::BACKEND : Order::FRONTEND;
        }

        if($order->is_part_payment != intval($inputs['is_part_payment'])){
            $is_part_payment = ' Payment Type changed from ' . PartPayment::PAYMENT_TYPES[$order->is_part_payment] . ' to ' . PartPayment::PAYMENT_TYPES[$order->is_part_payment];
            $order->is_part_payment = $inputs['is_part_payment'];
        }

        if($order->discount != intval($inputs['discount'])){
            var_dump($order->amount);
            $discount = ' Discount changed from ' . $order->discount . '% to ' . $inputs['discount'] . '%, ';
            $order->discount = intval($inputs['discount']);
            $order->amount = $order->getDiscountedAmount();
        }

        if($order->save()){
            $order->updateAmount();

            if($discount !== null || $paid !== null || $is_part_payment !== null)
                OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$order->id, 'comment'=>($discount . $paid . $is_part_payment)]);

            $this->setFlashMessage('  Updated!!! ' . $order->number . ' successfully modified.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to adjust record.', 2);
        }

        echo json_encode($order);
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
        $comment = 'Order Item: ' . $orderItem->id . ' Deleted on ' . date('Y-m-d h:i:s');
        $delete = !empty($orderItem) ? $orderItem->delete() : false;

        if($delete){
            $orderItem->order->updateAmount();
            OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$orderItem->order_id, 'comment'=>$comment]);

            $this->setFlashMessage('  Deleted!!! ' . $orderItem->item->name . ' deleted from the student billings.', 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to adjust record.', 2);
        }
    }

    /**
     * Update an order item amount/discount
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postItemUpdateAmount(Request $request)
    {
        $inputs = $request->all();
        $item = OrderItem::findOrFail($inputs['order_item_id']);
        $discount = ($item->discount != intval($inputs['discount'])) ? ' Item: ' . $item->id . ' Discount changed from ' . $item->discount . ' to ' . $inputs['discount'] : null;
        $item->discount = $inputs['discount'];
        $item->amount = $item->getDiscountedAmount();

        if($item->save()){
            $item->order->updateAmount();
            $this->setFlashMessage('  Updated!!! ' . $item->item->name . ' successfully modified.', 1);

            if($discount != null)
                OrderLog::create(['user_id' => Auth::id(), 'order_id' => $item->order_id, 'comment' => $discount]);

        }else{
            $this->setFlashMessage('Error!!! Unable to adjust record.', 2);
        }

        echo json_encode($item);
    }

    /**
     * Add/Edit Part Payments
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postPartPayments(Request $request)
    {
        $inputs = $request->all();

        $part = (!empty($inputs['part_id']) || $inputs['part_id'] != '') ? PartPayment::find($inputs['part_id']) : new PartPayment();
        if($part->amount != intval($inputs['amount']) && !empty($inputs['part_id']) || $inputs['part_id'] != ''){
            $comment =  'Part Payment: ' . $part->id . ' Amount changed from ' . $part->amount . ' to ' . $inputs['amount'];
        }
        $part->amount = $inputs['amount'];
        $part->order_id = $inputs['order_id'];
        $part->user_id = Auth::id();

        if($part->save()){
            $part->order->amount_paid = $part->order->partPayments()->lists('amount')->sum();
            $part->order->paid = Order::PAID;
            $part->order->status = Order::paid();
            $part->order->updateAmount();
            $this->setFlashMessage(" Updated!!! {$part->amount} Added on to Order: {$part->order->number} successfully.", 1);

            if(isset($comment))
                OrderLog::create(['user_id' => Auth::id(), 'order_id' => $part->order_id, 'comment' => $comment]);

        }else{
            $this->setFlashMessage('Error!!! Unable to adjust record.', 2);
        }

        echo json_encode($part);
    }

    /**
     * Soft Delete an order part payment
     *
     * @param Int $id
     * @return Response
     */
    public function getDeletePartPayment($id)
    {
        $part = PartPayment::findOrFail($id);
        $comment =  'Part Payment: ' . $part->id . ' Deleted on ' . date('Y-m-d h:i:s');
        $delete = !empty($part) ? $part->delete() : false;

        if($delete){
            $part->order->amount_paid = $part->order->partPayments()->lists('amount')->sum();
            if($part->order->partPayments->count() == 0){
                $part->order->paid = Order::NOT_PAID;
                $part->order->status = Order::notPaid();
            }
            $part->order->updateAmount();
            OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$part->order_id, 'comment'=>$comment]);

            $this->setFlashMessage(" Deleted!!! ' . $part->amount . ' deleted from Order: {$part->order->number} successfully", 1);
        }else{
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Update Order Status
     *
     * @param Int $orderId
     * @return Response
     */
    public function getStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        if($order->backend == Order::FRONTEND && $order->paid == Order::PAID){
            $this->setFlashMessage('Warning!!! Order: '.$order->number.' status cannot be updated, kindly contact systems admin.', 2);

            return response()->json($order);
        }

        $paid = $order->paid;
        $stat = !$paid ? Order::notPaid() . ' to ' . Order::paid() : Order::paid() . ' to ' . Order::notPaid();
        $order->paid = !$paid;
        $order->backend = ($order->paid == Order::PAID) ? Order::BACKEND : Order::FRONTEND;
        $order->status = !$paid ? Order::paid() : Order::notPaid();
        $comment = 'Order: ' . $order->number . ' Status changed from ' . $stat;

        if($order->save()){
            OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$order->id, 'comment'=>$comment]);
            $this->setFlashMessage('Updated!!! ' . $comment, 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to update record status.', 2);
        }

        return response()->json($order);
    }

    /**
     * Display a listing of the resource.
     * @param Boolean $term
     * @param Boolean $year
     * @return Response
     */
    public function getDashboard($term=false, $year=false)
    {
        $academic_term = ($term) ? AcademicTerm::findOrFail($this->decode($term)) : AcademicTerm::activeTerm();
        $academic_year = ($year) ? AcademicYear::findOrFail($this->decode($year)) : AcademicYear::activeYear();
        $academic_years = AcademicYear::lists('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');

        $pendingAmount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->notPaid()
            ->activeStudent()
            ->lists('amount')
            ->sum();
        $paidAmount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->paid()
            ->activeStudent()
            ->lists('amount')
            ->sum();
        $totalAmount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->activeStudent()
            ->lists('amount')
            ->sum();
        $studentCount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->activeStudent()
            ->lists('student_id')
            ->count();

        return view('admin.orders.dashboard',
            compact(
                'sponsors_count','staff_count', 'students_count', 'unmarked', 
                'academic_years', 'academic_year', 'academic_term',
                'pendingAmount', 'paidAmount', 'totalAmount', 'studentCount'
            )
        );
    }

    /**
     * Gets Paid Items
     *
     * @param $termId
     * @return Response
     */
    public function getPaidItems($termId)
    {
        $items = ItemView::where('academic_term_id', $termId)
            ->select(DB::raw('SUM(amount) as amount, item_name'))
            ->activeStudent()
            ->paid()
            ->notDeleted()
            ->groupBy('item_id')
            ->orderBy('item_name')
            ->get(['amount', 'item_name']);

        return $this->itemHelper($items);
    }

    /**
     * Gets Pending Items
     *
     * @param $termId
     * @return Response
     */
    public function getPendingItems($termId)
    {
        $items = ItemView::where('academic_term_id', $termId)
            ->select(DB::raw('SUM(amount) as amount, item_name'))
            ->activeStudent()
            ->notPaid()
            ->notDeleted()
            ->groupBy('item_id')
            ->orderBy('item_name')
            ->get(['amount', 'item_name']);

        return $this->itemHelper($items);
    }

    /**
     * Gets Expected Items
     *
     * @param $termId
     * @return Response
     */
    public function getExpectedItems($termId)
    {
        $items = ItemView::where('academic_term_id', $termId)
            ->select(DB::raw('SUM(amount) as amount, item_name'))
            ->activeStudent()
            ->notDeleted()
            ->groupBy('item_id')
            ->orderBy('item_name')
            ->get(['amount', 'item_name']);

        return $this->itemHelper($items);
    }

    /**
     * Get The Orders Analytics Given the class term id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function postDashboard(Request $request)
    {
        $inputs = $request->all();
        $year = $this->encode($inputs['academic_year_id']);
        $term = $this->encode($inputs['academic_term_id']);
        return redirect('/orders/dashboard/' . $term . '/' . $year);
    }

    /**
     * @param $items
     * @return mixed
     */
    private function itemHelper($items){
        $response = [];
        $color = 0;
        foreach($items as $item){
            $response[] = array(
                'item'=>$item->item_name,
                'amount'=>$item->amount,
                'color'=>$this->colors[$color++]
            );
        }
        return response()->json($response);
    }

    /**
     * Gets Paid Transactions
     *
     * @param $termId
     * @return Response
     */
    public function paid($termId=false)
    {
        $term = ($termId) ? AcademicTerm::findOrFail($this->decode($termId)) : AcademicTerm::activeTerm();
        $orders = OrderView::where('academic_term_id', $term->academic_term_id)
            ->paid()
            ->activeStudent()
            ->orderBy('fullname')
            ->get();
        $type = 'Paid';

        return view('admin.orders.summary', compact('orders', 'term', 'type'));
    }

    /**
     * Gets Not Paid Transactions
     *
     * @param $termId
     * @return Response
     */
    public function notPaid($termId=false)
    {
        $term = ($termId) ? AcademicTerm::findOrFail($this->decode($termId)) : AcademicTerm::activeTerm();
        $orders = OrderView::where('academic_term_id', $term->academic_term_id)
            ->notPaid()
            ->activeStudent()
            ->orderBy('fullname')
            ->get();
        $type = 'Not-Paid';

        return view('admin.orders.summary', compact('orders', 'term', 'type'));
    }

    /**
     * Gets All Order Transactions
     *
     * @param $termId
     * @return Response
     */
    public function allOrders($termId=false)
    {
        $term = ($termId) ? AcademicTerm::findOrFail($this->decode($termId)) : AcademicTerm::activeTerm();
        $orders = OrderView::where('academic_term_id', $term->academic_term_id)
            ->activeStudent()
            ->orderBy('fullname')
            ->get();
        $type = 'All-Orders';

        return view('admin.orders.summary', compact('orders', 'term', 'type'));
    }

    /**
     * Gets Paid/Not Paid Orders based on percentage
     * 
     * @param $termId
     * @return Response
     */
    public function percentage($termId=false)
    {
        $term = ($termId) ? AcademicTerm::findOrFail($termId) : AcademicTerm::activeTerm();
        $notPaid = OrderView::where('academic_term_id', $term->academic_term_id)
            ->notPaid()
            ->activeStudent()
            ->count();

        $paid = OrderView::where('academic_term_id', $term->academic_term_id)
            ->paid()
            ->activeStudent()
            ->count();
        $response[] = ['label'=>'Paid', 'color'=>'#0F0', 'data'=>$paid, 'value'=>$paid];
        $response[] = ['label'=>'Not-Paid', 'color'=>'#F00', 'data'=>$notPaid, 'value'=>$notPaid];

        return response()->json($response);
    }
}

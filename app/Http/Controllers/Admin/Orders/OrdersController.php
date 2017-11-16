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
    public function index()
    {
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');
        $classlevels = ClassLevel::pluck('classlevel', 'classlevel_id')
            ->prepend('- Class Level -', '');
        $items = Item::where('status', 1)
            ->where('item_type_id', '<>', ItemType::TERMLY)
            ->pluck('name', 'id')
            ->prepend('- Select Item -', '');

        return view('admin.orders.index', compact('academic_years', 'classlevels', 'items'));
    }

    /**
     * Search For Students in a classroom for an academic term to view Orders
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function search(Request $request)
    {
        session()->put('order-tab', 'view-order');
        $inputs = $request->all();
        $output = [];
        $response['flag'] = 0;
        $term = AcademicTerm::findOrFail($inputs['academic_term_id']);

        if (!empty($inputs['classroom_id'])) {
            $orders = OrderView::where('academic_term_id', $inputs['academic_term_id'])
                ->where('classroom_id', $inputs['classroom_id'])
                ->get();
        } else {
            $orders = OrderView::where('academic_term_id', $inputs['academic_term_id'])
                ->whereIn(
                    'classroom_id',
                    ClassRoom::where('classlevel_id', $inputs['classlevel_id'])
                        ->pluck('classroom_id')
                        ->toArray()
                )
                ->get();
        }

        if (!empty($orders)) {
            //All the students in the class room for the academic year
            foreach ($orders as $order) {
                $object = new stdClass();
                $status = ($order->paid) ? 'success' : 'danger';
                
                $object->student_id = $this->encode($order->student_id);
                $object->term_id = $this->encode($term->academic_term_id);
                $object->term = $term->academic_term;
                $object->order_id = $order->order_id;
                $object->fullname = $order->fullname;
                $object->number = $order->number;
                $object->status = '<span class="label label-sm label-'.$status.'">'.$order->status.'</span>';

                $object->total_amount = CurrencyHelper::format((float) $order->total_amount);
                $object->amount_paid = CurrencyHelper::format((float) $order->amount_paid);
                //$object->outstanding = CurrencyHelper::format((float) ($order->amount - $order->amount_paid));
                $object->paid = $order->paid;
                $object->is_part_payment = ($order->is_part_payment) ? LabelHelper::info('Part') : LabelHelper::default('Full') ;
                $object->classroom = $order->classroom;
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b) {
                return strcmp($a->fullname, $b->fullname);
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
    public function searchStudents(Request $request)
    {
        session()->put('order-tab', 'adjust-order');
        $inputs = $request->all();
        $students = $output = [];
        $term = AcademicTerm::findOrFail($inputs['view_academic_term_id']);

        if (!empty($inputs['view_classroom_id'])) {
            $students = StudentClass::where('academic_year_id', $inputs['view_academic_year_id'])
                ->where('classroom_id', $inputs['view_classroom_id'])
                ->whereIn('student_id',
                    Student::where('status_id', 1)
                        ->pluck('student_id')
                        ->toArray()
                )
                ->get();
        }

        $response = [];
        $response['flag'] = 0;

        if (!empty($students)) {
            //All the students in the class room for the academic year
            foreach ($students as $student) {
                $object = new stdClass();
                $object->student_id = $this->encode($student->student_id);
                $object->term_id = $this->encode($term->academic_term_id);
                $object->student_no = $student->student()->first()->student_no;
                $object->name = $student->student()->first()->fullNames();
                $object->gender = $student->student()->first()->gender;
                $output[] = $object;
            }
            //Sort The Students by name
            usort($output, function($a, $b) {
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
    public function orderUpdate(Request $request)
    {
        $discount = $paid = $is_part_payment = null;
        $inputs = $request->all();
        $order = Order::findOrFail($inputs['order_id']);

        if ($order->backend == Order::FRONTEND && $order->paid == Order::PAID) {
            $this->setFlashMessage('Warning!!! Order: '.$order->number.' status cannot be updated, kindly contact systems admin.', 2);

            return response()->json($order);
        }

        if ($order->paid != intval($inputs['paid'])) {
            $paid = ' Status changed from ' . Order::ORDER_STATUSES[$order->paid] . ' to ' . Order::ORDER_STATUSES[$inputs['paid']] . ', ';
            $order->paid = $inputs['paid'];
            $order->status = Order::ORDER_STATUSES[intval($inputs['paid'])];
            $order->backend = ($order->paid == Order::PAID) ? Order::BACKEND : Order::FRONTEND;
        }

        if ($order->is_part_payment != intval($inputs['is_part_payment'])) {
            $is_part_payment = ' Payment Type changed from '
                . PartPayment::PAYMENT_TYPES[$order->is_part_payment] . ' to '
                . PartPayment::PAYMENT_TYPES[$order->is_part_payment];
            $order->is_part_payment = $inputs['is_part_payment'];
        }

        if ($order->discount != intval($inputs['discount'])) {
            var_dump($order->amount);
            $discount = ' Discount changed from ' . $order->discount . '% to ' . $inputs['discount'] . '%, ';
            $order->discount = intval($inputs['discount']);
            $order->amount = $order->getDiscountedAmount();
        }

        if ($order->save()) {
            $order->updateAmount();

            if ($discount !== null || $paid !== null || $is_part_payment !== null) {
                OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$order->id, 'comment'=>($discount . $paid . $is_part_payment)]);
            }
            $this->setFlashMessage('  Updated!!! ' . $order->number . ' successfully modified.', 1);
        } else {
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
    public function items($studentId, $termId)
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
    public function deleteItem($itemId)
    {
        $orderItem = OrderItem::findOrFail($itemId);
        $comment = 'Order Item: ' . $orderItem->id . ' Deleted on ' . date('Y-m-d h:i:s');
        $delete = !empty($orderItem) ? $orderItem->delete() : false;

        if ($delete) {
            $orderItem->order->updateAmount();
            OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$orderItem->order_id, 'comment'=>$comment]);

            $this->setFlashMessage('  Deleted!!! ' . $orderItem->item->name . ' deleted from the student billings.', 1);
        } else {
            $this->setFlashMessage('Error!!! Unable to adjust record.', 2);
        }
    }

    /**
     * Update an order item amount/discount
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function itemUpdateAmount(Request $request)
    {
        $inputs = $request->all();
        $item = OrderItem::findOrFail($inputs['order_item_id']);
        $discount = ($item->discount != intval($inputs['discount']))
            ? ' Item: ' . $item->id . ' Discount changed from ' . $item->discount . ' to ' . $inputs['discount']
            : null;
        $item->discount = $inputs['discount'];
        $item->amount = $item->getDiscountedAmount();

        if ($item->save()) {
            $item->order->updateAmount();
            $this->setFlashMessage('  Updated!!! ' . $item->item->name . ' successfully modified.', 1);

            if ($discount != null) {
                OrderLog::create(['user_id' => Auth::id(), 'order_id' => $item->order_id, 'comment' => $discount]);
            }
        } else {
            $this->setFlashMessage('Error!!! Unable to adjust record.', 2);
        }

        echo json_encode($item);
    }

    /**
     * Add/Edit Part Payments
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function partPayments(Request $request)
    {
        $inputs = $request->all();

        $part = (!empty($inputs['part_id']) || $inputs['part_id'] != '')
            ? PartPayment::find($inputs['part_id'])
            : new PartPayment();

        if ($part->amount != intval($inputs['amount']) && !empty($inputs['part_id']) || $inputs['part_id'] != '') {
            $comment =  'Part Payment: ' . $part->id . ' Amount changed from '
                . $part->amount . ' to ' . $inputs['amount'];
        }

        $part->amount = $inputs['amount'];
        $part->order_id = $inputs['order_id'];
        $part->user_id = Auth::id();

        if ($part->save()) {
            $part->order->paid = Order::PAID;
            $part->order->status = Order::paid();
            $part->order->updateAmount();
            $this->setFlashMessage(
                " Updated!!! {$part->amount} Added on to Order: {$part->order->number} successfully.", 1
            );

            if (isset($comment)) {
                OrderLog::create(['user_id' => Auth::id(), 'order_id' => $part->order_id, 'comment' => $comment]);
            }
        } else {
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
    public function deletePartPayment($id)
    {
        $part = PartPayment::findOrFail($id);
        $comment =  'Part Payment: ' . $part->id . ' Deleted on ' . date('Y-m-d h:i:s');
        $delete = !empty($part) ? $part->delete() : false;

        if ($delete) {
            if ($part->order->partPayments->count() == 0) {
                $part->order->paid = Order::NOT_PAID;
                $part->order->status = Order::notPaid();
            }

            $part->order->updateAmount();
            OrderLog::create(['user_id'=>Auth::id(), 'order_id'=>$part->order_id, 'comment'=>$comment]);

            $this->setFlashMessage(
                " Deleted!!! ' . $part->amount . ' deleted from Order: {$part->order->number} successfully", 1
            );
        } else {
            $this->setFlashMessage('Error!!! Unable to delete record.', 2);
        }
    }

    /**
     * Update Order Status
     *
     * @param Int $orderId
     * @return Response
     */
    public function status($orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->backend == Order::FRONTEND && $order->paid == Order::PAID) {
            $this->setFlashMessage(
                'Warning!!! Order: '.$order->number.' status cannot be updated, kindly contact systems admin.', 2)
            ;

            return response()->json($order);
        }

        $paid = $order->paid;
        $stat = !$paid ? Order::notPaid() . ' to ' . Order::paid() : Order::paid() . ' to ' . Order::notPaid();
        $order->paid = !$paid;
        $order->backend = ($order->paid == Order::PAID) ? Order::BACKEND : Order::FRONTEND;
        $order->status = !$paid ? Order::paid() : Order::notPaid();
        $order->amount_paid = $order->paid == Order::PAID ? $order->amount : 0;
        $comment = 'Order: ' . $order->number . ' Status changed from ' . $stat;

        if ($order->save()) {
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
     * @return Response
     */
    public function dashboard($term=false)
    {
        $academic_term = ($term) 
            ? AcademicTerm::findOrFail($this->decode($term)) 
            : AcademicTerm::activeTerm();
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')
            ->prepend('- Academic Year -', '');
        $pendingAmount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->notPaid()
            ->activeStudent()
            ->pluck('amount')
            ->sum();
        $paidAmount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->paid()
            ->activeStudent()
            ->pluck('amount')
            ->sum();
        $totalAmount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->activeStudent()
            ->pluck('amount')
            ->sum();
        $studentCount = OrderView::where('academic_term_id', $academic_term->academic_term_id)
            ->activeStudent()
            ->pluck('student_id')
            ->count();

        return view('admin.orders.dashboard',
            compact(
                'sponsors_count','staff_count', 'students_count', 'unmarked', 'academic_years',
                'academic_term', 'pendingAmount', 'paidAmount', 'totalAmount', 'studentCount'
            )
        );
    }

    /**
     * Gets Paid Items
     *
     * @param $termId
     * @return Response
     */
    public function paidItems($termId)
    {
        $items = ItemView::where('academic_term_id', $termId)
            ->select(DB::raw('SUM(amount) as amount, item_name'))
            ->activeStudent()
            ->paid()
            ->notDeleted()
            ->groupBy('item_id')
            ->orderBy('item_name')
            ->get(['amount', 'item_name']);

        return $this->_itemHelper($items);
    }

    /**
     * Gets Pending Items
     *
     * @param $termId
     * @return Response
     */
    public function pendingItems($termId)
    {
        $items = ItemView::where('academic_term_id', $termId)
            ->select(DB::raw('SUM(amount) as amount, item_name'))
            ->activeStudent()
            ->notPaid()
            ->notDeleted()
            ->groupBy('item_id')
            ->orderBy('item_name')
            ->get(['amount', 'item_name']);

        return $this->_itemHelper($items);
    }

    /**
     * Gets Expected Items
     *
     * @param $termId
     * @return Response
     */
    public function expectedItems($termId)
    {
        $items = ItemView::where('academic_term_id', $termId)
            ->select(DB::raw('SUM(amount) as amount, item_name'))
            ->activeStudent()
            ->notDeleted()
            ->groupBy('item_id')
            ->orderBy('item_name')
            ->get(['amount', 'item_name']);

        return $this->_itemHelper($items);
    }

    /**
     * Get The Orders Analytics Given the class term id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function filterDashboard(Request $request)
    {
        $inputs = $request->all();
        $term = $this->encode($inputs['academic_term_id']);
        
        return redirect('/orders/dashboard/' . $term );
    }

    /**
     * @param $items
     * @return mixed
     */
    private function _itemHelper($items){
        $response = [];
        $color = 0;
        
        foreach ($items as $item) {
            $response[] = [
                'item'=>$item->item_name,
                'amount'=>$item->amount,
                'color'=>$this->colors[$color++]
            ];
        }
        
        return response()->json($response);
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

    /**
     * Gets Paid Transactions
     *
     * @param $termId
     * @return Response
     */
    public function paid($termId=false)
    {
        return $this->all($termId, 'Paid', Order::PAID);
    }

    /**
     * Gets Not Paid Transactions
     *
     * @param $termId
     * @return Response
     */
    public function notPaid($termId=false)
    {
        return $this->all($termId, 'Not-Paid', Order::NOT_PAID);
    }

    /**
     * Gets Not Paid Transactions
     *
     * @param $termId
     * @return Response
     */
    public function cancelled($termId=false)
    {
        return $this->all($termId, 'Cancelled', Order::CANCELLED);
    }

    /**
     * Gets All Order Transactions
     *
     * @param $termId
     * @return Response
     */
    public function allOrders($termId=false)
    {
        return $this->all($termId);
    }

    public function summary(Request $request)
    {
        $inputs = $request->all();
        $type =$inputs['order_type'];
        $term = $this->encode($inputs['academic_term_id']);
        
        return redirect("/orders/{$type}/{$term}");
    }

    /**
     * Gets All Order Transactions
     *
     * @param $type
     * @param $condition
     * @param $termId
     * @return Response
     */
    private function all($termId=false, $type='All-Orders', $condition=-1)
    {
        $term = ($termId) ? AcademicTerm::findOrFail($this->decode($termId)) : AcademicTerm::activeTerm();
        $academic_years = AcademicYear::pluck('academic_year', 'academic_year_id')->prepend('- Academic Year -', '');
        $conditions = "term=".$term->academic_term_id."&status=".$condition;

        return view('admin.orders.summary', compact('academic_years', 'term', 'type', 'conditions'));
    }

    /**
     * Display a listing of the Users using Ajax Datatable.
     * @param Request $request
     * @return Response
     */
    public function data(Request $request)
    {
        $termId = $request->input('term');
        $condition = $request->input('status');
        $term = (!empty($termId)) ? AcademicTerm::findOrFail($termId) : AcademicTerm::activeTerm();

        $iTotalRecords = OrderView::where('academic_term_id', $termId)->count();
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);

        $q = @$_REQUEST['sSearch'];

        $orders = OrderView::where('academic_term_id', $termId)
            ->orderBy('fullname')
            ->where(function ($query) use ($q, $condition) {
                if ($condition != -1) {
                    $query->where('paid', $condition);
                }
                //Filter by either number, name, no, classroom
                if (!empty($q)) {
                    $query->orWhere('fullname', 'like', '%'.$q.'%')
                        ->orWhere('number', 'like', '%'.$q.'%')
                        ->orWhere('status', 'like', '%'.$q.'%')
                        ->orWhere('student_no', 'like', '%'.$q.'%')
                        ->orWhere('gender', 'like', '%'.$q.'%')
                        ->orWhere('classroom', 'like', '%'.$q.'%');
                }
            });
        // iTotalDisplayRecords = filtered result count
        $iTotalDisplayRecords = $orders->count();
        $records["data"] = $records = [];

        $end = $iDisplayStart + $iDisplayLength;
        $end = $end > $iTotalRecords ? $iTotalRecords : $end;

        $i = $iDisplayStart;
        $allOrders = $orders->skip($iDisplayStart)->take($iDisplayLength)->get();
        
        foreach ($allOrders as $order) {
            $number = '<a href="/order/items/'.$this->encode($order->student_id).'/'.$this->encode($term->academic_term_id).'"
                           class="btn btn-link btn-xs sbold"><span style="font-size: 14px">'.$order->number.'</span>
                        </a>';
            $name = '<a href="/students/view/'.$this->encode($order->student_id).'"
                           class="btn btn-link btn-xs sbold"><span style="font-size: 14px">'.$order->fullname.'</span>
                        </a>';
            $print = '<a target="_blank" href="/invoices/order/'.$this->encode($order->order_id).'" class="btn btn-default btn-xs">
                            <i class="fa fa-print"></i> Print
                        </a>
                      <a target="_blank" href="/invoices/download/'.$this->encode($order->order_id).'" class="btn btn-primary btn-xs">
                            <i class="fa fa-download"></i> Download
                        </a>';
            $action = '<button  data-confirm-text="Yes, Undo Payment" data-name="'.$order->number.'" data-title="Order Status Update Confirmation"
                             data-message="Are you sure Order: <b>'.$order->number.'</b> meant for <b>'.$order->fullname.' has NOT being PAID, for '.$term->academic_term.'?</b>"
                             data-statusText="'.$order->number.'Order status updated to NOT-PAID" data-action="/orders/status/'.$order->order_id.'" data-status="Updated"
                             class="btn btn-warning btn-xs btn-sm confirm-delete-btn"> <span class="fa fa-undo"></span> Undo
                        </button>';
            if (!$order->paid) {
                $action = '<button  data-confirm-text="Yes, Confirm Payment" data-name="'.$order->number.'" data-title="Order Status Update Confirmation"
                             data-message="Are you sure Order: <b>'.$order->number.'</b> meant for <b>'.$order->fullname.' has being PAID, for '.$term->academic_term.'?</b>"
                             data-statusText="'.$order->number.' Order status updated to PAID" data-confirm-button="#44b6ae" data-action="/orders/status/'.$order->order_id.'" data-status="Updated"
                             class="btn btn-success btn-xs btn-sm confirm-delete-btn"> <span class="fa fa-save"></span> Update
                        </button>';
            }
            
            $action .= '<a href="/orders/items/'.$this->encode($order->student_id).'/'.$this->encode($term->academic_term_id).'"
                           class="btn btn-info btn-xs sbold"><i class="fa fa-eye"></i> View
                        </a>';
            $records["data"][] = [
                ($i++ + 1),
                $number,
                CurrencyHelper::format($order->total_amount, 0, true),
                CurrencyHelper::format($order->amount_paid, 0, true),
                ($order->is_part_payment) ? LabelHelper::info('Part') : LabelHelper::default('Full'),
                $order->getStatusLabel(),
                $name,
                $order->classroom,
                $print,
                $action
            ];
        }

        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = isset($iTotalDisplayRecords) ? $iTotalDisplayRecords :$iTotalRecords;

        echo json_encode($records);
    }
}

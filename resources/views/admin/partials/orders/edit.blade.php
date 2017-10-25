<!-- modal -->
<div id="edit_order_modal" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title text-center text-primary" id="modal-title-text-order">Edit Order Form</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-10 col-sm-12 col-md-offset-1">
                        <form method="POST" action="#" class="form" role="form" id="edit_order_form">
                            {!! csrf_field() !!}
                            {!! Form::hidden('order_id', '', ['id'=>'order_id']) !!}
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Amount ({{CurrencyHelper::NAIRA}}): <span class="sbold" id="total_amount"></span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                        {{--<span class="label label-default" id="order_amount"></span>--}}
                                        {!! Form::text('amount', '', ['id'=>'order_amount', 'placeholder'=>'Amount', 'class'=>'form-control', 'disabled'=>true]) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Discount: <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-money"></i></span>
                                        <select class="form-control" name="discount" id="order_discount" required>
                                            @for($i = 0; $i <= 100; $i+=5)
                                                <option value="{{$i}}">{{ $i }}%</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Status: <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-eyedropper"></i></span>
                                        <select class="form-control" name="paid" id="paid" required>
                                            @foreach(Order::ORDER_STATUSES as $key => $value)
                                                <option value="{{$key}}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Payment Type: <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="fa fa-th-large"></i></span>
                                        <select class="form-control" name="is_part_payment" id="is_part_payment" required>
                                            @foreach(PartPayment::PAYMENT_TYPES as $key => $value)
                                                <option value="{{$key}}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
                                <button type="submit" class="btn green">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.modal -->
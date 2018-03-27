<!-- modal -->
<div id="result_checker_modal" class="modal fade bs-modal-lg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title text-center font-blue" id="manage-title-text">
                    This result need to be activated with a SCRATCH CARD PIN to view/print result<br>
                    <small class="font-red">kindly enter the scratch card <i>Serial Number and Secret PIN Number</i>
                        <br>Contact your school for one if you don't have any</small>
                    {{--<small class="font-red">kindly enter the scratch card <i>Secret PIN Number</i> or contact your school for one</small>--}}
                </h4>
            </div>
            <form method="POST" action="#" class="form" role="form" id="result_checker_form">
                {!! csrf_field() !!}
                {!! Form::hidden('student_id', '', ['id'=>'student_id']) !!}
                {!! Form::hidden('academic_term_id', '', ['id'=>'term_id']) !!}
                <div class="modal-body">
                    <div id="msg_box_modal"></div>
                    <div class="scroller" style="height:220px;" data-always-visible="1" data-rail-visible1="1">
                        <div class="row">
                            <div class="form-body">
                                <div class="form-group col-md-8 col-md-offset-2">
                                    <label>Serial Number: <small class="font-red">*</small></label>
                                    <input type="text" maxlength="8" minlength="8" class="form-control" id="serial_number" required name="serial_number" placeholder="Card Serial Number">
                                </div>
                                <div class="form-group last col-md-8 col-md-offset-2">
                                    <label>PIN Number: <small class="font-red">*</small></label>
                                    <input type="text" maxlength="12" minlength="12" class="form-control" id="pin_number" required name="pin_number" placeholder="Card PIN Number">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Cancel</button>
                    <button type="submit" class="btn green" id="result_checker_btn" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Processing...">Proceed</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /.modal -->
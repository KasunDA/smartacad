/**
 * Created by Kheengz on 9/4/2017.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');
    
    //Edit Order Item Amount
    $(document.body).on('click', '.item-edit', function(){
        $('#modal-title-text').html('Edit Item Amount on: <b>' + $(this).data('item') +'</b>');
        $('#order_item_id').val($(this).data('id'));
        $('#amount').val($(this).data('amount'));
        $('#discount').val($(this).data('discount'));
        $('#edit_item_modal').modal('show');
    });

    //Update Order Item Amount
    $(document.body).on('submit', '#edit_item_form', function(){
        var values = $(this).serialize();

        App.blockUI({
            target: '#edit_item_modal',
            animate: true
        });

        $.ajax({
            type: "POST",
            url: '/orders/item-update-amount',
            data: values,
            success: function (data) {

                window.location.reload();
                window.setTimeout(function() {
                    App.unblockUI('#edit_item_modal');
                }, 2000);

            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                App.unblockUI('#edit_item_modal');
            }
        });
        return false;
    });
    
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#view_order_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#view_order_tab',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/orders/search',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    console.log(obj);
                    var assign = '<thead>\
                                <tr role="row" class="heading">\
                                    <th>#</th>\
                                    <th>Name</th>\
                                    <th>Class Room</th>\
                                    <th>Order No.</th>\
                                    <th>Amount (&#8358;)</th>\
                                    <th>Status</th>\
                                    <th>Details</th>\
                                    <th>Action</th>\
                                    <th>Source</th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Orders, function(key, order) {
                            var button = '<button  data-confirm-text="Yes, Confirm Payment" data-name="'+order.name+'" data-title="Order Status Update Confirmation" ' +
                            'data-message="Are you sure Order: <b>'+order.number+'</b> meant for <b>'+order.fullname+' has being PAID, for '+order.term+'?</b>" ' +
                            'data-statusText="'+order.number+' Order status updated to PAID" data-confirm-button="#44b6ae" data-status="Updated" ' +
                            'data-action="/orders/status/'+order.order_id+'" data-status="Updated" ' +
                            'class="btn btn-success btn-xs btn-sm confirm-delete-btn"><span class="fa fa-save"></span> Update</button>';

                            if(order.paid == 1) {
                                button = '<button  data-confirm-text="Yes, Undo Payment" data-name="'+order.name+'" data-title="Order Status Update Confirmation" ' +
                                'data-message="Are you sure Order: <b>'+order.number+'</b> meant for <b>'+order.fullname+' has NOT being PAID, for '+order.term+'?</b>" ' +
                                'data-statusText="'+order.number+' Order status updated to NOT-PAID" data-confirm-button="#44b6ae" data-status="Updated" ' +
                                'data-action="/orders/status/'+order.order_id+'" data-status="Updated" ' +
                                'class="btn btn-warning btn-xs btn-sm confirm-delete-btn"><span class="fa fa-undo"></span> Undo</button>';
                            }
                            
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+order.name+'</td>' +
                                '<td>'+order.classroom+'</td>' +
                                '<td>'+order.number+'</td>' +
                                '<td>'+order.amount+'</td>' +
                                '<td>'+order.status+'</td>' +
                                '<td><a href="/orders/items/'+order.student_id+'/'+ order.term_id + '"' +
                                ' class="btn btn-xs btn-info"> <i class="fa fa-eye"></i> Details</a></td>' +
                                '<td>'+button+'</td>' +
                                '<td>'+order.backend+'</td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_order_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_order_datatable')).refresh();
                    setTableData($('#view_order_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#view_order_tab');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_order_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#view_order_tab');
                }
            });
            return false;
        });
    };

    var handleSample2 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#adjust_order_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#adjust_order_tab',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/orders/search-students',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr role="row" class="heading">\
                                    <th>#</th>\
                                    <th>Student ID</th>\
                                    <th>Student Name</th>\
                                    <th>Gender</th>\
                                    <th>Action </th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Students, function(key, student) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+student.student_no+'</td>' +
                                '<td>'+student.name+'</td>' +
                                '<td>'+student.gender+'</td>' +
                                '<td><a href="/orders/items/'+student.student_id+'/'+ student.term_id + '"' +
                                ' class="btn btn-xs btn-info"> <i class="fa fa-send"></i> Proceed</a></td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#adjust_order_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#adjust_order_datatable')).refresh();
                    setTableData($('#adjust_order_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#adjust_order_tab');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#adjust_order_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#adjust_order_tab');
                }
            });
            return false;
        });
    };
 
    return {
        //main function to initiate the module
        init: function() {

            handleSample1();
            handleSample2();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});
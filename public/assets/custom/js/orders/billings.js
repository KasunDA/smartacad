/**
 * Created by Kheengz on 9/4/2017.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //Check All button click
    $(document.body).on('change', '.check-all', function () {
        var check_boxes = $('.check-one');
        //console.log(check_boxes);
        if($(this).is(':checked')){
            check_boxes.prop('checked', true);
            check_boxes.parents('tr').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            check_boxes.prop('checked', false);
            check_boxes.parents('tr').css({background : "#FFFFFF", color: "#434A54"});
        }
    });

    //Each Check box click
    $(document.body).on('change', '.check-one', function () {

        if($(this).is(':checked')){
            $(this).parents('tr').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            $('.check-all').prop('checked', false);
            $(this).parents('tr').css({background : "#FFFFFF", color: "#434A54"});
        }

    });

    //Each item on change
    $(document.body).on('change', '.each-item', function () {
        var amount = $(this).children('option:selected').data('amount');
        var td = $(this).parent('td').next()
        td.html('<b> '+amount+'</b>');
        // alert(amount);
    });

    $(document.body).on('click', '.billing-items', function(){
        $('#modal-title-text').html('Items Billing for a ' + $(this).data('type'));
        $('#type_id').val($(this).data('type-id'));
        $('#ids').val($(this).val());
        $('#billing_form').modal('show');
    });

    $(document.body).on('click', '#all-marked', function(){
        var ids = '';
        var count = 0;
        $('.check-one').each(function (i, e) {
            if($(e).is(':checked')) {
                ids += $(e).val() + ',';
                count++;
            }
        });
        var type = $(this).data('type');
        $('#modal-title-text').html('Items Billing for ' + count +': ' + $(this).data('type'));
        $('#type_id').val($(this).data('type-id'));
        $('#ids').val(ids);
        $('#billing_form').modal('show');
    });

    $(document.body).on('submit', '#items_billing_form', function(){
        var values = $(this).serialize();
        
        $.ajax({
            type: "POST",
            url: '/billings/item-variables',
            data: values,
            success: function (data) {

                window.setTimeout(function() {
                    App.unblockUI('#student_billing');
                }, 2000);
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                App.unblockUI('#student_billing');
            }
        });
        return false;
    });
    
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the initiate button is clicked
        $(document.body).on('submit', '#initiate_billings_form', function(){
            var values = $(this).serialize();

            swal({
                title: "Are you sure?",
                text: 'Do You want to: <span class="bold">Initiate Billings for all active Students</span>?',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Initiate it!",
                closeOnConfirm: false,
                html: true
                // timer: 3000,
                // allowOutsideClick: true
            },
            function(){
                $.ajax({
                    type: "POST",
                    url: '/billings/initiate-billings',
                    data: values,
                    async: true,
                    success: function(data,textStatus){
                        swal("Initiated!", "Billings initiate successfully", "success");
                        window.location.reload();
                    },
                    error: function(xhr,textStatus,error){
                        swal("Server Error!", "Error encountered please try again later...", "error");
                    }
                });
            });
            return false;
        });
    };
    
    var handleSample2 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#search_view_student_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#student_billing',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/billings/search-results',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr role="row" class="heading">\
                                    <th><input type="checkbox" class="group-checkable check-all"> </th>\
                                    <th>#</th>\
                                    <th>Student ID. / Term</th>\
                                    <th>Student Name / Class Room</th>\
                                    <th>Gender / Class Size</th>\
                                    <th>Action </th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Students, function(key, value) {
                            $('#all-marked').data('type', 'Student');
                            $('#all-marked').data('type-id', '1');
                            assign += '<tr>' +
                                '<td><input type="checkbox" class="check-one" name="student_id[]" value="'+value.student_id+'"></td>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.student_no+'</td>' +
                                '<td>'+value.name+'</td>' +
                                '<td>'+value.gender+'</td>' +
                                '<td><button data-type="Student" data-type-id="1" value="'+value.student_id+'"' +
                                ' class="btn btn-xs btn-warning billing-items"> <i class="fa fa-money"></i> Bill Student</button></td>' +
                                '</tr>';
                        });
                    }else if(obj.flag == 2){
                        $.each(obj.Classrooms, function(key, value) {
                            $('#all-marked').data('type', 'Class Room');
                            $('#all-marked').data('type-id', '2');
                            assign += '<tr>' +
                                '<td><input type="checkbox" class="check-one" name="class_id[]" value="'+value.classroom_id+'"></td>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.academic_term+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.student_count+' Student(s)</td>' +
                                '<td><button data-type="Class Room" data-type-id="2" value="'+value.classroom_id+'"' +
                                ' class="btn btn-xs btn-warning billing-items"> <i class="fa fa-money"></i> Bill Class</button></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_student_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_student_datatable')).refresh();
                    setTableData($('#view_student_datatable')).init();

                    $('#all-marked').removeClass('hide');
                    $('#term_id').val(obj.term_id);

                    var option = '<option value="">- Select Item -</option>';
                    //Set items
                    $.each(obj.Items, function(key, item) {
                        option += '<option data-amount="'+item.amount+'" value="'+item.id+'">'+item.name+'</option>';
                    });
                    $('#all_item_id').html(option);

                    window.setTimeout(function() {
                        App.unblockUI('#student_billing');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_student_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#student_billing');
                }
            });
            return false;
        });
    }
 
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
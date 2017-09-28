/**
 * Created by Kheengz on 9/4/2017.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');

    //Check All button click
    $(document.body).on('change', '.check-all', function () {
        var check_boxes = $( '.check-one' );
        var reasons = $( '.reasons' );
        // console.log(check_boxes);
        if($(this).is(':checked')){
            check_boxes.prop("checked", true);
            reasons.prop("disabled", true);
            reasons.val('');
            check_boxes.parents('tr').css({background : "#1BA39C", color: "#29343F"});
        }else{
            check_boxes.prop("checked", false);
            reasons.prop("disabled", false);
            check_boxes.parents('tr').css({background : "#e35b5a", color: "#434A54"});
        }
    });

    var inputElement = $( "input" );

    //Each Check box click
    $(document.body).on('change', '.check-one', function () {

        var input = $(this).parents('td').prev($( 'td' )).children( inputElement );
        if($(this).is(':checked')){
            $( input ).val('');
            $( input ).prop('disabled', true);
            $(this).parents('tr').css({background : "#1BA39C", color: "#29343F"});
        }else{
            $('.check-all').prop("checked", false);
            $( input ).prop('disabled', false);
            $(this).parents('tr').css({background : "#e35b5a", color: "#434A54"});
        }
    });

    $(document.body).on('click', '.check-td', function () {
        var input = $(this).parent().children(':last-child').find( inputElement );
        var td = $( input ).parents('td').prev($( 'td' )).children( inputElement );

        if($(input).prop('checked')){
            $(input).prop('checked', false);
            $( td ).prop('disabled', false);
            $(this).parent().css({background : "#e35b5a", color: "#434A54"});
        }else{
            $(input).prop('checked', true);
            $( td ).val('');
            $( td ).prop('disabled', true);
            $(this).parent().css({background : "#1BA39C", color: "#29343F"});
        }
    });
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#attendance_summary_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#attendance_summary_tab',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/attendances/summary',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr role="row" class="heading">\
                                    <th>#</th>\
                                    <th>Head Tutor</th>\
                                    <th>Class Room</th>\
                                    <th>Date Taken</th>\
                                    <th>Present</th>\
                                    <th>Absent</th>\
                                    <th>Details</th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Attendance, function(key, attend) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+attend.tutor+'</td>' +
                                '<td>'+attend.classroom+'</td>' +
                                '<td>'+attend.date_taken+'</td>' +
                                '<td>'+attend.present+'</td>' +
                                '<td>'+attend.absent+'</td>' +
                                '<td><a target="_blank" href="/attendances/details/'+attend.id + '"' +
                                ' class="btn btn-xs btn-info"> <i class="fa fa-eye"></i> View</a></td>'+
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#attendance_summary_datatable').html(assign);
                    setTableData($('#attendance_summary_datatable')).refresh();
                    setTableData($('#attendance_summary_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#attendance_summary_tab');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#attendance_summary_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#attendance_summary_tab');
                }
            });
            return false;
        });
    };

    return {
        //main function to initiate the module
        init: function() {

            handleSample1();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});

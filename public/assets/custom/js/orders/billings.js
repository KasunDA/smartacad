/**
 * Created by Kheengz on 9/4/2017.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
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
                        url: '/orders/initiate-billings',
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

            App.blockUI({
                target: '#assessment',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/assessments/subject-assigned',
                data: values,
                success: function (data) {
                  
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#assessment');
                }
            });
            return false;
        });
    };
    var handleSample2 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#assessment_report_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#assessment_report',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/assessments/search-students',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr>\
                                    <th>#</th>\
                                    <th>Student ID.</th>\
                                    <th>Student Name</th>\
                                    <th>Gender</th>\
                                    <th>View Result</th>\
                                    <th>Action </th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Students, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.student_no+'</td>' +
                                '<td>'+value.name+'</td>' +
                                '<td>'+value.gender+'</td>' +
                                '<td><a href="/assessments/report-details/'+value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-link"> <i class="fa fa-bookmark"></i> Proceed</a></td>' +
                                '<td><a href="/assessments/print-report/'+value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-link"> <i class="fa fa-print"></i> Print</a></td>' +
                                '</tr>';
                        });
                    }else {
                        set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2);
                        App.unblockUI('#assessment');
                    }
                    assign += '</tbody>';

                    $('#view_report_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_report_datatable')).refresh();
                    setTableData($('#view_report_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#assessment_report');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_report_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#assessment_report');
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
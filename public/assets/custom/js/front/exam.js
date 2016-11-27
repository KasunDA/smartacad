/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');

    $(document.body).on('submit', '#result_checker_form', function(){
        var values = $(this).serialize();
        if($('#serial_number').val() == '' || $('#pin_number').val() == ''){
            set_msg_box($('#msg_box_modal'), 'Serial and Pin numbers are required', 2);
        }else {
            $.ajax({
                type: "POST",
                url: '/wards-exams/result-checker',
                data: values,
                success: function (data) {
                    console.log(data);
                    // var obj = $.parseJSON(data);
                    if(data.flag == true){
                        set_msg_box($('#msg_box_modal'), 'Proceed', 1);
                        window.location.replace('/wards-exams/terminal-result/' + data.url);
                    }else {
                        set_msg_box($('#msg_box_modal'), 'Invalid Card Serial Number or Pin Number', 2);
                    }
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box_modal'), 'Error...Kindly Try Again', 2);
                }
            });
        }

        return false;
    });

});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#view_student_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#assessment',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/wards-exams/search-students',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr>\
                                    <th>#</th>\
                                    <th>Student ID.</th>\
                                    <th>Student Name</th>\
                                    <th>Gender</th>\
                                    <th>Class Room</th>\
                                    <th>View Result</th>\
                                    <th>Action </th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.Students, function(key, value) {
                            var hashed = value.hashed_stud+'/'+value.hashed_term;
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.student_no+'</td>' +
                                '<td>'+value.name+'</td>' +
                                '<td>'+value.gender+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td><button class="btn btn-link btn-sm check-result" rel="view" value="' + hashed + '"> <i class="fa fa-bookmark"></i> Proceed</button></td>' +
                                '<td><button class="btn btn-link btn-sm check-result" rel="print" value="' + hashed + '"> <i class="fa fa-print"></i> Print</button></td>' +
                                '</tr>';
                        });
                    }else {
                        set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                        App.unblockUI('#assessment');
                    }
                    assign += '</tbody>';

                    $('#view_student_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_student_datatable')).refresh();
                    setTableData($('#view_student_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#assessment');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_student_datatable'));
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
        $(document.body).on('click', '.check-result', function(){
            var hashed = $(this).val().split('/');
            var type = $(this).attr('rel');
            console.log(hashed + ': ' + type);

            App.blockUI({
                target: '#assessment',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/wards-exams/verify',
                data: {student_id:hashed[0], term_id:hashed[1]},
                success: function (data) {
                    if(data == true){
                        window.location.replace('/wards-exams/terminal-result/' + hashed[0] + '/' + hashed[1] + ((type == 'print') ? '/print' : '/'));
                    }else {
                        $('#student_id').val(hashed[0]);
                        $('#term_id').val(hashed[1]);
                        $('#result_checker_modal').modal('show');
                    }
                    window.setTimeout(function() {
                        App.unblockUI('#assessment');
                    }, 2000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#assessment');
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
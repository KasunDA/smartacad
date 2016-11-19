/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');

});

var UIBlockUI = function() {

    var handleSample2 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#view_student_form', function(){
            var values = $(this).serialize();
            var url = $('#display_url').val();
            var url2 = $('#display_url2').val();

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
                                    <th>View Result</th>\
                                    <th>Action </th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.Students, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.student_no+'</td>' +
                                '<td>'+value.name+'</td>' +
                                '<td>'+value.gender+'</td>' +
                                '<td><a href="' + url + value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-link"> <i class="fa fa-bookmark"></i> Proceed</a></td>' +
                                '<td><a href="' + url2 + value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-link"> <i class="fa fa-print"></i> Print</a></td>' +
                                //'<td><a href="/exams/print/'+value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i> Print</a></td>' +
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
    }

    return {
        //main function to initiate the module
        init: function() {
            handleSample2();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});
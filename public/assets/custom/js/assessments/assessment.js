/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    var tutors = $('#subject-tutors').clone();

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //Validate Scores So that it does'nt exceed the weight point assigned
    $(document.body).on('change', '.scores', function(){
        var val = parseInt($(this).val());
        var wp = parseInt($('#weight_point').val());
        if(val > wp || val < 0) {
            $(this).next('span').addClass('badge badge-danger badge-roundless');
            $(this).next('span').html('>= 0 and <= ' + wp);

        } else {
            $(this).next('span').html('');

        }
    });

    //Validate Before submitting the form
    $(document.body).on('submit', '#scores-form', function(e){
        //e.preventDefault();
        var check = 0;
        var wp = parseInt($('#weight_point').val());
        var name = '';
        $('.scores').each(function(index, elem){
            var value = parseInt($(elem).val());
            if(value > wp || value < 0){
                check = check + 1;
                name += '<li>' + $(elem).parent().parent().children(':nth-child(2)').html() + ': ' + $(elem).parent().parent().children(':nth-child(3)').html()
                    + ' Score(' + value + ') is less than 0 or more than ' + wp + '</li>'
            }
        });
        if(check > 0){
            set_msg_box($('#error-div'), '<ul>' + name + '</ul>', 2);
            return false;
        }
        return true;
    });
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#search_subject_staff', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#assessment',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/assessments/subject-assigned',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr>\
                                    <th>#</th>\
                                    <th>Academic Term</th>\
                                    <th>Subject</th>\
                                    <th>Class Room</th>\
                                    <th>Tutor</th>\
                                    <th>Action</th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.ClassSubjects, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.academic_term+'</td>' +
                                '<td>'+value.subject+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.tutor+'</td>' +
                                '<td><a href="/assessments/subject-details/'+value.hashed_id+'" class="btn btn-link"> Proceed</a></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#subject_assigned_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#subject_assigned_datatable')).refresh();
                    setTableData($('#subject_assigned_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#assessment');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#subject_assigned_datatable'));
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
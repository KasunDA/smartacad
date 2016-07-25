/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#setup_academic_year_id'), $('#setup_academic_term_id'), '/list-box/academic-term/');
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

    //Validate Before submitting the form
    $(document.body).on('submit', '#setup_exam_form', function(e){
        var values = $('#setup_exam_form').serialize();
        $.ajax({
            type: "POST",
            data: values,
            url: '/exams/validate-all-setup',
            success: function(data,textStatus){
                if(data.flag === 1){
                    //set_msg_box($('#error-box'), data.output, 2);
                    $('#exam-message').html(data.output);
                }else if(data.flag === 2){
                    $('#exam-message').html(data.output);
                }
                $('#confirm-btn').val(data.term);
                $('#exam_setup_modal').modal('show');
            },
            error: function(xhr,textStatus,error){
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
            }
        });
        return false;
    });

    //Submit the form to process the exam setup
    $(document.body).on('click', '#confirm-btn', function(e){
        $.ajax({
            type: "POST",
            data: {academic_term_id: $(this).val()},
            url: '/exams/all-setup',
            success: function(data,textStatus){
                console.log("Data", data);
                window.location.reload();
            },
            error: function(xhr,textStatus,error){
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
            }
        });
        return false;
    });
});

var UIBlockUI = function() {
    var handleSample0 = function() {

        //Setup My Exam Form when submitted
        $(document.body).on('submit', '#my_exam_setup_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#my_exams_setup',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/exams/validate-my-setup',
                data: values,
                success: function (data) {
                    // console.log(data);
                    if(data.flag === 1){
                        bootbox.dialog({
                            message: '<h4>Are You Sure You Want To Setup your exams for <strong>'+data.term.academic_term+' Academic Year? </strong>' +
                            '<span class="text-danger">Note: its not reversible</span></h4>',
                            title: '<h3><span class="text-primary">Exam Setup Confirmation</span></h3>',
                            buttons: {
                                danger: {
                                    label: "NO",
                                    className: "btn-danger",
                                    callback: function() {
                                        $(this).hide();
                                    }
                                },
                                success: {
                                    label: "YES Set It Up",
                                    className: "btn-success",
                                    callback: function() {
                                        $.ajax({
                                            type: 'POST',
                                            data:{academic_term_id: data.term.academic_term_id},
                                            url: '/exams/my-setup',
                                            success: function(data,textStatus){
                                                // console.log(data);
                                                window.location.replace('/exams');
                                            },
                                            error: function(xhr,textStatus,error){
                                                bootbox.alert("Error encountered pls try again later..", function() {
                                                    $(this).hide();
                                                });
                                            }
                                        });
                                    }
                                }
                            }
                        });
                    }else{
                        set_msg_box($('#error-box'), data.output, 2);
                    }

                    window.setTimeout(function() {
                        App.unblockUI('#my_exams_setup');
                    }, 2000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#my_exams_setup');
                }
            });
            return false;
        });
    }

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#search_subject_staff', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#exams_input_score',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/exams/subject-assigned',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr>\
                                    <th>#</th>\
                                    <th>Subject</th>\
                                    <th>Class Room</th>\
                                    <th>C. A</th>\
                                    <th>Exam</th>\
                                    <th>Status</th>\
                                    <th>Input Score</th>\
                                    <th>View Score</th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.Exam, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                //'<td>'+value.academic_term+'</td>' +
                                '<td>'+value.subject+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.ca_wp+'</td>' +
                                '<td>'+value.exam_wp+'</td>' +
                                //'<td>'+value.tutor+'</td>' +
                                '<td>'+value.marked+'</td>' +
                                '<td><a href="/exams/input-scores/'+value.hashed_id+'" class="btn btn-link"> <i class="fa fa-edit"></i> Proceed</a></td>' +
                                '<td><a href="/exams/view-scores/'+value.hashed_id+'" class="btn btn-link btn-info"> <i class="fa fa-eye"></i> View</a></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#subject_assigned_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#subject_assigned_datatable')).refresh();
                    setTableData($('#subject_assigned_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#exams_input_score');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#subject_assigned_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#exams_input_score');
                }
            });
            return false;
        });
    }

    var handleSample2 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#search_view_student_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#terminal',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/exams/search-results',
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
                                '<td><a href="/exams/student-terminal-result/'+value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-link"> <i class="fa fa-bookmark"></i> Proceed</a></td>' +
                                //'<td><a href="/exams/chart/'+value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-default btn-xs"> <i class="fa fa-bar-chart"></i> View</a></td>' +
                                //'<td><a href="/exams/print/'+value.hashed_stud+'/'+value.hashed_term+'" class="btn btn-primary btn-xs"> <i class="fa fa-eye"></i> Print</a></td>' +
                                '</tr>';
                        });
                    }else if(obj.flag === 2){
                        assign = '<thead>\
                                <tr>\
                                    <th>#</th>\
                                    <th>Academic Term.</th>\
                                    <th>Class Room</th>\
                                    <th>Class Room Size</th>\
                                    <th>View Result</th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                        $.each(obj.Classrooms, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.academic_term+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.student_count+' Student(s)</td>' +
                                '<td><a href="/exams/classroom-terminal-result/'+value.hashed_class+'/'+value.hashed_term+'" class="btn btn-link"> <i class="fa fa-bookmark"></i> Proceed</a></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_student_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_student_datatable')).refresh();
                    setTableData($('#view_student_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#terminal');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_student_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#terminal');
                }
            });
            return false;
        });
    }

    return {
        //main function to initiate the module
        init: function() {

            handleSample0();
            handleSample1();
            handleSample2();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});
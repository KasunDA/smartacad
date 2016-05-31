/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    var tutors = $('#tutors').clone();
    var old_btn;

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#student_classlevel_id'), $('#student_classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //When The Checkbox is Checked To Assign A Student to Class
    $(document.body).on('click', '.assign_student', function(){
        var class_id = $('#hidden_classroom_id').val();
        var year_id = $('#hidden_academic_year_id').val();
        var student_id = $(this).val();
        var student_class_id = $(this).prop('title');
        var parent_tr = $(this).parent().parent();
        //Assign A Students
        if($(this).prop('checked') === true){
            $(this).attr("checked", "checked");
            $.post('/class-rooms/assign', {student_class_id:student_class_id, student_id:student_id, class_id:class_id, year_id:year_id}, function(data){
                if(data !== '0'){
                    var title = parent_tr.children().next().next();
                    title.children().attr("title", data);
                    if($("#assign_student_tr").next().children().html() === "No Student Has Been Assigned"){
                        $("#assign_student_tr").next().remove();
                    }
                    $("#assign_student_tr").after("<tr><td>"+parent_tr.children().html()+"</td><td>"+parent_tr.children().next().html()+"</td><td>"+title.html()+"</td></tr>");
                    parent_tr.remove();
                }
            });
            //Remove An Assigned Examiner
        } else if($(this).prop('checked') === false){
            $(this).removeAttr("checked");
            $.post('/class-rooms/assign', {student_class_id:student_class_id, student_id:student_id}, function(data){
                if(data !== '0'){
                    var title = parent_tr.children().next().next();
                    title.children().attr("title", '-1');
                    if($("#available_student_tr").next().children().html() === "No Student Available"){
                        $("#available_student_tr").next().remove();
                    }
                    $("#available_student_tr").after("<tr><td>"+parent_tr.children().html()+"</td><td>"+parent_tr.children().next().html()+"</td><td>"+title.html()+"</td></tr>");
                    parent_tr.remove();
                }
            });
        }
        //return false;
    });

    //When the edit button is clicked show Tutors Drop Down
    $(document.body).on('click', '.edit-tutor', function(){
        var buttonTD = $(this).parent();
        tutors.removeClass('hide');
        var employees = tutors.clone();
        var subject_classroom_id = $(this).val();
        old_btn = $(this).clone();
        buttonTD.html(employees);
        employees.val($(this).attr('rel'));
        employees.prop('id', '');
        employees.attr('title', subject_classroom_id);
        employees.addClass('tutor_subject_select');
        buttonTD.children('select').focus();
    });

    //When No Changes is made to the Teachers Listbox //On Blur
    $(document.body).on('blur', '.tutor_subject_select', function(){
        var td = $(this).parent();
        td.html(old_btn);
    });

    //On Change of the employees name assign to the class
    $(document.body).on('change', '.tutor_subject_select', function(){
        var subject_classroom_id = $(this).attr('title');
        var buttonTD = $(this).parent();
        var tutor_id = $(this).val();
        var tutor = $(this).children('option:selected').text();

        $.ajax({
            type: "GET",
            url: '/subject-classrooms/assign-tutor/' + subject_classroom_id + '/' + tutor_id,
            success: function (data) {
                buttonTD.html('<button value="'+data+'" title="'+tutor_id+'" class="btn btn-link edit-tutor">\n\
                <i class="fa fa-edit"></i> '+tutor+'</button></td>');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
            }
        });
    });
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#assign_student_form', function(){
            var values = $(this).serialize();
            $('#hidden_classroom_id').val($('#student_classroom_id').val());
            $('#hidden_academic_year_id').val($('#student_academic_year_id').val());

            App.blockUI({
                target: '#assign2student',
                animate: true
            });

            $.post('/class-rooms/search-students', values, function(data){
                try{
                    var obj = $.parseJSON(data);
                    var available = '<caption><strong>List Of Available Students</strong></caption>\
                                    <tr id="available_student_tr">\
                                        <th >Student No.</th>\
                                        <th >Full Name</th>\
                                        <th ><i class="fa fa-check-square"></i> </th>\
                                    </tr>';
                    var assign = '<caption><strong>List Of Assigned Students</strong></caption>\
                                    <tr id="assign_student_tr">\
                                        <th >Student No.</th>\
                                        <th >Full Name</th>\
                                        <th ><i class="fa fa-times"></i> </th>\
                                    </tr>';

                    if(obj.flag2 == 1){
                        $.each(obj.StudentsNoClass, function(key, value) {
                            available += '<tr>\
                                <td>'+value.student_no+'</td>\n\
                                <td>'+value.name+'</td>\n\
                                <td><input type="checkbox" class="assign_student" title="-1" value="'+value.student_id+'"/> </td></tr>\n\
                            ';
                        });
                        $('#available_students').html(available);
                    }else if(obj.Flag2 === 0){
                        available += '<tr><th colspan="3">No Student Available</th></tr>';
                        $('#available_students').html(available);
                    }
                    if(obj.flag1 == 1){
                        $.each(obj.StudentsClass, function(key, value) {
                            assign += '<tr>\
                                <td>'+value.student_no+'</td>\n\
                                <td>'+value.name+'</td>\n\
                                <td><input type="checkbox" class="assign_student" title="'+value.student_class_id+'" checked="checked" value="'+value.student_id+'"/> </td></tr>\n\
                            ';
                        });
                        $('#assigned_students').html(assign);
                    }else if(obj.Flag === 0){
                        assign += '<tr><th colspan="3">No Student Has Been Assigned</th></tr>';
                        $('#assigned_students').html(assign);
                    }

                    window.setTimeout(function() {
                        App.unblockUI('#assign2student');
                    }, 2000);
                    $('#assign_student_table').removeClass('hide');
                    //Scroll To Div
                    scroll2Div($('#assign_student_table'));

                } catch (exception) {
                    $('#assigned_students').html(data);
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#assign2student');
                }
            });
            return false;
        });

        //When the search button is clicked for viewing Students
        $(document.body).on('submit', '#search_student_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#search4student',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/class-rooms/view-students',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Student No.</th>\
                                        <th>Student Name</th>\
                                        <th>Gender</th>\
                                        <th>Sponsor</th>\
                                        <th>Class Room</th>\
                                        <th>View</th>\
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
                                '<td><a href="/sponsors/view/'+value.sponsor_id+'" class="btn btn-link"><i class="fa fa-user"></i> '+value.sponsor+'</a></td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td><a href="/students/view/'+value.student_id+'" class="btn btn-link"><i class="fa fa-eye"></i> Proceed</a></td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_students_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_students_datatable')).refresh();
                    setTableData($('#view_students_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#search4student');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_students_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box1'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#search4student');
                }
            });
            return false;
        });

        //When the search button is clicked for Assigning Form Master Students
        $(document.body).on('submit', '#search_form_master_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#assign_formMaster',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/class-rooms/form-masters',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Student No.</th>\
                                        <th>Student Name</th>\
                                        <th>Gender</th>\
                                        <th>Sponsor</th>\
                                        <th>Class Room</th>\
                                        <th>View</th>\
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
                                '<td><a href="/sponsors/view/'+value.sponsor_id+'" class="btn btn-link"><i class="fa fa-user"></i> '+value.sponsor+'</a></td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td><a href="/students/view/'+value.student_id+'" class="btn btn-link"><i class="fa fa-eye"></i> Proceed</a></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#form_master_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#form_master_datatable')).refresh();
                    setTableData($('#form_master_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#assign_formMaster');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#form_master_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box2'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#assign_formMaster');
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
/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#student_classlevel_id'), $('#student_classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#from_classlevel_id'), $('#from_classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#to_classlevel_id'), $('#to_classroom_id'), '/list-box/classroom/');

    //When The Checkbox is Checked To Assign A Student to Class
    $(document.body).on('click', '.assign_student', function(){
        var class_id = $('#hidden_classroom_id').val();
        var year_id = $('#hidden_academic_year_id').val();
        var student_id = $(this).val();
        var student_class_id = $(this).prop('title');
        var parent_tr = $(this).parent().parent();
        //Assign A Students
        if($(this).prop('checked') == true){
            $(this).attr("checked", "checked");
            $.post('/class-students/assign', {student_class_id:student_class_id, student_id:student_id, class_id:class_id, year_id:year_id}, function(data){
                if(data !== '0'){
                    var title = parent_tr.children().next().next();
                    title.children().attr("title", data);
                    if($("#assign_student_tr").next().children().html() == "No Student Has Been Assigned"){
                        $("#assign_student_tr").next().remove();
                    }
                    $("#assign_student_tr").after("<tr><td>"+parent_tr.children().html()+"</td><td>"+parent_tr.children().next().html()+"</td><td>"+title.html()+"</td></tr>");
                    parent_tr.remove();
                }
            });
            //Remove An Assigned Examiner
        } else if($(this).prop('checked') == false){
            $(this).removeAttr("checked");
            $.post('/class-students/assign', {student_class_id:student_class_id, student_id:student_id}, function(data){
                if(data !== '0'){
                    var title = parent_tr.children().next().next();
                    title.children().attr("title", '-1');
                    if($("#available_student_tr").next().children().html() == "No Student Available"){
                        $("#available_student_tr").next().remove();
                    }
                    $("#available_student_tr").after("<tr><td>"+parent_tr.children().html()+"</td><td>"+parent_tr.children().next().html()+"</td><td>"+title.html()+"</td></tr>");
                    parent_tr.remove();
                }
            });
        }
        //return false;
    });

    //Validate if the academic term has been cloned
    $(document.body).on('submit', '#clone_students_assigned', function(e){
        var values = $('#clone_students_assigned').serialize();
        $.ajax({
            type: "POST",
            data: values,
            url: '/class-students/validate-clone',
            success: function(data,textStatus){
                if(data.flag == 1){
                    bootbox.dialog({
                        message: '<h4>Are You Sure You Want To Clone Students Class Records From <strong>'+data.from.academic_year+'</strong> to <strong>'+data.to.academic_year+'</strong>? ' +
                        '<span class="text-danger">Note: its not reversible</span></h4>',
                        title: '<span class="text-primary">Clone Record Confirmation</span>',
                        buttons: {
                            danger: {
                                label: "NO",
                                className: "btn-default",
                                callback: function() {
                                    $(this).hide();
                                }
                            },
                            success: {
                                label: "YES",
                                className: "btn-success",
                                callback: function() {
                                    $.ajax({
                                        type: 'POST',
                                        data:{from_year:data.from.academic_year_id, to_year:data.to.academic_year_id, from_class:data.from_class, to_class:data.to_class},
                                        url: '/class-students/cloning',
                                        success: function(data,textStatus){
                                            window.location.replace('/class-students');
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
                // $('#confirm-btn').val(data.term);
            },
            error: function(xhr,textStatus,error){
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
            }
        });
        return false;
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

            $.post('/class-students/search-students', values, function(data){
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
                    }else if(obj.flag2 == 0){
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
                    }else if(obj.flag1 == 0){
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
                url: '/class-students/view-students',
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
                    if(obj.flag == 1){
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

    };

    return {
        //main function to initiate the module
        init: function() {

            handleSample1();
        }
    };
}();

var ComponentsDropdowns = function () {

    var handleMultiSelect = function () {
        $('#subject_multi_select').multiSelect({
            selectableOptgroup: true,
            selectableHeader: '<span class="label label-info"><strong>List of Available Subjects</strong></span>',
            selectableFooter: '<span class="label label-info"><strong>List of Available Subjects</strong></span>',
            selectionHeader: '<span class="label label-success"><strong>List of Selected Subjects Offered</strong></span>',
            selectionFooter: '<span class="label label-success"><strong>List of Selected Subjects Offered</strong></span>',
            cssClass: 'multi-select-subjects'
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleMultiSelect();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();
    ComponentsDropdowns.init();
});

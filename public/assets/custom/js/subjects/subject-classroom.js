/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    var tutors = $('#subject-tutors').clone();
    var old_btn;

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#class_academic_year_id'), $('#class_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#level_academic_year_id'), $('#level_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#manage_academic_year_id'), $('#manage_academic_term_id'), '/list-box/academic-term/');

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#class_classlevel_id'), $('#class_classroom_id'), '/list-box/classroom/');
    getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //When The Assign Subject To Class Room Form is Submitted
    $(document.body).on('submit', '#assign_subject_form', function(){
        var values = $('#assign_subject_form').serialize();
        $.ajax({
            type: "POST",
            url: '/subject-classrooms/assign-subjects',
            data: values,
            success: function (data) {
                //console.log(data);
                window.location.replace('/subject-classrooms');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
            }
        });
        return false;
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

    //Delete a subject class room
    $(document.body).on('click', '.delete-subject',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var subject = parent.children(':nth-child(2)').html();
        var classroom = parent.children(':nth-child(3)').html();
        var id = $(this).val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete " + subject + " Subject in " + classroom + '?',
            title: '<span class="label label-danger">Warning Alert: The Delete Record will also delete its corresponding Assessments Records</span>',
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
                            type: 'GET',
                            async: true,
                            url: '/subject-classrooms/delete/' + id,
                            success: function(data,textStatus){
                                window.location.replace('/subject-classrooms');
                            },
                            error: function(xhr,textStatus,error){
                                bootbox.alert("Error encountered pls try again later... or contact your Admin Provider", function() {
                                    $(this).hide();
                                });
                            }
                        });
                    }
                }
            }
        });
    });

    //When The Manage Subject offered by students in Class Room Form is submitted
    $(document.body).on('submit', '#manage_student_form', function(){
        var values = $('#manage_student_form').serialize();
        $.ajax({
            type: "POST",
            url: '/subject-classrooms/manage-students',
            data: values,
            success: function (data) {
                window.location.replace('/subject-classrooms');
                $('#manage_subject_form').modal('hide');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2)
            }
        });
        return false;
    });
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '.search_subject_assign_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#assign_2classroom',
                animate: true
            });

            $.post('/subject-classrooms/search-assigned', values, function(data){
                try{
                    var obj = $.parseJSON(data);
                    var assign = '';

                    if(obj.flag === 1){
                        //console.log(obj.ClassSubjects);
                        $.each(obj.SchoolSubjects, function(key, value) {
                            var selected = ($.inArray(value.subject_id, obj.ClassSubjects) > -1) ? 'selected' : '';
                            var sub = ($.trim(value.subject_alias) != "" && value.subject_alias !== null) ? value.subject_alias : value.subject;

                            assign += '<option '+selected+' value="'+value.subject_id+'">' + sub +'</option>';
                        });
                    }
                    var msg = (obj.Type == 1) ? $('#class_classroom_id').children('option:selected').text() : $('#level_classlevel_id').children('option:selected').text();
                    $('#modal-title-text').html('<b>Assign Subjects To ' + msg
                        +' for '+$('#class_academic_term_id').children('option:selected').text()+ ' Academic Term</b>');

                    $('#assign_classroom_id').val(obj.ClassID);
                    $('#assign_classlevel_id').val(obj.LevelID);
                    $('#assign_academic_term_id').val(obj.TermID);
                    $('#subject_multi_select').html(assign);
                    $('#assign_subject_modal').modal('show');

                    window.setTimeout(function() {
                        App.unblockUI('#assign_2classroom');
                    }, 2000);
                    ComponentsDropdowns.init();
                    ComponentsDropdowns.refresh();

                } catch (exception) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#assign_2classroom');
                }
            });
            return false;
        });

        //When the search button is clicked for viewing subjects
        $(document.body).on('submit', '#search_view_subject_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#view_subject',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/subject-classrooms/view-assigned',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Subject</th>\
                                        <th>Class Room</th>\
                                        <th>Tutor</th>\
                                        <th>Academic Term</th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.ClassSubjects, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.subject+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td><button class="btn btn-link edit-tutor" value="'+value.subject_classroom_id+'" rel="'+value.tutor_id+'"><i class="fa fa-edit"></i> '+value.tutor+'</button></td>' +
                                //'<td><a href="javascript:;" class="subject_tutor" data-type="select" data-pk="1" ' +
                                //'data-value="'+value.tutor_id+'" data-souce="/subject-classrooms/tutors" data-original-title="Select Tutor">'+value.tutor+'</a></td>' +
                                '<td>'+value.academic_term+'</td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_subject_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#view_subject_datatable')).refresh();
                    setTableData($('#view_subject_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#view_subject');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#view_subject_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#view_subject');
                }
            });
            return false;
        });

        //When the search button is clicked for managing subjects
        $(document.body).on('submit', '#search_manage_subject_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#manage_subject',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/subject-classrooms/search-subjects',
                data: values,
                success: function (data) {

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Subject</th>\
                                        <th>Class Room</th>\
                                        <th>Tutor</th>\
                                        <th>Academic Term</th>\
                                        <th>Status</th>\
                                        <th>Students</th>\
                                        <th>Action</th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.ClassSubjects, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.subject+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.tutor+'</td>' +
                                '<td>'+value.academic_term+'</td>' +
                                '<td>'+value.status+'</td>' +
                                '<td><button class="btn btn-link manage-student" value="'+value.subject_classroom_id+'"><i class="fa fa-edit"></i> Manage</button></td>' +
                                '<td><button class="btn btn-danger btn-xs delete-subject" value="'+value.subject_classroom_id+'"><i class="fa fa-trash"></i> Delete</button></td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#manage_subject_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#manage_subject_datatable')).refresh();
                    setTableData($('#manage_subject_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#manage_subject');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#manage_subject_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#manage_subject');
                }
            });
            return false;
        });

        //When the manage button is clicked to register a subject to students in a class
        $(document.body).on('click', '.manage-student', function(){
            var subject_classroom_id = $(this).val();
            var academic_term_id = $('#manage_academic_term_id').val();
            var tr = $(this).parent().parent();
            App.blockUI({
                target: '#manage_subject',
                animate: true
            });

            $.ajax({
                type: "GET",
                url: '/subject-classrooms/manage-student/' + subject_classroom_id + '/' + academic_term_id,
                success: function (data) {
                    //console.log(data);
                    var obj = $.parseJSON(data);
                    var assign = '<optgroup label="Select All Students">';
                    if(obj.flag === 1){
                        $.each(obj.Students, function(key, value) {
                            var selected = ($.inArray(value.student_id, obj.Registered) > -1) ? 'selected' : '';
                            assign += '<option ' + selected + ' value="'+value.student_id+'">' + value.name + '</option>';
                        });

                        $('#manage-title-text').html('<b>Managing ' + tr.children(':nth-child(2)').html()
                            + ' Subject Offered by Students in '+tr.children(':nth-child(3)').html()+ ' Class Room</b>');

                        $('#subject_classroom_id').val(subject_classroom_id);
                        $('#manage_student_multi_select').html(assign + '</optgroup>');
                        $('#manage_student_modal').modal('show');

                        ComponentsDropdowns.initSubject();
                        ComponentsDropdowns.refreshSubject();
                    }else {
                        set_msg_box($('#error-box'), '  Error...Kindly Try Again No Record Found', 2);
                    }
                    window.setTimeout(function() {
                        App.unblockUI('#manage_subject');
                    }, 2000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#error-box'), '  Error...Kindly Try Again', 2);
                    App.unblockUI('#manage_subject');
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
    };
    var handleMultiSelectRefresh = function () {
        $('#subject_multi_select').multiSelect('refresh');
    };

    var handleMultiSelectStudent = function () {
        $('#manage_student_multi_select').multiSelect({
            selectableOptgroup: true,
            selectableHeader: '<span class="label label-info"><strong>Students Not Offering The Subject</strong></span>',
            selectableFooter: '<span class="label label-info"><strong>Students Not Offering The Subject</strong></span>',
            selectionHeader: '<span class="label label-success"><strong>Students Offering The Subject</strong></span>',
            selectionFooter: '<span class="label label-success"><strong>Students Offering The Subject</strong></span>',
            cssClass: 'multi-select-subjects'
        });
    };
    var handleMultiSelectRefreshStudent = function () {
        $('#manage_student_multi_select').multiSelect('refresh');
    };

    return {
        //main function to initiate the module
        init: function () {
            handleMultiSelect();
        },
        refresh: function() {
            handleMultiSelectRefresh();
        },
        initSubject: function () {
            handleMultiSelectStudent();
        },
        refreshSubject: function() {
            handleMultiSelectRefreshStudent();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});
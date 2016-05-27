/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {

    // Ajax Get Academic Terms Based on the Academic Year
    //getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#manage_academic_year_id'), $('#manage_academic_term_id'), '/list-box/academic-term/');

    // Ajax Get Class Rooms Based on the Class Level
    //getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //When The Manage Subject offered by students in Class Room Form is submitted
    $(document.body).on('submit', '#manage_student_form', function(){
        var values = $('#manage_student_form').serialize();
        $.ajax({
            type: "POST",
            url: '/subject-tutors/manage-students',
            data: values,
            success: function (data) {
                window.location.replace('/subject-tutors');
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

        //When the search button is clicked for managing subjects
        $(document.body).on('submit', '#search_manage_subject_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#manage_subject',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/subject-tutors/search-subjects',
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
                                        <th>Status</th>\
                                        <th>Students</th>\
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
                url: '/subject-tutors/manage-student/' + subject_classroom_id + '/' + academic_term_id,
                success: function (data) {
                    console.log(data);
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
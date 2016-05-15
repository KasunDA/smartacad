/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#class_academic_year_id'), $('#class_academic_term_id'), '/list-box/academic-term/');
    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#class_classlevel_id'), $('#class_classroom_id'), '/list-box/classroom/');

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#level_academic_year_id'), $('#level_academic_term_id'), '/list-box/academic-term/');

    getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    // Ajax Get Class Rooms Based on the Class Level
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
                    var assign = ''; var assign_count = 0;

                    if(obj.flag === 1){
                        //console.log(obj.ClassSubjects);
                        $.each(obj.SchoolSubjects, function(key, value) {
                            var selected = ($.inArray(value.subject_id, obj.ClassSubjects) > -1) ? 'selected' : '';
                            var sub = (value.subject_alias != "") ? value.subject_alias : value.subject;

                            assign += '<option '+selected+' value="'+value.subject_id+'">' + sub +'</option>';
                            //assign_count++;
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
                                '<td>'+value.academic_term+'</td>' +
                            '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#view_subject_datatable').html(assign);
                    setTableData($('#view_subject_datatable')).refresh();
                    setTableData($('#view_subject_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#view_subject');
                    }, 2000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#view_subject');
                }
            });
            return false;
        });
    }

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

    return {
        //main function to initiate the module
        init: function () {
            handleMultiSelect();
        },
        refresh: function() {
            handleMultiSelectRefresh();
        }
    };
}();

jQuery(document).ready(function() {
    UIBlockUI.init();

});





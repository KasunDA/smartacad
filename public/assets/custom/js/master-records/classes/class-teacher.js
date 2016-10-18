/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    var tutors = $('#tutors').clone();
    var old_btn;

    //When the edit button is clicked show Tutors Drop Down
    $(document.body).on('click', '.edit-class-master', function(){
        var buttonTD = $(this).parent();
        tutors.removeClass('hide');
        var employees = tutors.clone();
        old_btn = $(this).clone();
        buttonTD.html(employees);
        employees.val($(this).attr('rel'));
        employees.prop('id', '');
        employees.attr('rel', $(this).val());
        employees.attr('title', $(this).attr('title'));
        employees.addClass('class-master-select');
        buttonTD.children('select').focus();
    });

    //When No Changes is made to the Teachers Listbox //On Blur
    $(document.body).on('blur', '.class-master-select', function(){
        var td = $(this).parent();
        td.html(old_btn);
    });

    //On Change of the employees name assign to the class
    $(document.body).on('change', '.class-master-select', function(){
        var class_master_id = $(this).attr('rel');
        var classroom_id = $(this).attr('title');
        var year_id = $('#hidden_master_year_id').val();
        var buttonTD = $(this).parent();
        var user_id = $(this).val();
        var name = $(this).children('option:selected').text();

        $.ajax({
            type: "POST",
            data: {class_master_id:class_master_id, classroom_id:classroom_id, year_id:year_id, user_id:user_id},
            url: '/class-rooms/assign-class-teachers',
            success: function (data) {
                buttonTD.html('<button value="'+data+'" title="'+classroom_id+'" rel="'+user_id+'" class="btn btn-link edit-class-master">\n\
                <i class="fa fa-edit"></i> '+name+'</button></td>');
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                set_msg_box($('#msg_box2'), 'Error...Kindly Try Again', 2)
            }
        });
    });
});


var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked for Assigning Class Teacher
        $(document.body).on('submit', '#search_class_teacher_form', function(){
            var values = $(this).serialize();
            $('#hidden_master_year_id').val($('#academic_year_id').val());

            App.blockUI({
                target: '#assign_classMaster',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/class-rooms/class-teachers',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr>\
                                        <th>#</th>\
                                        <th>Class Room</th>\
                                        <th>No. of Student</th>\
                                        <th>Class Teacher</th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    if(obj.flag === 1){
                        $.each(obj.ClassRooms, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.students+'</td>' +
                                '<td><button class="btn btn-link edit-class-master" value="'+value.class_master_id+'" rel="'+value.user_id+'" title="'+value.classroom_id+'"><i class="fa fa-edit"></i> '+value.name+'</button></td>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#class_master_datatable').html(assign);

                    window.setTimeout(function() {
                        App.unblockUI('#assign_classMaster');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#class_master_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box2'), 'Error...Kindly Try Again', 2);
                    App.unblockUI('#assign_classMaster');
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
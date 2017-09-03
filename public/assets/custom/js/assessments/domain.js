/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {
    
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
});

var UIBlockUI = function() {

    var handleSample1 = function() {

        //When the search button is clicked
        $(document.body).on('submit', '#search_classroom', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#domains',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/domains/classroom-assigned',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                <tr>\
                                    <th>#</th>\
                                    <th>Academic Term</th>\
                                    <th>Class Room</th>\
                                    <th>Class Teacher</th>\
                                    <th>Assessment</th>\
                                    <th>Remark</th>\
                                </tr>\
                            </thead>\
                            <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Classrooms, function(key, value) {
                            assign += '<tr>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.academic_term+'</td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td>'+value.class_master+'</td>' +
                                '<td><a href="/domains/view-students/'+value.hashed_class_id+'/'+value.hashed_term_id+'" class="btn btn-info btn-xs"><i class="fa fa-toggle-on"></i> Assess</a></td>' +
                                '<td><a href="/domains/remark/'+value.hashed_class_id+'/'+value.hashed_term_id+'" class="btn btn-warning btn-xs"><i class="fa fa-comments"></i> Remark</a></td>' +
                                '</tr>';
                        });
                    }else if(obj.flag == 0){
                        App.alert({
                            icon: 'warning',
                            type: 'danger',
                            message: 'No Class Room Has Been Assigned To You as a Class Master '+obj.term+' for Academic Year',
                            container: '#domains',
                            place: 'prepend'
                        });
                    }
                    assign += '</tbody>';

                    $('#classroom_assigned_datatable').html(assign);
                    //FormEditable.init();
                    setTableData($('#classroom_assigned_datatable')).refresh();
                    setTableData($('#classroom_assigned_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#domains');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#classroom_assigned_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    set_msg_box($('#msg_box'), 'Error...Kindly Try Again', 2)
                    App.unblockUI('#domains');
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

jQuery(document).ready(function() {
    UIBlockUI.init();

});
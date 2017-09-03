/**
 * Created by Emmanuel on 4/17/2016.
 */
jQuery(document).ready(function() {

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');

    //Check All button click
    $(document.body).on('change', '#check-all-box', function () {
        var check_boxes = $('.phone-checkbox');
        //console.log(check_boxes);
        if($(this).is(':checked')){
            check_boxes.prop('checked', true);
            check_boxes.parents('tr').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            check_boxes.prop('checked', false);
            check_boxes.parents('tr').css({background : "#FFFFFF", color: "#434A54"});
        }
    });

    //Each Check box click
    $(document.body).on('change', '.phone-checkbox', function () {

        if($(this).is(':checked')){
            $(this).parents('tr').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            $(this).parents('tr').css({background : "#FFFFFF", color: "#434A54"});
        }

    });

    //Send All Staffs of Sponsors Link
    $(document.body).on('click', '#all_sposnors, #all_staffs', function () {
        var val = $(this).attr('href');
        $('#message_content_all').val('');
        $('#modal_title_all').html('<i class="fa fa-envelope"></i> Sending Message Form: To all active '+val.toUpperCase()+'S');
        $('#message_type').val(val);
        $('#message_all_modal').modal('show');
    });

});

var UIBlockUI = function() {

    var handleStudentSponsors = function() {

        //When the search button is clicked for viewing Student Sponsors
        $(document.body).on('submit', '#search_student_form', function(){
            var values = $(this).serialize();

            App.blockUI({
                target: '#sponsors',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/messages/list-students',
                data: values,
                success: function (data) {
                    //console.log(data);

                    var obj = $.parseJSON(data);
                    var assign = '<thead>\
                                    <tr role="row" class="heading">\
                                        <th width="2%"><input type="checkbox" class="group-checkable" id="check-all-box"> </th>\
                                        <th width="2%">#</th>\
                                        <th width="10%">Student No.</th>\
                                        <th width="25%">Student Name</th>\
                                        <th width="10%">Gender</th>\
                                        <th width="25%">Sponsor</th>\
                                        <th width="20%">Class Room</th>\
                                        <th width="6%">Message</th>\
                                    </tr>\
                                </thead>\
                                <tbody>';
                    if(obj.flag == 1){
                        $.each(obj.Students, function(key, value) {
                            assign += '<tr>' +
                                '<td><input type="checkbox" class="phone-checkbox" name="phone_no[]" value="'+value.phone_no+'"></td>' +
                                '<td>'+(key + 1)+'</td>' +
                                '<td>'+value.student_no+'</td>' +
                                '<td><a target="_blank" href="/students/view/'+value.student_id+'" class="btn btn-link"><i class="fa fa-user"></i> '+value.name+'</a></td>' +
                                '<td>'+value.gender+'</td>' +
                                '<td><a target="_blank" href="/sponsors/view/'+value.sponsor_id+'" class="btn btn-link"><i class="fa fa-user"></i> '+value.sponsor+'</a></td>' +
                                '<td>'+value.classroom+'</td>' +
                                '<td><button value="'+value.phone_no+'" class="btn btn-warning btn-rounded btn-condensed btn-xs send-message"><span class="fa fa-envelope"></span> Send</button>' +
                                '</tr>';
                        });
                    }
                    assign += '</tbody>';

                    $('#search_student_datatable').html(assign);
                    $('#search_student_button').removeClass('hide');
                    //FormEditable.init();
                    setTableData($('#search_student_datatable')).refresh();
                    setTableData($('#search_student_datatable')).init();

                    window.setTimeout(function() {
                        App.unblockUI('#sponsors');
                    }, 2000);
                    //Scroll To Div
                    scroll2Div($('#search_student_datatable'));
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    setAlert('sponsors', 'Error...Kindly Try Again', 'danger');
                    App.unblockUI('#sponsors');
                }
            });
            return false;
        });
    };

    //message All / Selected Sponsors
    var handleSponsors = function() {

        //When the search button is clicked for messaging Sponsors
        $(document.body).on('submit', '#message_sponsor_form', function(){
            var values = $(this).serialize();
            $('#message_content').val('');

            App.blockUI({
                target: '#sponsors',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/messages/message-selected',
                data: values,
                success: function (data) {
                    // console.log(data);
                    var obj = $.parseJSON(data);

                    if(obj.flag == 1){
                        $('#modal_title').html('<i class="fa fa-envelope"></i> Sending Message Form: '+obj.count+' Sponsors were selected');
                        $('#phone_nos').val(obj.phone_no);
                        $('#message_selected_modal').modal('show');

                    }else {
                        setAlert('sponsors', 'No Sponsor or Sponsor Was Selected...Kindly check at least one with the checkboxes.', 'warning');
                    }

                    window.setTimeout(function() {
                        App.unblockUI('#sponsors');
                    }, 2000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    setAlert('sponsors', 'Error...Kindly Try Again', 'danger');
                    App.unblockUI('#sponsors');
                }
            });
            return false;
        });
    };

    //Message All / Selected Staffs
    var handleStaffs = function() {

        //When the search button is clicked for viewing Student Sponsors
        $(document.body).on('submit', '#message_staffs_form', function(){
            var values = $(this).serialize();
            $('#message_content').val('');

            App.blockUI({
                target: '#staffs',
                animate: true
            });

            $.ajax({
                type: "POST",
                url: '/messages/message-selected',
                data: values,
                success: function (data) {
                    // console.log(data);
                    var obj = $.parseJSON(data);

                    if(obj.flag == 1){
                        $('#modal_title').html('<i class="fa fa-envelope"></i> Sending Message Form: '+obj.count+' Staffs were selected');
                        $('#phone_nos').val(obj.phone_no);
                        $('#message_selected_modal').modal('show');

                    }else {
                        setAlert('staffs', 'No Staff or Staffs Was Selected...Kindly check at least one with the checkboxes.', 'warning');
                    }

                    window.setTimeout(function() {
                        App.unblockUI('#staffs');
                    }, 2000);
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    setAlert('staffs', 'Error...Kindly Try Again', 'danger');
                    App.unblockUI('#staffs');
                }
            });
            return false;
        });
    };

    //Message a Staff / Sponsor
    var handleStaffSponsor = function() {

        //When the message button is clicked for sending message to a staff
        $(document.body).on('click', '.send-message', function(){
            var value = $(this).val();
            $('#message_content').val('');

            if(value != ''){
                $('#modal_title').html('<i class="fa fa-envelope"></i> Sending Message Form: Individual Staff / Sponsor');
                $('#phone_nos').val(value);
                $('#message_selected_modal').modal('show');
            }
            return false;
        });
    };

    return {
        //main function to initiate the module
        init: function() {

            handleStudentSponsors();
            handleSponsors();
            handleStaffs();
            handleStaffSponsor();
        }
    };
}();

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#staff_tabledata"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Network issue...kindly retry or contact your system administrator',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            },
            onDataLoad: function(grid) {
                // execute some code on ajax data load
            },
            loadingMessage: 'Loading...',
            responsive: true,
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options

                // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
                // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/scripts/datatable.js).
                // So when dropdowns used the scrollable div should be removed.
                //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",

                "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.

                "lengthMenu": [
                    [10, 20, 50, 100, 150, -1],
                    [10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "/messages/all-staffs" // ajax source
                },
                "ordering": false,
                "order": [
                    [1, "asc"]
                ]// set first column as a default sort by asc
            }
        });

        // handle general search or name or sponsor
        grid.getTableWrapper().on('input', '#search_param', function (e) {
            e.preventDefault();
            var search = $(this).val();
            if (search != "") {
                grid.setAjaxParam("sSearch", search);
                grid.getDataTable().ajax.reload();
                App.alert({
                    icon: 'success',
                    message: 'Find your search result below',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (search == "") {
                // grid.clearAjaxParams();
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Enter A Search Parameter either name, email or phone no',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        grid.getDataTable().ajax.reload();
        grid.clearAjaxParams();
    };

    return {

        //main function to initiate the module
        init: function () {

            handleRecords();
        }

    };

}();
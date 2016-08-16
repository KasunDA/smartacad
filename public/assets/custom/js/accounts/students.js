/**
 * Created by Emmanuel on 4/17/2016.
 */
jQuery(document).ready(function() {

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');
    //State And L.G.A
    getDependentListBox($('#state_id'), $('#lga_id'), '/list-box/lga/');

    //Delete a students record
    $(document.body).on('click', '.delete_student',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var student = parent.children(':nth-child(2)').html();
        var student_id = $(this).val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete Student: <strong>"+student+ "</strong> and all its equivalent assessments records?",
            title: "Warning Alert",
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
                            url: '/students/delete/' + student_id,
                            success: function(data,textStatus){
                                window.location.replace('/students');
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
    });
});

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#student_tabledata"),
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
                    "url": "/students/all-students" // ajax source
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
                    message: 'Enter A Search Parameter either name, gender, status or sponsor',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        // handle gender, class room or status search
        grid.getTableWrapper().on('change', '.search-params', function (e) {
            e.preventDefault();
            var search = $(this).val();
            var type = $(this).attr('id');
            if (search != "") {
                grid.setAjaxParam("search["+type+"]", search);
                grid.getDataTable().ajax.reload();
                App.alert({
                    icon: 'success',
                    message: 'Find your search result below',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (search == "") {
                $('#gender').val(''); $('#status_id').val(''); $('#classroom_id').val('');
                grid.clearAjaxParams();
                grid.getDataTable().ajax.reload();
                App.alert({
                    type: 'info',
                    icon: 'warning',
                    message: 'Enter A Search Parameter either name, sponsor, class room, gender or status',
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
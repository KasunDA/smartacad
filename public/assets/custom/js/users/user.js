/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {

    $(document.body).on('click', '.user_status',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var user_name = parent.children(':nth-child(2)').text();
        var user_id = $(this).val();
        var value = $(this).attr('rel');
        var msg = (value == '1') ? '<strong>ACTIVATE '+user_name+' </strong>' : '<strong>DEACTIVATE '+user_name+'</strong>';
        //alert(user_name + ' = ' + value);

        bootbox.dialog({
            message: "Are You sure You want to "+msg+" from accessing this application?",
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
                            url: '/users/status/' + user_id + '/' + value,
                            success: function(data,textStatus){
                                window.location.replace('/users');
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

    $(document.body).on('click', '.delete_user',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var user = parent.children(':nth-child(2)').html();
        var user_id = $(this).val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete User: "+user+ " and all its reference places",
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
                            url: '/users/delete/' + user_id,
                            success: function(data,textStatus){
                                window.location.replace('/users');
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
            src: $("#users_datatable"),
            onSuccess: function (grid, response) {
                // grid:        grid object
                // response:    json object of server side ajax response
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error
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
                    "url": "/users/all-users" // ajax source
                },
                "ordering": false,
                "order": [
                    [1, "asc"]
                ]// set first column as a default sort by asc
            }
        });

//                    $('#users_datatable_wrapper .dataTables_filter input').addClass("form-control input-medium input-inline"); // modify table search input

        // handle group actionsubmit button click
        grid.getTableWrapper().on('input', '#search_param', function (e) {
            e.preventDefault();
            var search = $(this).val();
            if (search != "") {
                grid.setAjaxParam("sSearch", search);
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
                App.alert({
                    icon: 'success',
                    message: 'Find your search result below',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (search == "") {
                App.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'Enter A Search Parameter either name, email or phone number',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });

        grid.setAjaxParam("sSearch", $('#search_param').val());
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
/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_role').click(function (e) {
        e.preventDefault();
        var clone_row = $('#role_table tbody tr:last-child').clone();
        var new_user_types = $('#new_user_types').clone();

        $('#role_table tbody').append(clone_row);


        clone_row.children(':nth-child(1)').html(parseInt(clone_row.children(':nth-child(1)').html()) + 1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(4)').children('input').val('');
        clone_row.children(':nth-child(5)').html(new_user_types.html());
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_role"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click', '.remove_role', function () {
        $(this).parent().parent().remove();
    });
});

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#user_role_table"),
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
                // $('.selectpicker').selectpicker('refresh');
                $('.selectpicker').selectpicker('refresh');
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
                    [5, 10, 20, 50, 100, 150, -1],
                    [5, 10, 20, 50, 100, 150, "All"] // change per page values here
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    "url": "/roles/all-users" // ajax source
                },
                "ordering": false,
                "order": [
                    [1, "asc"]
                ]// set first column as a default sort by asc
            }
        });
        // $('#multi-append1').selectpicker('refresh');

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
                    message: 'Enter A Search Parameter either name or email',
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
/**
 * Created by Emmanuel on 4/17/2016.
 */

$(function(){
    // Ajax Get Local Governments Based on the state
    getDependentListBox($('#state_id'), $('#lga_id'), '/list-box/lga/');
});

var TableDatatablesAjax = function () {

    var handleRecords = function () {

        var grid = new Datatable();

        grid.init({
            src: $("#sponsor_tabledata"),
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
                    "url": "/sponsors/all-sponsors" // ajax source
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
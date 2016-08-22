$(function () {

    $('#flash_message').delay(10000).slideUp(850);
});

//time function
function showTime() {
    var today = new Date();
    var hours = today.getHours();
    var min = today.getMinutes();
    var sec = today.getSeconds();
    var time = "";

    //time definition
    if (hours == 0) {
        time = "12";
    }

    if (hours < 10) {
        time += "0" + hours;
    } else if (hours <= 12) {
        time += hours;
    }

    if (hours > 12) {
        time += hours - 12;
    }

    if (min < 10) {
        time += ":0" + min;
    } else {
        time += ":" + min;
    }

    if (sec < 10) {
        time += ":0" + sec;
    } else {
        time += ":" + sec;
    }

    if (hours >= 12) {
        time += " PM";
    } else {
        time += " AM";
    }
    $("#timer").html(time);
    setTimeout("showTime()", 1000)
}

//Activing Tab And Links With the class set to active
function setTabActive(link) {
    var menu = $('ul.page-sidebar-menu li.active').removeClass('active');
    var parents = $(link).parents('li');
    parents.each(function(){
        $(this).addClass('active');
    });
}

// set a loading image
function small_loading_image(div) {
    div.html('<img src="/images/loading.gif" style="width:20px; height:20px" alt="Loading"/>');
}

//Scrolling To a div
function scroll2Div(div){
    $('html, body').animate({
        scrollTop: div.offset().top
    }, 2000);
}

//Dependent List Box
function getDependentListBox(parent, child, url) {
    parent.bind("change", function (event) {
        if (parent.val() == '') {
            child.val('');
        } else {
            parent.parent().next().html('<img src="/assets/custom/img/admin/loading.gif" alt="Loading Image"/>');
            $.ajax({
                type: "get",
                async: true,
                //data:parent.serialize(),
                url: url + parent.val(),
                dataType: "html",
                success: function (data, textStatus) {
                    child.html(data);
                    parent.parent().next().html('');
                }
            });
        }
        return false;
    });
}

//Image file type and file size vaildation
function validateImageFile(id) {
    id.bind('change', function () {
        var size = this.files[0].size;
        var value = $(this).val().toLowerCase();
        var extension = value.substring(value.lastIndexOf('.'));
        if ($.inArray(extension, ['.gif', '.png', '.jpg', '.jpeg']) === -1) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            Invalid File Type. Require Only Image Files With Extensions Of .gif, .png, .jpg, .jpeg</div>')
        } else if (size >= 1048576) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            File Size To Large. Requires Only Files Less Than ' + (1048576 / 1024) + ' KB</div>')
        } else {
            $('#image_error').html('');
        }
    });
}

//Image file type and file size vaildation
function validateDocumentFile(id) {
    id.bind('change', function () {
        var size = this.files[0].size;
        var value = $(this).val().toLowerCase();
        var extension = value.substring(value.lastIndexOf('.'));
        if ($.inArray(extension, ['.pdf']) === -1) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            Invalid File Type. Require Only PDF Files With Extension Of .pdf</div>')
        } else if (size >= 5120000) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            File Size To Large. Requires Only Files Less Than ' + (5120000 / 1024) + ' MB</div>')
        } else {
            $('#image_error').html('');
        }
    });
}

// Auto Complete Function
function autoCompleteField(name, id, source){
    name.autocomplete({
        source: source,
        minLength: 1
    });
    name.autocomplete({
        select: function(event, ui) {
            selected_id = ui.item.id;
            id.val(selected_id);
            //alert(selected_id);
        }
    });
    name.autocomplete({
        open: function(event, ui) {
            id.val(-1);
        }
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#img_prev')
                .attr('src', e.target.result)
                .width(150)
                .height(150);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

//Set Warning 3, Error 2, Info 0 or success 1 messages
function set_msg_box(div, text, type) {
    if(type === 1)
        div.html('<div class="alert alert-success"> <i class="fa fa-thumbs-up fa-1x"></i> ' + text + ' </div>');
    else if(type === 2)
        div.html('<div class="alert alert-danger"> <i class="fa fa-thumbs-down fa-1x"></i> ' + text + ' </div>');
    else if(type === 3)
        div.html('<div class="alert alert-warning"> <i class="fa fa-warning fa-1x"></i> ' + text + ' </div>');
    else
        div.html('<div class="alert alert-info"> <i class="fa fa-info fa-1x"></i> ' + text + ' </div>');
}

function setAlert(div, text, type) {
    App.alert({
        type: type,
        message: 'Error...Kindly Try Again',
        container: '#sponsors',
        place: 'prepend'
    });
    if(type === 1)
        div.html('<div class="alert alert-success"> <i class="fa fa-thumbs-up fa-1x"></i> ' + text + ' </div>');
    else if(type === 2)
        div.html('<div class="alert alert-danger"> <i class="fa fa-thumbs-down fa-1x"></i> ' + text + ' </div>');
    else if(type === 3)
        div.html('<div class="alert alert-warning"> <i class="fa fa-warning fa-1x"></i> ' + text + ' </div>');
    else
        div.html('<div class="alert alert-info"> <i class="fa fa-info fa-1x"></i> ' + text + ' </div>');
}

//Custom TableData
function setTableData(table) {
    var initTable1 = function () {
        var oTable = table.dataTable({

            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            "language": {
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                },
                "emptyTable": "No data available in table",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered1 from _MAX_ total entries)",
                "lengthMenu": "_MENU_ entries",
                "search": "Search:",
                "zeroRecords": "No matching records found"
            },

            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},


            buttons: [
                //{ extend: 'print', className: 'btn dark btn-outline' },
                //{ extend: 'copy', className: 'btn red btn-outline' },
                //{ extend: 'pdf', className: 'btn green btn-outline' },
                //{ extend: 'excel', className: 'btn yellow btn-outline ' },
                //{ extend: 'csv', className: 'btn purple btn-outline ' }
                //{ extend: 'colvis', className: 'btn dark btn-outline', text: 'Columns'}
            ],

            // setup responsive extension: http://datatables.net/extensions/responsive/
            responsive: true,

            //"ordering": false, disable column ordering
            //"paging": false, disable pagination

            "order": [
                [0, 'asc']
            ],

            "lengthMenu": [
                [5, 10, 15, 20, -1],
                [5, 10, 15, 20, "All"] // change per page values here
            ],
            // set the initial value
            "pageLength": 10,

            "dom": "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable

            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js).
            // So when dropdowns used the scrollable div should be removed.
            //"dom": "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
        });
    }

    var refreshTable = function () {
        var oTable = table.dataTable().fnDestroy();
    }

    return {

        //main function to initiate the module
        init: function () {

            if (!jQuery().dataTable) {
                return;
            }

            initTable1();
        },
        refresh: function () {

            if (!jQuery().dataTable) {
                return;
            }

            refreshTable();
        }

    };

};
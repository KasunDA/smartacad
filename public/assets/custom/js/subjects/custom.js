/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_custom').click(function(e){
        e.preventDefault();
        var clone_row = $('#custom_table tbody tr:last-child').clone();
        $('#custom_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(4)').children('select').val('');
        // clone_row.children(':nth-child(3)').html(new_role.html());

        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_custom"><span class="fa fa-times"></span> Remove</button>');
    });

    $('.add_subject').click(function(e){
        e.preventDefault();
        var clone_row = $('#subject_table tbody tr:last-child').clone();
        $('#subject_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('select').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('select').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_custom"><span class="fa fa-times"></span> Remove</button>');
    });
    
    $(document.body).on('click','.remove_custom',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_custom',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var custom = parent.children(':nth-child(2)').children('input').val();
        var custom_id = $(this).val();

        bootbox.dialog({
            message: 'Are You sure You want to permanently delete custom:<span class="bold"> '+custom+'</span>',
            title: '<span class="bold font-red">Warning Alert</span>',
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
                            url: '/custom-subjects/delete/' + custom_id,
                            success: function(data,textStatus){
                                window.location.reload();
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

var UITree = function () {

    var handleCategory = function () {

        $('#customs_tree').jstree({
            "core" : {
                "themes" : {
                    "responsive": false
                }
            },
            "types" : {
                "default" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "file" : {
                    "icon" : "fa fa-file icon-state-primary icon-lg"
                },
                "add" : {
                    "icon" : "fa fa-plus icon-state-success icon-lg"
                },
                "one" : {
                    "icon" : "fa fa-folder icon-state-warning icon-lg"
                },
                "two" : {
                    "icon" : "fa fa-folder icon-state-info icon-lg"
                },
                "three" : {
                    "icon" : "fa fa-folder icon-state-success icon-lg"
                },
                "four" : {
                    "icon" : "fa fa-folder icon-state-danger icon-lg"
                },
                "five" : {
                    "icon" : "fa fa-file icon-state-warning icon-lg"
                }
            },
            "plugins": ["types"]
        });

        // handle link clicks in tree nodes(support target="_blank" as well)
        $('#customs_tree').on('select_node.jstree', function(e,data) {
            var link = $('#' + data.selected).find('a');
            if (link.attr("href") != "#" && link.attr("href") != "javascript:;" && link.attr("href") != "") {
                if (link.attr("target") == "_blank") {
                    link.attr("href").target = "_blank";
                }
                document.location.href = link.attr("href");
                return false;
            }
        });
    };
    return {
        //main function to initiate the module
        init: function () {

            handleCategory();
        }
    };
}();






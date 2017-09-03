/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_menu').click(function(e){
        e.preventDefault();
        var clone_row = $('#menu_table tbody tr:last-child').clone();
        var new_role = $('#new_roles').clone();

        $('#menu_table tbody').append(clone_row);
        var count = $('#menu_table tbody tr').length;

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(4)').children('div.input-group').children('input').val('');
        clone_row.children(':nth-child(4)').children('div.input-group').children('span').html('');
        clone_row.children(':nth-child(5)').children('select').val('');

        new_role.children('select').attr('name', 'role_id['+count+'][]');
        clone_row.children(':nth-child(6)').html(new_role.html());
        
        clone_row.children(':nth-child(7)').children('input').val('');
        clone_row.children(':nth-child(8)').children('input').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_menu"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_menu',function(){
        $(this).parent().parent().remove();
    });

});

var UITree = function () {

    var handleCategory = function () {

        $('#menus_tree').jstree({
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
        $('#menus_tree').on('select_node.jstree', function(e,data) {
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






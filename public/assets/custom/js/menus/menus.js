/**
 * Created by Cecilee2 on 8/4/2015.
 */

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






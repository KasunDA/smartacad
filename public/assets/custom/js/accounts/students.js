/**
 * Created by Emmanuel on 4/17/2016.
 */
jQuery(document).ready(function() {

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');
});

var ComponentsTypeahead = function () {

    var handleSponsorTypeahead = function() {
        // Example #2
        var sponsors = new Bloodhound({
            datumTokenizer: function(d) { return Bloodhound.tokenizers.whitespace(d.name); },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            limit: 10,
            prefetch: {
                type: 'GET',
                url: '/students/sponsors',
                dataType: 'text/json',
                filter: function(list) {
                    //console.log(list);
                    return list;
                    //return $.map(list, function(country) { return { name: country }; });
                }
            }
        });

        sponsors.initialize();

        if (App.isRTL()) {
            $('#sponsor_name').attr("dir", "rtl");
        }
        $('#sponsor_name').typeahead(null, {
            name: 'sponsor_name',
            highlight: true,
            displayKey: 'name',
            hint: (App.isRTL() ? false : true),
            source: sponsors.ttAdapter(),
            templates:{
                empty:[
                    '<div class="label label-danger">No Record Match Found</div>'
                ]
            }
        }).on('typeahead:selected', function(obj, datum){
            console.log(obj, datum);
            $('#sponsor_id').val(datum.id);
        }).on('change', function(datum){
            console.log(datum);
            $('#sponsor_id').val(-1);
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleSponsorTypeahead();
        }
    };

}();

jQuery(document).ready(function() {
    ComponentsTypeahead.init();
});
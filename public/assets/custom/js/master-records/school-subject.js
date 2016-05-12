/**
 * Created by Cecilee2 on 8/4/2015.
 */

var ComponentsDropdowns = function () {

    var handleMultiSelect = function () {
        $('#subject_multi_select').multiSelect({
            selectableOptgroup: true,
            selectableHeader: '<span class="label label-info"><strong>List of Available Subjects</strong></span>',
            selectableFooter: '<span class="label label-info"><strong>List of Available Subjects</strong></span>',
            selectionHeader: '<span class="label label-success"><strong>List of Selected Subjects Offered</strong></span>',
            selectionFooter: '<span class="label label-success"><strong>List of Selected Subjects Offered</strong></span>',
            cssClass: 'multi-select-subjects'
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleMultiSelect();
        }
    };
}();




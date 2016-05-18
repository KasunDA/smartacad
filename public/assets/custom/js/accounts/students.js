/**
 * Created by Emmanuel on 4/17/2016.
 */
jQuery(document).ready(function() {

    // Ajax Get Class Rooms Based on the Class Level
    getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');

    // Auto Complete of Sponsor Name
    autoCompleteField($("#sponsor_name"), $("#sponsor_id"), "/students/sponsors/");
});
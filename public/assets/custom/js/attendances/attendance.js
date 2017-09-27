/**
 * Created by Kheengz on 9/4/2017.
 */

jQuery(document).ready(function() {
    // Ajax Get Academic Terms Based on the Academic Year
    // getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    // getDependentListBox($('#classlevel_id'), $('#classroom_id'), '/list-box/classroom/');
    // getDependentListBox($('#view_academic_year_id'), $('#view_academic_term_id'), '/list-box/academic-term/');
    // getDependentListBox($('#view_classlevel_id'), $('#view_classroom_id'), '/list-box/classroom/');

    //Check All button click
    $(document.body).on('change', '.check-all', function () {
        var check_boxes = $('.check-one');
        // console.log(check_boxes);
        if($(this).is(':checked')){
            check_boxes.prop("checked", true);
            check_boxes.parents('tr').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            check_boxes.prop("checked", false);
            check_boxes.parents('tr').css({background : "#FFFFFF", color: "#434A54"});
        }
    });

    //Each Check box click
    $(document.body).on('change', '.check-one', function () {

        if($(this).is(':checked')){
            $('.check-all').prop("checked", true);
            $(this).parents('tr').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            $('.check-all').prop("checked", false);
            $(this).parents('tr').css({background : "#FFFFFF", color: "#434A54"});
        }
    });
});

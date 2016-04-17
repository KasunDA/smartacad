/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {

    $(document.body).on('change', '#checkAll', function () {
        var check_boxes = $('.permissions_check_box');
        //console.log(check_boxes);
        if($(this).is(':checked')){
            $(this).parent().parent().next().html('UN-CHECK ALL');
            $(this).parent().parent().next().prop('class', 'label label-danger');

            check_boxes.prop('checked', true);
            check_boxes.parent('span').prop('class', 'checked');
            check_boxes.parent().parent().next().html('Remove');
            check_boxes.parent().parent().next().prop('class', 'badge badge-danger');
            check_boxes.parents('li').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            $(this).parent().parent().next().html('CHECK ALL');
            $(this).parent().parent().next().prop('class', 'label label-success');

            check_boxes.prop('checked', false);
            check_boxes.parent('span').removeProp('class');
            check_boxes.parent().parent().next().html('Add');
            check_boxes.parent().parent().next().prop('class', 'badge badge-success');
            check_boxes.parents('li').css({background : "#FFFFFF", color: "#434A54"});
        }
    });


    $(document.body).on('change', '.permissions_check_box', function () {

        if($(this).is(':checked')){
            $(this).parent().parent().next().html('Remove');
            $(this).parent().parent().next().prop('class', 'badge badge-danger');
            $(this).parents('li').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            $('#checkAll').parent('span').removeProp('class');
            $('#checkAll').parent().parent().next().html('CHECK ALL');
            $('#checkAll').parent().parent().next().prop('class', 'label label-success');
            $(this).parent().parent().next().html('Add');
            $(this).parent().parent().next().prop('class', 'badge badge-success');
            $(this).parents('li').css({background : "#FFFFFF", color: "#434A54"});
        }

    });

    //$( "input.color_border" ).addClass( "hide" );
    $( "input.color_border" ).each(function() {
        $( this ).parents('li').css({background : "#F5F5F5", color: "#29343F"});
    });
});





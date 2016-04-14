/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {

    $(document.body).on('click', '.permissions_all', function () {
        var check_boxes = $('.permissions_check_box');
        if($(this).is(':checked')){
            check_boxes.prop('checked', true);
            console.info('box', check_boxes);
            $(this).parent().children('span').html('Remove All');
            check_boxes.parent().children('span').html('Remove');
            check_boxes.parent().children('span').prop('class', 'label label-danger');
            check_boxes.parents('li').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            check_boxes.prop('checked', false);
            $(this).parent().children('span').html('Add All');
            check_boxes.parent().children('span').html('Add');
            check_boxes.parent().children('span').prop('class', 'label label-success');
            check_boxes.parents('li').css({background : "#FFFFFF", color: "#434A54"});
        }
    });


    $(document.body).on('click', '.permissions_check_box', function () {

        if($(this).is(':checked')){
            $(this).parent().children('span').html('Remove');
            $(this).parent().children('span').prop('class', 'label label-danger');
            $( this ).parents('li').css({background : "#F5F5F5", color: "#29343F"});
        }else{
            $('.permissions_all').prop('checked', false);
            $('.permissions_all').parent().children('span').html('Add All');
            $(this).parent().children('span').html('Add');
            $(this).parent().children('span').prop('class', 'label label-success');
            $( this ).parents('li').css({background : "#FFFFFF", color: "#434A54"});
        }

    });

    //$( "input.color_border" ).addClass( "hide" );
    $( "input.color_border" ).each(function() {
        $( this ).parents('li').css({background : "#F5F5F5", color: "#29343F"});
    });
});





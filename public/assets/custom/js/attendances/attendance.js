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
        var check_boxes = $( '.check-one' );
        var reasons = $( '.reasons' );
        // console.log(check_boxes);
        if($(this).is(':checked')){
            check_boxes.prop("checked", true);
            reasons.prop("disabled", true);
            reasons.val('');
            check_boxes.parents('tr').css({background : "#1BA39C", color: "#29343F"});
        }else{
            check_boxes.prop("checked", false);
            reasons.prop("disabled", false);
            check_boxes.parents('tr').css({background : "#e35b5a", color: "#434A54"});
        }
    });

    var inputElement = $( "input" );

    //Each Check box click
    $(document.body).on('change', '.check-one', function () {

        var input = $(this).parents('td').prev($( 'td' )).children( inputElement );
        if($(this).is(':checked')){
            $( input ).val('');
            $( input ).prop('disabled', true);
            $(this).parents('tr').css({background : "#1BA39C", color: "#29343F"});
        }else{
            $('.check-all').prop("checked", false);
            $( input ).prop('disabled', false);
            $(this).parents('tr').css({background : "#e35b5a", color: "#434A54"});
        }
    });

    $(document.body).on('click', '.check-td', function () {
        var input = $(this).parent().children(':last-child').find( inputElement );
        var td = $( input ).parents('td').prev($( 'td' )).children( inputElement );

        if($(input).prop('checked')){
            $(input).prop('checked', false);
            $( td ).prop('disabled', false);
            $(this).parent().css({background : "#e35b5a", color: "#434A54"});
        }else{
            $(input).prop('checked', true);
            $( td ).val('');
            $( td ).prop('disabled', true);
            $(this).parent().css({background : "#1BA39C", color: "#29343F"});
        }
    });
});

/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    
    $('.add_assessment_setup').click(function(e){
        e.preventDefault();
        var clone_row = $('#assessment_setup_table tbody tr:last-child').clone();

        $('#assessment_setup_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('select').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('select').val('');
        clone_row.children(':nth-child(4)').children('select').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-xs remove_assessment_setup"><span class="fa fa-times"></span> Remove</button>');
    });

    //Validate the percentage values make sure its sums up to 100% per class group
    $(document.body).on('submit', '#assessment_detail_form', function(){
        var count = $('#assessment_setup_count').val();
        var output = '';
        for(var i=1; i <= count; i++) {
            var sum = 0;
            $('#classgroup_tbody' + i + ' .percent').each(function (index, elem) {
                sum += parseInt($(elem).val());
            });
            if(sum != 100){
                output += '<li>The Sum of the percentage for ' + $('#classgroup_tbody' + i).children(':nth-child(1)').children(':nth-child(1)').html() + ' do not SUM UP to 100% </li>';
            }
        }
        if(output == ''){
            return true;
        }else {
            $('#error-div').removeClass('hide');
            $('#error-div').html('<ul>' + output + '</ul>');
            return false;
        }
    });

    $(document.body).on('click','.remove_assessment_setup',function(){
        $(this).parent().parent().remove();
    });
});





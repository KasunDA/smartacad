/**
 * Created by Cecilee2 on 8/4/2015.
 */

jQuery(document).ready(function() {

    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');

    //Validate Scores So that it does'nt exceed the weight point assigned
    $(document.body).on('change', '.scores', function(){
        var val = parseInt($(this).val());
        var wp = parseInt($('#weight_point').val());
        if(val > wp || val < 0) {
            $(this).next('span').addClass('badge badge-danger badge-roundless');
            $(this).next('span').html('>= and <= ' + wp);

        } else {
            $(this).next('span').html('');

        }
    });

    //Validate Before submitting the form
    $(document.body).on('submit', '#scores-form', function(e){
        //e.preventDefault();
        var check = 0;
        var wp = parseInt($('#weight_point').val());
        var name = '';
        $('.scores').each(function(index, elem){
            var value = parseInt($(elem).val());
            if(value > wp || value < 0){
                check = check + 1;
                name += '<li>' + $(elem).parent().parent().children(':nth-child(2)').html() + ' Score(' + value + ') is less than 0 or more than ' + wp + '</li>'
            }
        });
        if(check > 0){
            set_msg_box($('#error-div'), '<ul>' + name + '</ul>', 2);
            return false;
        }
        return true;
    });

    //Validate Before submitting the form
    $(document.body).on('submit', '#setup_exam_form', function(e){
        var values = $('#setup_exam_form').serialize();
        $.ajax({
            type: "POST",
            data: values,
            url: '/exams/validate-setup/',
            success: function(data,textStatus){
                if(data.flag === 1){
                    //set_msg_box($('#error-box'), data.output, 2);
                    $('#exam-message').html(data.output);
                }else if(data.flag === 2){
                    $('#exam-message').html(data.output);
                }
                $('#confirm-btn').val(data.term);
                $('#exam_setup_modal').modal('show');
            },
            error: function(xhr,textStatus,error){
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
            }
        });
        return false;
    });

    //Submit the form to process the exam setup
    $(document.body).on('click', '#confirm-btn', function(e){
        $.ajax({
            type: "POST",
            data: {academic_term_id: $(this).val()},
            url: '/exams/setup/',
            success: function(data,textStatus){
                console.log("Data", data);
                window.location.reload();
            },
            error: function(xhr,textStatus,error){
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
            }
        });
        return false;
    });
});
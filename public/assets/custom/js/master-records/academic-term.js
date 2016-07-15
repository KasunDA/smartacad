/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    // Ajax Get Academic Terms Based on the Academic Year
    getDependentListBox($('#academic_year_id'), $('#academic_term_id'), '/list-box/academic-term/');
    getDependentListBox($('#to_academic_year_id'), $('#to_academic_term_id'), '/list-box/academic-term/');

    //Add New Academic Term
    $('.add_academic_term').click(function(e){
        e.preventDefault();
        var clone_row = $('#academic_term_table tbody tr:last-child').clone();

        $('#academic_term_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('select').val('');
        clone_row.children(':nth-child(4)').children('select').val('');
        clone_row.children(':nth-child(5)').children('select').val('');
        clone_row.children(':nth-child(6)').children('input').val('');
        clone_row.children(':nth-child(6)').children('input').datepicker();
        clone_row.children(':nth-child(7)').children('input').val('');
        clone_row.children(':nth-child(7)').children('input').datepicker();
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_academic_term"><span class="fa fa-times"></span> Remove</button>');
    });

    //Remove an empty row
    $(document.body).on('click','.remove_academic_term',function(){
        $(this).parent().parent().remove();
    });

    //Delete an academic term record
    $(document.body).on('click', '.delete_academic_term',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var academic_term = parent.children(':nth-child(2)').children('input').val();
        var academic_term_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete Academic term "+academic_term,
            title: "Warning Alert",
            buttons: {
                danger: {
                    label: "NO",
                    className: "btn-default",
                    callback: function() {
                        $(this).hide();
                    }
                },
                success: {
                    label: "YES",
                    className: "btn-success",
                    callback: function() {
                        $.ajax({
                            type: 'GET',
                            async: true,
                            url: '/academic-terms/delete/' + academic_term_id,
                            success: function(data,textStatus){
                                window.location.replace('/academic-terms');
                            },
                            error: function(xhr,textStatus,error){
                                bootbox.alert("Error encountered pls try again later..", function() {
                                    $(this).hide();
                                });
                            }
                        });
                    }
                }
            }
        });
    });

    //Validate if the academic term has been cloned
    $(document.body).on('submit', '#clone_subjects_assigned', function(e){
        var values = $('#clone_subjects_assigned').serialize();
        $.ajax({
            type: "POST",
            data: values,
            url: '/academic-terms/validate-clone/',
            success: function(data,textStatus){
                if(data.flag === 1){
                    // set_msg_box($('#error-box'), data.output, 1);
                    bootbox.dialog({
                        message: '<h4>Are You Sure You Want To Clone Records From <strong>'+data.from.academic_term+'</strong> to <strong>'+data.to.academic_term+'</strong>? ' +
                                '<span class="text-danger">Note: its not reversible</span></h4>',
                        title: '<span class="text-primary">Clone Record Confirmation</span>',
                        buttons: {
                            danger: {
                                label: "NO",
                                className: "btn-default",
                                callback: function() {
                                    $(this).hide();
                                }
                            },
                            success: {
                                label: "YES",
                                className: "btn-success",
                                callback: function() {
                                    $.ajax({
                                        type: 'POST',
                                        data:{from_academic_term_id:data.from.academic_term_id, to_academic_term_id:data.to.academic_term_id},
                                        url: '/academic-terms/cloning/',
                                        success: function(data,textStatus){
                                            window.location.replace('/academic-terms/clones');
                                        },
                                        error: function(xhr,textStatus,error){
                                            bootbox.alert("Error encountered pls try again later..", function() {
                                                $(this).hide();
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    });
                }else{
                    set_msg_box($('#error-box'), data.output, 2);
                }
                // $('#confirm-btn').val(data.term);
            },
            error: function(xhr,textStatus,error){
                set_msg_box($('#error-box'), 'Error...Kindly Try Again', 2);
            }
        });
        return false;
    });

});





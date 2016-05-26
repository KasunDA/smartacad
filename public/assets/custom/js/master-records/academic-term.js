/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
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

    $(document.body).on('click','.remove_academic_term',function(){
        $(this).parent().parent().remove();
    });

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
});





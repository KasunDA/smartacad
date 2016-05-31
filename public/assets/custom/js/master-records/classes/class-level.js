/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_class_level').click(function(e){
        e.preventDefault();
        var clone_row = $('#class_level_table tbody tr:last-child').clone();

        $('#class_level_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('select').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_class_level"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_class_level',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_class_level',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var class_level = parent.children(':nth-child(2)').children('input').val();
        var class_level_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete Class Level "+class_level,
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
                            url: '/class-levels/delete/' + class_level_id,
                            success: function(data,textStatus){
                                window.location.replace('/class-levels');
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





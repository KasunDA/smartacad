/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_menu_header').click(function(e){
        e.preventDefault();
        var clone_row = $('#menu_header tbody tr:last-child').clone();
        var new_role = $('#new_roles').clone();

        $('#menu_header tbody').append(clone_row);
        var count = $('#menu_header tbody tr').length;

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(4)').children('select').val('');

        new_role.children('select').attr('name', 'role_id['+count+'][]');
        clone_row.children(':nth-child(5)').html(new_role.html());

        clone_row.children(':nth-child(6)').children('input').val('');
        clone_row.children(':nth-child(7)').children('input').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_menu_header"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_menu_header',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_menu_header',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var menu_header = parent.children(':nth-child(2)').children('input').val();
        var menu_header_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete menu header "+menu_header,
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
                            url: '/menu-headers/delete/' + menu_header_id,
                            success: function(data,textStatus){
                                window.location.replace('/menu-headers');
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





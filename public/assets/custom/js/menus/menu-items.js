/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_menu_item').click(function(e){
        e.preventDefault();
        var clone_row = $('#menu_item_table tbody tr:last-child').clone();
        var new_role = $('#new_roles').clone();

        $('#menu_item_table tbody').append(clone_row);
        var count = $('#menu_item_table tbody tr').length;

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(4)').children('div.input-group').children('input').val('');
        clone_row.children(':nth-child(4)').children('div.input-group').children('span').html('');
        clone_row.children(':nth-child(5)').children('select').val('');
        clone_row.children(':nth-child(6)').children('select').val('');

        new_role.children('select').attr('name', 'role_id['+count+'][]');
        clone_row.children(':nth-child(7)').html(new_role.html());
        clone_row.children(':nth-child(8)').children('input').val('');
        clone_row.children(':nth-child(9)').children('input').val('');

        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_menu_item"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_menu_item',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_menu_item',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var menu_item = parent.children(':nth-child(2)').children('input').val();
        var menu_item_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete menu item "+menu_item,
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
                            url: '/menu-items/delete/' + menu_item_id,
                            success: function(data,textStatus){
                                window.location.replace('/menu-items');
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

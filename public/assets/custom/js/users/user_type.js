/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_user_type').click(function(e){
        e.preventDefault();
        var clone_row = $('#menu_table tbody tr:last-child').clone();

        $('#menu_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_user_type"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_user_type',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_user_type',function(e){
        e.preventDefault();
        var box = $("#confirm-remove-row");

        var parent = $(this).parent().parent();
        var user_type = parent.children(':nth-child(2)').children('input').val();
        var user_type_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        $("#menu_value").text('User Type '+user_type);
        $("#confirm_user_type_delete").val(user_type_id);
        box.addClass("open");
    });
    $(document.body).on('click', '#confirm_user_type_delete',function(e){
        //e.preventDefault();
        var box = $("#confirm-remove-row");
        var user_type_id = $(this).val();
        $.ajax({
            type: 'GET',
            async: true,
            url: '/user-types/delete/' + user_type_id,
            success: function(data,textStatus){
                box.removeClass("open");
                window.location.replace('/user-types');
            },
            error: function(xhr,textStatus,error){
                alert(textStatus + ' ' + xhr);
            }
        });
        return false;
    });

});





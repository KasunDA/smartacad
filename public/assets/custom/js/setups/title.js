/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_title').click(function(e){
        e.preventDefault();
        var clone_row = $('#title_table tbody tr:last-child').clone();

        $('#title_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_title"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_title',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_title',function(e){
        e.preventDefault();
        var box = $("#confirm-remove-row");

        var parent = $(this).parent().parent();
        var title = parent.children(':nth-child(2)').children('input').val();
        var title_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        $("#menu_value").text('User Type '+title);
        $("#confirm_title_delete").val(title_id);
        box.addClass("open");
    });
    $(document.body).on('click', '#confirm_title_delete',function(e){
        //e.preventDefault();
        var box = $("#confirm-remove-row");
        var title_id = $(this).val();
        $.ajax({
            type: 'GET',
            async: true,
            url: '/user-types/delete/' + title_id,
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





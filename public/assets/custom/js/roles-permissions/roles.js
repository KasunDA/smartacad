/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_role').click(function (e) {
        e.preventDefault();
        var clone_row = $('#role_table tbody tr:last-child').clone();

        $('#role_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html(parseInt(clone_row.children(':nth-child(1)').html()) + 1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('input').val('');
        clone_row.children(':nth-child(4)').children('input').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_role"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click', '.remove_role', function () {
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_role',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var role = parent.children(':nth-child(2)').children('input').val();
        var role_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete role "+role,
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
                            url: '/roles/delete/' + role_id,
                            success: function(data,textStatus){
                                window.location.replace('/roles');
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





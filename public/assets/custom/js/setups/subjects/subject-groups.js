/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_subject_groups').click(function(e){
        e.preventDefault();
        var clone_row = $('#subject_groups_table tbody tr:last-child').clone();

        $('#subject_groups_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_subject_groups"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_subject_groups',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_subject_groups',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var subject_groups = parent.children(':nth-child(2)').children('input').val();
        var subject_groups_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete Subject Groups "+subject_groups,
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
                            url: '/subject-groups/delete/' + subject_groups_id,
                            success: function(data,textStatus){
                                window.location.replace('/subject-groups');
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





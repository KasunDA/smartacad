/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $('.add_assessment_setup').click(function(e){
        e.preventDefault();
        var clone_row = $('#assessment_setup_table tbody tr:last-child').clone();

        $('#assessment_setup_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('select').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':nth-child(3)').children('select').val('');
        clone_row.children(':nth-child(4)').children('select').val('');
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_assessment_setup"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_assessment_setup',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_assessment_setup',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var assessment_setup_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete Assessment Setup ",
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
                            url: '/assessment-setups/delete/' + assessment_setup_id,
                            success: function(data,textStatus){
                                window.location.replace('/assessment-setups');
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

    $(document.body).on('click', '.delete_assessment_detail',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var assessment_detail_id = parent.children(':nth-child(2)').children('input[type=hidden]').val();
        var classgroup_id = $(this).val();

        bootbox.dialog({
            message: "Are You sure You want to permanently delete this Assessment Setup Detail",
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
                            url: '/assessment-setups/details-delete/' + assessment_detail_id + '/' + classgroup_id,
                            success: function(data,textStatus){
                                window.location.replace('/assessment-setups/details');
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





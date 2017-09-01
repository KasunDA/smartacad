/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {

    $('.add_item_type').click(function(e){
        e.preventDefault();
        var clone_row = $('#menu_table tbody tr:last-child').clone();

        $('#menu_table tbody').append(clone_row);

        clone_row.children(':nth-child(1)').html( parseInt(clone_row.children(':nth-child(1)').html())+1);
        clone_row.children(':nth-child(2)').children('input').val('');
        clone_row.children(':nth-child(2)').children('input[type=hidden]').val(-1);
        clone_row.children(':last-child').html('<button class="btn btn-danger btn-rounded btn-condensed btn-sm remove_item_type"><span class="fa fa-times"></span> Remove</button>');
    });

    $(document.body).on('click','.remove_item_type',function(){
        $(this).parent().parent().remove();
    });

    $(document.body).on('click', '.delete_item_type',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var id = $(this).val();
        var name = parent.children(':nth-child(2)').html();
        var url = '/item-types/delete/' + id;

        swal({
                title: "Are you sure?",
                text: 'Are You sure You want to permanently delete: <span class="bold">' + name + '</span>',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false,
                html: true
                // timer: 3000,
                // allowOutsideClick: true
            },
            function(){
                $.ajax({
                    type: 'GET',
                    async: true,
                    url: url,
                    success: function(data,textStatus){
                        swal("Deleted!", name + " record has been deleted.", "success");
                        window.location.reload();
                        // window.location.replace(redirect);
                    },
                    error: function(xhr,textStatus,error){
                        swal("Server Error!", "Error encountered please try again later...", "error");
                    }
                });
            }
        );
        
    });
});






$(function () {
    $(document.body).on('click', '.confirm-delete-btn',function(e){
        e.preventDefault();

        var title = $.trim($(this).data('title'));
        title = (title != '') ? title : 'Are you sure?';

        var name = $.trim($(this).data('name'));
        name = (name != '') ? name : 'it';

        var type = $.trim($(this).data('type'));
        type = (type != '') ? type : 'warning';

        var message = $.trim($(this).data('message'));
        message = (message != '') ? message : 'Are You sure You want to permanently delete: <span class="bold">' + name + '</span>';

        var closeText = $.trim($(this).data('close-text'));
        closeText = (closeText.length) ? closeText : 'Cancel';

        var confirmText = $.trim($(this).data('confirm-text'));
        confirmText = (confirmText.length) ? confirmText : 'Yes, delete it!';

        var status = $.trim($(this).data('status'));
        status = (status.length) ? status : 'Deleted!';

        var statusText = $.trim($(this).data('status-text'));
        statusText = (statusText.length) ? statusText : name + ' record has been deleted.!';

        var action = $(this).data('action');

        var redirect = $.trim($(this).data('redirect'));

        swal({
                title: title,
                text: message,
                type: type,
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: confirmText,
                closeOnConfirm: false,
                html: true
                // timer: 3000,
                // allowOutsideClick: true
            },
            function(){
                $.ajax({
                    type: 'GET',
                    async: true,
                    url: action,
                    success: function(data,textStatus){
                        swal(status, statusText, "success");
                        (redirect.length) ? window.location.replace(redirect) : window.location.reload();
//                    window.location.reload();
                        // window.location.replace(redirect);
                    },
                    error: function(xhr,textStatus,error){
                        swal("Server Error!", "Error encountered please try again later...", "error");
                    }
                });
            });
    });
});

/**
 * Created by Cecilee2 on 8/4/2015.
 */

$(function () {
    $(document.body).on('click', '.school_status',function(e){
        e.preventDefault();

        var parent = $(this).parent().parent();
        var user_name = parent.children(':nth-child(2)').text();
        var user_id = $(this).val();
        var value = $(this).attr('rel');
        var msg = (value === '1') ? '<strong>ACTIVATE '+user_name+' </strong>' : '<strong>DEACTIVATE '+user_name+'</strong>';
        //alert(user_name + ' = ' + value);

        bootbox.dialog({
            message: "Are You sure You want to "+msg+" on this application?",
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
                            url: '/schools/status/' + user_id + '/' + value,
                            success: function(data,textStatus){
                                window.location.replace('/schools');
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
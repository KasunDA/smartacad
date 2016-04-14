$(function () {

    $('#flash_message').delay(10000).slideUp(850);
});

//time function
function showTime() {
    var today = new Date();
    var hours = today.getHours();
    var min = today.getMinutes();
    var sec = today.getSeconds();
    var time = "";

    //time definition
    if (hours == 0) {
        time = "12";
    }

    if (hours < 10) {
        time += "0" + hours;
    } else if (hours <= 12) {
        time += hours;
    }

    if (hours > 12) {
        time += hours - 12;
    }

    if (min < 10) {
        time += ":0" + min;
    } else {
        time += ":" + min;
    }

    if (sec < 10) {
        time += ":0" + sec;
    } else {
        time += ":" + sec;
    }

    if (hours >= 12) {
        time += " PM";
    } else {
        time += " AM";
    }
    $("#timer").html(time);
    setTimeout("showTime()", 1000)
}

//Activing Tab And Links With the class set to active
function setTabActive(link) {
    var menu = $('ul.page-sidebar-menu li.active').removeClass('active');
    var parents = $(link).parents('li');
    parents.each(function(){
        $(this).addClass('active');
    });
}

// set a loading image
function small_loading_image(div) {
    div.html('<img src="/images/loading.gif" style="width:20px; height:20px" alt="Loading"/>');
}

//Dependent List Box
function getDependentListBox(parent, child, url) {
    parent.bind("change", function (event) {
        if (parent.val() == '') {
            child.val('');
        } else {
            parent.parent().next().html('<img src="/images/loading.gif" alt="Loading Image"/>');
            $.ajax({
                type: "get",
                async: true,
                //data:parent.serialize(),
                url: url + parent.val(),
                dataType: "html",
                success: function (data, textStatus) {
                    child.html(data);
                    parent.parent().next().html('');
                }
            });
        }
        return false;
    });
}

//Image file type and file size vaildation
function validateImageFile(id) {
    id.bind('change', function () {
        var size = this.files[0].size;
        var value = $(this).val().toLowerCase();
        var extension = value.substring(value.lastIndexOf('.'));
        if ($.inArray(extension, ['.gif', '.png', '.jpg', '.jpeg']) === -1) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            Invalid File Type. Require Only Image Files With Extensions Of .gif, .png, .jpg, .jpeg</div>')
        } else if (size >= 1048576) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            File Size To Large. Requires Only Files Less Than ' + (1048576 / 1024) + ' KB</div>')
        } else {
            $('#image_error').html('');
        }
    });
}

//Image file type and file size vaildation
function validateDocumentFile(id) {
    id.bind('change', function () {
        var size = this.files[0].size;
        var value = $(this).val().toLowerCase();
        var extension = value.substring(value.lastIndexOf('.'));
        if ($.inArray(extension, ['.pdf']) === -1) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            Invalid File Type. Require Only PDF Files With Extension Of .pdf</div>')
        } else if (size >= 5120000) {
            $('#image_error').html('<div class="alert alert-danger" style="margin:0; padding:0;">\n\
            File Size To Large. Requires Only Files Less Than ' + (5120000 / 1024) + ' MB</div>')
        } else {
            $('#image_error').html('');
        }
    });
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#img_prev')
                .attr('src', e.target.result)
                .width(150)
                .height(150);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
$(function() {
    "use strict";

    function clearFields() {
        $('#input-password').val('');
        $('#input-oldpassword').val('');
    }

    $(document).on('click','#btn-clear-pass',function(e) {
        e.preventDefault();
    
        clearFields();
       
        return false;
    });

    $(document).on('click', '#btn-change-pass', function(e) {
        e.preventDefault();

        var action = 'changepass';
        var password = $('#input-password').val();
        var oldpassword = $('#input-oldpassword').val();

        $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'password': password,
                'oldpassword': oldpassword
            },

            success: function(response, textStatus, jQxhr) {
                response = JSON.parse(response);
                // console.log(response);

                if (response['type'] == 'success') {

                    Swal.fire({

                        icon: 'success',
                        text: response['msg'],

                    }).then(function(){

                        clearFields();
                    });
    
                } else {
    
                    Swal.fire({

                        icon: 'error',
                        html: '<div style="color: #e84343">'+response['msg']+'</div>'

                    }).then(function(){
                        // window.location.reload();
                    });
                }
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });
});
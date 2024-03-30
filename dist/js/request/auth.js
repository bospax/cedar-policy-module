$(function() {
    "use strict";

    $(document).on('click', '#btn-login-submit', function(e) {
        e.preventDefault();

        var action = this.dataset.action;
        var username = $("#input-login-username").val();
        var password = $('#input-login-password').val();
        
        $.ajax({
            url: 'controllers/auth.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'username': username,
                'password': password
            },

            success: function(response, textStatus, jQxhr) {

                response = JSON.parse(response);

                // console.log(response);

                if (response['type'] == 'success') {

                    // console.log(response['type']);
                    window.location.href = "index.php";
    
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

    $(document).on('click', '#btn-signup-submit', function(e) {
        e.preventDefault();

        var action = this.dataset.action;
        var fullname = $('#input-fullname').val();
        var username = $('#input-username').val();
        var password = $('#input-password').val();
        var email = $('#input-email').val();
        var term_id = $('#user-combo-terminal').val();
        var sub_id = $('#user-combo-subunit').val();
        
        $.ajax({
            url: 'controllers/auth.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'fullname': fullname,
                'username': username,
                'password': password,
                'email': email,
                'term_id': term_id,
                'sub_id': sub_id
            },

            success: function(response, textStatus, jQxhr) {

                response = JSON.parse(response);

                // console.log(response);

                if (response['type'] == 'success') {

                    Swal.fire({

                        icon: 'success',
                        text: response['msg'],

                    }).then(function(){
                        window.location.href = "index.php";
                    });

                    // console.log(response['type']);
                    
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
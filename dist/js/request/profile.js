$(function() {
    "use strict";

    $(document).ready(function(){
        loadData();
    });

    function loadData() {
        var action = 'read';

        $.ajax({
            url: 'controllers/profile.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            beforeSend: function( xhr ) {
                // $('.cust-loader-document').show();
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-profile" class="light-logo" width="40" height="40"/>';
                $(".profile-wrapper").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                $(".profile-wrapper").html(response);
                // $('.cust-loader-document').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }
});
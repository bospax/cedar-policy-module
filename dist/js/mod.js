// here goes the extra template modification done by dev
$(document).ready(function() {

    loadDataTerminalCountLanding();
    loadDataUserCountLanding()
    
    $('.cust-card-menu').on('click', function() {

        var module = $(this).data('module');

        window.location.href = 'index.php?route='+module; 
    });
});

function loadDataTerminalCountLanding() {
    var action = 'termcount';

    $.ajax({
        url: 'controllers/terminal.php',
        type: 'post',
        contentType: 'application/x-www-form-urlencoded',
        datatype: 'json/html',
        data: {
            'action': action
        },

        success: function(response, textStatus, jQxhr) {
            $("#terminal-ctr").text(response);
            // console.log(response);
        },

        error: function(jqXhr, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}

function loadDataUserCountLanding() {
    var action = 'usercount';

    $.ajax({
        url: 'controllers/user.php',
        type: 'post',
        contentType: 'application/x-www-form-urlencoded',
        datatype: 'json/html',
        data: {
            'action': action
        },

        success: function(response, textStatus, jQxhr) {
            $("#user-ctr").text(response);
            // console.log(response);
        },

        error: function(jqXhr, textStatus, errorThrown) {
            console.log(errorThrown);
        }
    });
}


// datatables initiliazation
// $(document).ready(function() {

//     $('#table-terminal').DataTable({
//         dom: 'Bfrtip',
//         buttons: [
//             'csv', 'excel'
//         ]
//     });

//     $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
// });




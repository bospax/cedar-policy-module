$(function() {
    "use strict";

    $(document).ready(function(){
        loadData();
        loadCombo();
        loadDataTerminalCount();
    });

    function loadData() {
        var action = 'read';

        $.ajax({
            url: 'controllers/terminal.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            beforeSend: function( xhr ) {
                // $('.cust-loader').show();
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-terminal" class="light-logo" width="40" height="40"/>';
                $(".table-wrapper-terminal").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                $(".table-wrapper-terminal").html(response);
                // $('.cust-loader').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadDataTerminalCount() {
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

    function loadDataSubunit() {
        var action = 'read';

        $.ajax({
            url: 'controllers/subunit.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            beforeSend: function( xhr ) {
                // $('.cust-loader-subunit').show();
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-subunit" class="light-logo" width="40" height="40"/>';
                $(".table-wrapper-subunit").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                $(".table-wrapper-subunit").html(response);
                // $('.cust-loader-subunit').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadCombo() {
        var action = 'combo';

        $.ajax({
            url: 'controllers/terminal.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            beforeSend: function( xhr ) {
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-combo" class="light-logo" width="20" height="20"/>';
                $(".subunit-combo-wrapper").html(loader);
                // $('.cust-loader-combo').show();
            },

            success: function(response, textStatus, jQxhr) {
                $(".subunit-combo-wrapper").html(response);
                // $('.cust-loader-combo').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function clearFields() {
        $('#btn-add-terminal').attr('data-action', 'add');
        $('#input-tid').val('');
        $('#input-termcode').val('');
        $('#input-termname').val('');
    }

    $(document).on('click','#btn-clear-terminal',function(e) {
        e.preventDefault();
    
        clearFields();
       
        return false;
    });
    
    $(document).on('click', '#btn-add-terminal', function(e) {
        e.preventDefault();

        var tid = $('#input-tid').val();
        var termcode = $('#input-termcode').val();
        var termname = $('#input-termname').val();
        // var action = $(this).data('action');
        var action = this.dataset.action;

        // alert(action);

        $.ajax({
            url: 'controllers/terminal.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'termcode': termcode,
                'termname': termname,
                'tid': tid
            },

            success: function(response, textStatus, jQxhr) {
                response = JSON.parse(response);

                if (response['type'] == 'success') {

                    Swal.fire({

                        icon: 'success',
                        text: response['msg'],

                    }).then(function(){

                        clearFields();
                        loadData();
                        loadDataSubunit()
                        loadCombo();
                        loadDataTerminalCount()
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

    $(document).on('click','#table-terminal .cust-btn-del',function(e) {
        e.preventDefault();
    
        var action = 'delete';
        var tid = this.dataset.id;

        Swal.fire({

            title: 'Are you sure?',
            text: "Subunit of this Terminal will also be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
    
        }).then((result) => {
            
            if (result.value) {
                $.ajax({
                    url: 'controllers/terminal.php',
                    data: {
                        'action': action,
                        'tid': tid
                    },
                    type: 'post',
                    success: function(response){
            
                        var response = JSON.parse(response);
            
                        if (response['type'] == 'success') {

                            Swal.fire({
        
                                icon: 'success',
                                text: response['msg'],
        
                            }).then(function(){
        
                                loadData();
                                loadDataSubunit()
                                loadCombo();
                                loadDataTerminalCount()
                            });
            
                        } else {
            
                            Swal.fire({
        
                                icon: 'error',
                                html: '<div style="color: #e84343">'+response['msg']+'</div>'
        
                            }).then(function(){
                                // window.location.reload();
                            });
                        }
                    }
                });
            }
        })
       
        return false;
    });

    $(document).on('click', '#table-terminal .cust-btn-edit', function(e) {
        e.preventDefault();

        $('#btn-add-terminal').attr('data-action', 'edit');

        // get the data from table row with class into the input fieldv
        var tid = this.dataset.id;
        var termcode = this.dataset.tcode;
        var termname = this.dataset.tname;
        
        $('#input-tid').val(tid);
        $('#input-termcode').val(termcode);
        $('#input-termname').val(termname);
       
        return false;
    });
});


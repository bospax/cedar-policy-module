$(function() {
    "use strict";

    $(document).ready(function(){
        loadData();
        loadDataSubCount();
    });

    function loadData() {
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

    function loadDataSubCount() {
        var action = 'subcount';

        $.ajax({
            url: 'controllers/subunit.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            success: function(response, textStatus, jQxhr) {
                $("#subunit-ctr").text(response);
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function clearFields() {
        $('#btn-add-subunit').attr('data-action', 'add');
        $('#input-sid').val('');
        $('#combo-subcode')[0].selectedIndex = 0;
        $('#input-subname').val('');
    }

    $(document).on('click','#btn-clear-subunit',function(e) {
        e.preventDefault();
    
        clearFields();
       
        return false;
    });

    $(document).on('click', '#btn-add-subunit', function(e) {
        e.preventDefault();

        var sid = $('#input-sid').val();
        var term_id = $('#combo-subcode').val();
        var subname = $('#input-subname').val();
        var action = this.dataset.action;

        // alert(sid + subname + term_id + action);

        $.ajax({
            url: 'controllers/subunit.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'term_id': term_id,
                'subname': subname,
                'sid': sid
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
                        loadDataSubCount()
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

    $(document).on('click','#table-subunit .cust-btn-del',function(e) {
        e.preventDefault();
    
        var action = 'delete';
        var sid = this.dataset.id;

        // alert(action + sid);

        Swal.fire({

            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
    
        }).then((result) => {
            
            if (result.value) {
                $.ajax({
                    url: 'controllers/subunit.php',
                    data: {
                        'action': action,
                        'sid': sid
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
                                loadDataSubCount()
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

    $(document).on('click', '#table-subunit .cust-btn-edit', function(e) {
        e.preventDefault();

        $('#btn-add-subunit').attr('data-action', 'edit');

        // get the data from table row with class into the input fieldv
        var sid = this.dataset.id;
        var term_id = this.dataset.termid;
        var subname = this.dataset.subname;
        
        $('#input-sid').val(sid);
        $('#combo-subcode').val(term_id);
        $('#input-subname').val(subname);
       
        return false;
    });
});
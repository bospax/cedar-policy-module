$(function() {
    "use strict";

    $(document).ready(function(){
        loadComboPosition();
        loadComboUserTerminal();
        loadComboSubunit();
        loadComboPermission();
        loadDataUserCount()
        loadData();
    });

    function loadComboPosition() {

        var action = 'combo-position';

         $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
            },

            beforeSend: function(xhr) {
                $('.cust-loader-position').show();
            },

            success: function(response, textStatus, jQxhr) {
                $(".position-combo-wrapper").html(response);
                $('.cust-loader-position').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadComboUserTerminal() {
        var action = 'combo-terminal';

         $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
            },

            beforeSend: function(xhr) {
                $('.cust-loader-user-terminal').show();
            },

            success: function(response, textStatus, jQxhr) {
                $(".user-combo-terminal-wrapper").html(response);
                $('.cust-loader-user-terminal').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadComboSubunit(term_id = '', sub_id = 0) {
        var action = 'combo-subunit';

         $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'term_id': term_id
            },

            beforeSend: function(xhr) {
                $('.cust-loader-user-subunit').show();
            },

            success: function(response, textStatus, jQxhr) {
                $(".user-combo-subunit-wrapper").html(response);
                $('.cust-loader-user-subunit').hide();
                $('#user-combo-subunit').val(sub_id);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadComboPermission(uid = '') {
        var action = 'combo-permission';

         $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'uid': uid
            },

            beforeSend: function(xhr) {
                $('.cust-loader-user-permission').show();
            },

            success: function(response, textStatus, jQxhr) {
                $(".permissions-wrapper").html(response);
                $('.cust-loader-user-permission').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadData() {
        var action = 'read';

        $.ajax({
           url: 'controllers/user.php',
           type: 'post',
           contentType: 'application/x-www-form-urlencoded',
           datatype: 'json/html',
           data: {
               'action': action
           },

           beforeSend: function(xhr) {
            //    $('.cust-loader-user-permission').show();
               var loader = '<img src="assets/images/loader.gif" class="cust-loader-user" class="light-logo" width="40" height="40"/>';
                $(".table-wrapper-user").html(loader);
           },

           success: function(response, textStatus, jQxhr) {
               $(".table-wrapper-user").html(response);
            //    $('.cust-loader-user').hide();
               // console.log(response);

               return true;
           },

           error: function(jqXhr, textStatus, errorThrown) {
               console.log(errorThrown);
           }
       });
    }

    function loadDataUserCount() {
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

    function clearFields() {

        $('#btn-add-user').attr('data-action', 'add');
        $('#input-uid').val('');
        $('#input-fullname').val('');
        $('#input-username').val('');
        $('#input-password').val('');
        $('#input-oldpassword').val('');
        $('#input-email').val('');

        $('#combo-position')[0].selectedIndex = 0;
        $('#combo-usertype')[0].selectedIndex = 0;
        $('#user-combo-terminal')[0].selectedIndex = 0;
        $('#user-combo-subunit')[0].selectedIndex = 0;

        $("#input-password").attr("placeholder", "Password");
        $('#oldpassword-wrapper').hide();

        $('#password-wrapper').show();

        $('input[name="permissions[]"]').each(function(i) {
            $(this).prop("checked", false);
        });
    }
    
    $(document).on('click', '#btn-clear-user', function(e) {
        e.preventDefault();

        clearFields();
    
        return false;
    });

    $(document).on('click', '#btn-add-user', function(e) {
        e.preventDefault();

        var action = this.dataset.action;

        var uid = $('#input-uid').val();
        var fullname = $('#input-fullname').val();
        var username = $('#input-username').val();
        var password = $('#input-password').val();
        var oldpassword = $('#input-oldpassword').val();
        var email = $('#input-email').val();

        var position = $('#combo-position').val();
        var usertype = $('#combo-usertype').val();
        var term_id = $('#user-combo-terminal').val();
        var sub_id = $('#user-combo-subunit').val();

        var permission = [];

        $('input[name="permissions[]"]:checked').each(function(i) {
            permission[i] = $(this).val();
        });

        // console.log(permission);

        $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'uid': uid,
                'fullname': fullname,
                'username': username,
                'password': password,
                'oldpassword': oldpassword,
                'email': email,
                'position': position,
                'usertype': usertype,
                'term_id': term_id,
                'sub_id': sub_id,
                'permission': permission
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
                        loadData();
                        loadDataUserCount()
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

    $(document).on('change','#chk-all',function(e) {

        if ($(this).is(':checked')) {
    
            $('input.policy-permission').each(function(i) {
                $(this).prop("checked", true);
            });
                
        } else {
            
            $('input.policy-permission').each(function(i) {
                $(this).prop("checked", false);
            });
        }
    });

    $(document).on('change',"input.policy-permission:not('#chk-all')", function(e) {
        var all = true;

        if (!$(this).is(':checked')) {
    
            $('#chk-all').prop("checked", false);    
        }

        $("input.policy-permission:not('#chk-all')").each(function(i) {
            if (!$(this).is(':checked')) {
                all = false;
            }
        });

        if (all) {
            $('#chk-all').prop("checked", true);
        }
    });

    $(document).on('change','select[name="user-combo-terminal"]',function(e) {
        e.preventDefault();
    
        var action = 'combo-subunit';
        var term_id = $('select[name="user-combo-terminal"]').val();

        loadComboSubunit(term_id);
       
        return false;
    });

    $(document).on('click', '#table-user .cust-btn-edit', function(e) {
        e.preventDefault();

        $('#btn-add-user').attr('data-action', 'edit');

        // get the data from table row with class into the input field
        var uid = this.dataset.id;
        var posid = this.dataset.posid;
        var fullname = this.dataset.fname;
        var usertype = this.dataset.utype;
        var sid = this.dataset.sid;
        var tid = this.dataset.tid;
        var uname = this.dataset.uname;
        var email = this.dataset.email;
        
        $('#input-uid').val(uid);
        $('#input-fullname').val(fullname);
        $('#combo-usertype').val(usertype);
        $('#combo-position').val(posid);
        $('#user-combo-terminal').val(tid);
        $('#input-username').val(uname);
        $('#input-email').val(email);

        $('#password-wrapper').hide();

        loadComboSubunit(tid, sid);
        loadComboPermission(uid);
       
        return false;
    });

    $(document).on('click', '#table-user .cust-btn-chg', function(e) {
        e.preventDefault();

        // old process
        // $('#btn-add-user').attr('data-action', 'changepass');

        // var uid = this.dataset.id;
        // var posid = this.dataset.posid;
        // var fullname = this.dataset.fname;
        // var usertype = this.dataset.utype;
        // var sid = this.dataset.sid;
        // var tid = this.dataset.tid;
        // var uname = this.dataset.uname;
        // var email = this.dataset.email;
        
        // $('#input-uid').val(uid);
        // $('#input-fullname').val(fullname);
        // $('#combo-usertype').val(usertype);
        // $('#combo-position').val(posid);
        // $('#user-combo-terminal').val(tid);
        // $('#input-username').val(uname);
        // $('#input-email').val(email);

        // $("#input-password").attr("placeholder", "New Password");
        // $('#password-wrapper').show();
        // $('#oldpassword-wrapper').show();

        // loadComboSubunit(tid, sid);
        // loadComboPermission(uid);

        // new process
        var action = 'resetpass';
        var uid = this.dataset.id;

        Swal.fire({
            title: 'Are you sure? Password will be reset for this user.',
            input: 'password',
            inputAttributes: {
                placeholder: 'Enter new password',
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Proceed',
            showLoaderOnConfirm: true,
            preConfirm: () => {

                var password = $('input.swal2-input').val();

                $.ajax({
                    url: 'controllers/user.php',
                    data: {
                        'action': action,
                        'uid': uid,
                        'password': password
                    },
                    type: 'post',
                    success: function(response){
            
                        var response = JSON.parse(response);
            
                        if (response['type'] == 'success') {

                            Swal.fire({
        
                                icon: 'success',
                                text: response['msg'],
        
                            }).then(function() {
 
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
            },
            
            allowOutsideClick: () => !Swal.isLoading()

        }).then((result) => {

        })
       
        return false;
    });

    $(document).on('click','#table-user .cust-btn-del',function(e) {
        e.preventDefault();
    
        var action = 'delete';
        var uid = this.dataset.id;

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
                    url: 'controllers/user.php',
                    data: {
                        'action': action,
                        'uid': uid
                    },
                    type: 'post',
                    success: function(response){
            
                        var response = JSON.parse(response);
            
                        if (response['type'] == 'success') {

                            Swal.fire({
        
                                icon: 'success',
                                text: response['msg'],
        
                            }).then(function() {
        
                                loadData();
                                loadDataUserCount();
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

    $(document).on('click','#table-user .cust-btn-view',function(e) {
        e.preventDefault();
    
        var action = 'user-details';
        var uid = this.dataset.id;

        $.ajax({
            url: 'controllers/user.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'uid': uid
            },

            success: function(response, textStatus, jQxhr) {
                $(".modal-user-wrapper").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });

    $(document).on('click', '#btn-import-user', function(e) {
        e.preventDefault();

        var action = 'import-user';

        var file_data = $('#input-import-user').prop('files')[0];   
        var form_data = new FormData();      

        form_data.append('file', file_data);
        form_data.append('action', action);

        // console.log(form_data);

        $.ajax({
            url: 'controllers/user.php', // point to server-side PHP script 
            type: 'post',
            datatype: 'json/html/text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,

            beforeSend: function(xhr) {
            //    $('.cust-loader-user-permission').show();
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-user" class="light-logo" width="20" height="20"/>';
                $(".import-user-wrapper").html(loader);
            },

            success: function(response) {
    
                var response = JSON.parse(response);

                $(".import-user-wrapper").html('');

                if (response['type'] == 'success') {

                    Swal.fire({
                        icon: 'success',
                        text: response['msg'],
                    }).then(function(){
                        clearFields();
                        loadData();
                        loadDataUserCount()
                    });
    
                } else if (response['type'] == 'invalid') {
    
                    Swal.fire({
                        title: response['msg'],
                        icon: 'info',
                        html: '<div style="color: #e84343">'+response['err']+'</div>'
                    }).then(function(){
                        clearFields();
                        loadData();
                        loadDataUserCount()
                    });
    
                } else {
    
                    Swal.fire({
                        icon: 'error',
                        text: response['msg'],
                    })
                }
            }
        });
       
        return false;
    });
});


$(function() {
    "use strict";

    $(document).ready(function(){
        $('#add-doc-form').hide();
        loadData();
        loadDataDocCount();
        // loadDataApproval();
    });

    function loadData() {
        var action = 'read';

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            beforeSend: function( xhr ) {
                // $('.cust-loader-document').show();
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-document" class="light-logo" width="40" height="40"/>';
                $(".table-wrapper-document").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                $(".table-wrapper-document").html(response);
                // $('.cust-loader-document').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadDataDocCount() {
        var action = 'doccount';

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            success: function(response, textStatus, jQxhr) {
                $("#document-ctr").text(response);
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function clearFields() {
        $('#btn-add-doc').attr('data-action', 'add');
        $('#btn-add-doc').text('Upload');
        $('.import-doc-wrapper').show();

        $('#input-did').val('');
        $('#input-title').val('');
        $('#input-tag').val('');
        $('#input-description').val('');
        $('#input-remarks').val('');
        $('#input-import-doc').val('');
        $('#input-import-attachment').val('');
        
        $('label[for="input-import-doc"]').text('Choose file');
        $(".attach-icon").css({"color":"#7d7e7f"});

        $('#add-doc-form').slideToggle(300);
        $('.add-new-doc').slideToggle(300);

        $('#chk-announce').prop("checked", false); 
    }

    $(document).on('click','#btn-clear-doc',function(e) {
        e.preventDefault();
    
        clearFields();
       
        return false;
    });

    $(document).on('click','.cust-btn-back',function(e) {
        e.preventDefault();
    
        loadData();
       
        return false;
    });

    $(document).on('click', '#btn-add-doc', function(e) {
        e.preventDefault();

        var action = this.dataset.action;
        var did = $('#input-did').val();
        var title = $('#input-title').val();
        var tags = $('#input-tag').val();
        var description = $('#input-description').val();
        var remarks = $('#input-remarks').val();
        var announce = '';

        if ($('#chk-announce').is(':checked')) {
            announce = 1;    
        } else {
            announce = 0;
        }

        var file_data = $('#input-import-doc').prop('files')[0];   
        var file_attachment = $('#input-import-attachment').prop('files')[0];

        var form_data = new FormData();      

        form_data.append('file', file_data);
        form_data.append('file_attachment', file_attachment);
        form_data.append('title', title);
        form_data.append('tags', tags);
        form_data.append('description', description);
        form_data.append('remarks', remarks);
        form_data.append('action', action);
        form_data.append('did', did);
        form_data.append('announce', announce);

        // console.log(file_data);
        // console.log(file_attachment);

        // console.log(action + title + tags + description + remarks);
        
        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            datatype: 'json/html/text',  // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,

            beforeSend: function( xhr ) {
                // $('.cust-loader-document').show();
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-document" class="light-logo" width="20" height="20"/>';
                $(".upload-action-wrapper").html(loader);
            },

            success: function(response, textStatus, jQxhr) {

                response = JSON.parse(response);

                // console.log(response);

                $(".upload-action-wrapper").html('');

                if (response['type'] == 'success') {

                    // console.log(response['type']);
                    Swal.fire({

                        icon: 'success',
                        text: response['msg'],

                    }).then(function(){
                        loadData();
                        clearFields();
                        loadDataDocCount();
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

                $(".upload-action-wrapper").html('');

                Swal.fire({

                    icon: 'error',
                    html: '<div style="color: #e84343">Check for valid file / filenames</div>'

                }).then(function(){
                    // window.location.reload();
                });
            }
        });
       
        return false;
    });

    $(document).on('click', '#table-document .cust-btn-pub', function(e) {
        e.preventDefault();

        var action = 'publish';
        var did = this.dataset.id;

        // console.log(action + did);
        
        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'did': did
            },

            success: function(response, textStatus, jQxhr) {

                response = JSON.parse(response);

                // console.log(response);

                if (response['type'] == 'success') {

                    // console.log(response['type']);
                    Swal.fire({

                        icon: 'success',
                        text: response['msg'],

                    }).then(function(){

                        loadData();
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

    $(document).on('click','#table-document .cust-btn-unpub',function(e) {
        e.preventDefault();
    
        var action = 'unpublish';
        var did = this.dataset.id;
    
        Swal.fire({
            title: 'Are you sure? File will be unpublished.',
            input: 'textarea',
            inputAttributes: {
                placeholder: 'Enter your Remarks..',
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Proceed',
            showLoaderOnConfirm: true,
            preConfirm: () => {

                var rmk = $('textarea.swal2-textarea').val();

                $.ajax({
                    url: 'controllers/document.php',
                    data: {
                        'action': action,
                        'did': did,
                        'rmk': rmk
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

    $(document).on('click', '#table-document .cust-btn-edit', function(e) {
        e.preventDefault();

        $('#btn-add-doc').attr('data-action', 'edit');
        $('#btn-add-doc').text('Save');
        // $('.import-doc-wrapper').hide();

        // get the data from table row with class into the input fieldv
        var did = this.dataset.id;
        var title = this.dataset.title;
        var type = this.dataset.type;
        var tags = this.dataset.tags;
        var descr = this.dataset.descr;
        var rmk = this.dataset.rmk;
        
        $('#input-did').val(did);
        $('#input-title').val(title);
        $('#input-tag').val(tags);
        $('#input-description').val(descr);
        $('#input-remarks').val(rmk);

        if (type == 1) {
            $('#chk-announce').prop("checked", true); 
        } else {
            $('#chk-announce').prop("checked", false); 
        }

        $('#add-doc-form').slideToggle(300);
        $('.add-new-doc').slideToggle(300);

        window.scrollTo(0, 0);
       
        return false;
    });

    $(document).on('click','#table-document .cust-btn-del',function(e) {
        e.preventDefault();
    
        var action = 'delete';
        var did = this.dataset.id;
    
        Swal.fire({
            title: 'Are you sure? File will be put into archive.',
            input: 'textarea',
            inputAttributes: {
                placeholder: 'Enter your Remarks..',
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Proceed',
            showLoaderOnConfirm: true,
            preConfirm: () => {

                var rmk = $('textarea.swal2-textarea').val();

                $.ajax({
                    url: 'controllers/document.php',
                    data: {
                        'action': action,
                        'did': did,
                        'rmk': rmk
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
                    }
                });
            },
            
            allowOutsideClick: () => !Swal.isLoading()

        }).then((result) => {

        })
       
        return false;
    });

    $(document).on('click','#table-document .cust-btn-view',function(e) {
        e.preventDefault();
    
        var action = 'doc-details';
        var did = this.dataset.id;

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'did': did
            },

            success: function(response, textStatus, jQxhr) {
                $(".modal-doc-wrapper").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });

    $(document).on('click','#table-document .cust-btn-filename',function(e) {
        e.preventDefault();
    
        var action = 'open-doc';
        var did = this.dataset.id;

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'did': did
            },

            success: function(response, textStatus, jQxhr) {
                $(".modal-doc-preview-wrapper").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });

    $(document).on('click','.view-org-doc',function(e) {
        e.preventDefault();
    
        var action = 'open-doc';
        var did = this.dataset.id;

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'did': did
            },

            success: function(response, textStatus, jQxhr) {
                $(".modal-doc-preview-wrapper").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });

    $(document).on('click','#table-document .cust-btn-upd',function(e) {
        e.preventDefault();
    
        var action = 'upload-rev';
        var did = this.dataset.id;

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'did': did
            },

            success: function(response, textStatus, jQxhr) {

                $(".modal-doc-revision-wrapper").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });

    $(document).on('click','#table-revision .cust-btn-upd',function(e) {
        e.preventDefault();
    
        var action = 'upload-rev';
        var did = this.dataset.id;

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'did': did
            },

            success: function(response, textStatus, jQxhr) {
                $(".modal-doc-revision-wrapper").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });

        return false;
    });

    $(document).on('click', '#table-document .cust-btn-dld', function(e) {
        e.preventDefault();

        var action = 'download';
        var did = this.dataset.id;
        var type = 'doc';

        window.open('controllers/download.php?did='+did+'&type='+type);
       
        return false;
    });

    $(document).on('click', '#table-document .cust-btn-dldall', function(e) {
        e.preventDefault();

        var action = 'download';
        var did = this.dataset.id;
        var type = 'all';

        window.open('controllers/download.php?did='+did+'&type='+type);
       
        return false;
    });

    $(document).on('click', '.cust-btn-attach', function(e) {
        e.preventDefault();

        var file = this.dataset.attach;

        window.open('controllers/download.php?attach='+file);
       
        return false;
    });

    $(document).on('change',"#input-import-doc", function(e) {
        var label = $('label[for="input-import-doc"]').text();
        var input = $(this).val().replace('C:\\fakepath\\', " ").substring(0, 35)+"...";
        
        if (input) {
            $('label[for="input-import-doc"]').text(input);
            // console.dir(input);
        }
    });

    $(document).on('change',"#input-import-attachment", function(e) {
        var file_data = $('#input-import-attachment').prop('files')[0];

        if (file_data) {
            $(".attach-icon").css({"color":"#3eac3e"});
        } else {
            $(".attach-icon").css({"color":"#7d7e7f"});   
        }
    });

    $(document).on('click','.add-new-doc',function(e) {
        e.preventDefault();
    
        $('#add-doc-form').slideToggle(300);
        $(this).slideToggle(300);
       
        return false;
    });
});
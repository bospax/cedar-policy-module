$(function() {
    "use strict";

    $(document).ready(function(){
        // loadData();
    });

    function clearFields() {
        $('#btn-add-rev').attr('data-action', 'add');
        $('#btn-add-rev').text('Upload');
        $('#modal-rev-label').text('Upload Revision');
        $('.import-rev-wrapper').show();

        $('#input-rid').val('');
        $('#input-rev-title').val('');
        $('#input-rev-tag').val('');
        $('#input-rev-description').val('');
        $('#input-rev-remarks').val('');
        $('#input-import-rev').val('');

        $(".attach-icon").css({"color":"#7d7e7f"});

        $('#chk-announce-rev').prop("checked", false);
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

    function loadData(rid = '') {
        var action = 'read';

        $.ajax({
            url: 'controllers/revision.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'rid': rid
            },

            beforeSend: function( xhr ) {
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-revision" class="light-logo" width="40" height="40"/>';
                // $(".table-wrapper-revision").html(loader);
                $(".table-wrapper-document").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                // $(".table-wrapper-revision").html(response);
                $(".table-wrapper-document").html(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function loadDocData() {
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

    $(document).on('click','#table-document .cust-btn-rev',function(e) {
        e.preventDefault();

        var rid = this.dataset.id;
    
        loadData(rid);
       
        return false;
    });

    $(document).on('click','#btn-clear-rev',function(e) {
        e.preventDefault();
    
        clearFields();
       
        return false;
    });

    $(document).on('click','#btn-add-rev',function(e) {
        e.preventDefault();
    
        var action = this.dataset.action;
        var rid = $('#input-rid').val();
        var title = $('#input-rev-title').val();
        var tags = $('#input-rev-tag').val();
        var description = $('#input-rev-description').val();
        var remarks = $('#input-rev-remarks').val();
        var announce = '';

        if ($('#chk-announce-rev').is(':checked')) {
            announce = 1;    
        } else {
            announce = 0;
        }

        var file_data = $('#input-import-rev').prop('files')[0];   
        var file_attachment = $('#input-import-attachment-rev').prop('files')[0];

        var form_data = new FormData();      

        form_data.append('file', file_data);
        form_data.append('file_attachment', file_attachment);
        form_data.append('title', title);
        form_data.append('tags', tags);
        form_data.append('description', description);
        form_data.append('remarks', remarks);
        form_data.append('action', action);
        form_data.append('rid', rid);
        form_data.append('announce', announce);

        // console.log(action);

        // console.log(file_data);
        // console.log(file_attachment);

        $.ajax({
            url: 'controllers/revision.php',
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

                    Swal.fire({

                        icon: 'success',
                        text: response['msg'],

                    }).then(function(){

                        if (action == 'add') {

                            loadDocData();
                            loadData(rid);
                            loadDataDocCount();

                        } else {
                            
                            var did = document.getElementById("table-revision");
                            did = did.dataset.docid;
                            loadData(did);
                        }

                        clearFields();
                        $('#modal-doc-revision').modal('toggle');
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

    $(document).on('click', '#table-revision .cust-btn-edit', function(e) {
        e.preventDefault();

        var action = 'edit-rev';
        var rid = this.dataset.id;
        var title = this.dataset.title;
        var tags = this.dataset.tags;
        var descr = this.dataset.descr;
        var rmk = this.dataset.rmk;
        var type = this.dataset.type;

        $.ajax({
            url: 'controllers/revision.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'rid': rid
            },

            success: function(response, textStatus, jQxhr) {
                $(".modal-doc-revision-wrapper").html(response);
                // console.log(response);
                $('#btn-add-rev').attr('data-action', 'edit');
                $('#btn-add-rev').text('Save');
                $('#modal-rev-label').text('Edit Revision');
                // $('.import-rev-wrapper').hide();

                // get the data from table row with class into the input field
                $('#input-rid').val(rid);
                $('#input-rev-title').val(title);
                $('#input-rev-tag').val(tags);
                $('#input-rev-description').val(descr);
                $('#input-rev-remarks').val(rmk);

                if (type == 1) {
                    $('#chk-announce-rev').prop("checked", true); 
                } else {
                    $('#chk-announce-rev').prop("checked", false); 
                }
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
       
        return false;
    });

    $(document).on('click','#table-revision .cust-btn-filename',function(e) {
        e.preventDefault();
    
        var action = 'open-rev';
        var rid = this.dataset.id;

        $.ajax({
            url: 'controllers/revision.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'rid': rid
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

    $(document).on('click','#table-revision .cust-btn-view',function(e) {
        e.preventDefault();
    
        var action = 'rev-details';
        var rid = this.dataset.id;

        // console.log(action + rid);

        $.ajax({
            url: 'controllers/revision.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'rid': rid
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

    $(document).on('click','#table-revision .cust-btn-del',function(e) {
        e.preventDefault();

        var action = 'delete';
        var rid = this.dataset.id;
    
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
                    url: 'controllers/revision.php',
                    data: {
                        'action': action,
                        'rid': rid,
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
        
                                // loadData();
                                var did = document.getElementById("table-revision");
                                did = did.dataset.docid;
                                loadData(did);
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

    $(document).on('click', '#table-revision .cust-btn-dld', function(e) {
        e.preventDefault();

        var action = 'download';
        var did = this.dataset.id;
        var type = 'rev';

        window.open('controllers/download.php?did='+did+'&type='+type);
       
        return false;
    });

    $(document).on('click', '#table-revision .cust-btn-pub', function(e) {
        e.preventDefault();

        var action = 'publish';
        var rid = this.dataset.id;

        // console.log(action + rid);
        
        $.ajax({
            url: 'controllers/revision.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action,
                'rid': rid
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

                        var did = document.getElementById("table-revision");
                        did = did.dataset.docid;
                        loadData(did);
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

    $(document).on('click','#table-revision .cust-btn-unpub',function(e) {
        e.preventDefault();
    
        var action = 'unpublish';
        var rid = this.dataset.id;
    
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
                    url: 'controllers/revision.php',
                    data: {
                        'action': action,
                        'rid': rid,
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
        
                                var did = document.getElementById("table-revision");
                                did = did.dataset.docid;
                                loadData(did);
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

    $(document).on('change',"#input-import-attachment-rev", function(e) {
        var file_data = $('#input-import-attachment-rev').prop('files')[0];

        if (file_data) {
            $(".attach-icon").css({"color":"#3eac3e"});
        } else {
            $(".attach-icon").css({"color":"#7d7e7f"});   
        }
    });

    $(document).on('change',"#input-import-rev", function(e) {
        var label = $('label[for="input-import-rev"]').text();
        var input = $(this).val().replace('C:\\fakepath\\', " ").substring(0, 35)+"...";
        
        if (input) {
            $('label[for="input-import-rev"]').text(input);
            console.dir(input);
        }
    });
});
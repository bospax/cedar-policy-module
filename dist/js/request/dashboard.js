$(function() {
    "use strict";

    $(document).ready(function(){
        loadDataApproval();
        // loadDataApprovalRevision();
    });

    function loadDataApproval() {
        var action = 'approval';

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
                var loader = '<img src="assets/images/loader.gif" class="cust-loader-approval" class="light-logo" width="40" height="40"/>';
                $(".table-wrapper-approval").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                $(".table-wrapper-approval").html(response);
                // $('.cust-loader-document').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    // function loadDataApprovalRevision() {
    //     var action = 'approval';

    //     $.ajax({
    //         url: 'controllers/revision.php',
    //         type: 'post',
    //         contentType: 'application/x-www-form-urlencoded',
    //         datatype: 'json/html',
    //         data: {
    //             'action': action
    //         },

    //         beforeSend: function( xhr ) {
    //             // $('.cust-loader-document').show();
    //             var loader = '<img src="assets/images/loader.gif" class="cust-loader-approval" class="light-logo" width="40" height="40"/>';
    //             $(".table-wrapper-approval-rev").html(loader);
    //         },

    //         success: function(response, textStatus, jQxhr) {
    //             $(".table-wrapper-approval-rev").html(response);
    //             // $('.cust-loader-document').hide();
    //             // console.log(response);
    //         },

    //         error: function(jqXhr, textStatus, errorThrown) {
    //             console.log(errorThrown);
    //         }
    //     });
    // }

    $(document).on('click','#table-approval .cust-btn-approve',function(e) {
        e.preventDefault();
    
        var action = 'approve';
        var uploadtype = this.dataset.uploadtype;
        var id = '';
        var data = '';
        var url = '';
    
        Swal.fire({
            title: 'Are you sure? File will be approved.',
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

                if (uploadtype == 'doc') {

                    id = this.dataset.id;
                    url = 'controllers/document.php';
        
                    data = {
                        'action': action,
                        'did': id,
                        'rmk': rmk
                    }
        
                } else if (uploadtype == 'rev') {
        
                    id = this.dataset.id;
                    url = 'controllers/revision.php';
        
                    data = {
                        'action': action,
                        'rid': id,
                        'rmk': rmk
                    }
                }

                $.ajax({
                    url: url,
                    data: data,
                    type: 'post',
                    success: function(response){
            
                        var response = JSON.parse(response);
            
                        if (response['type'] == 'success') {

                            Swal.fire({
        
                                icon: 'success',
                                text: response['msg'],
        
                            }).then(function() {
        
                                loadDataApproval();
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

    $(document).on('click','#table-approval .cust-btn-reject',function(e) {
        e.preventDefault();
    
        var action = 'reject';
        var uploadtype = this.dataset.uploadtype;
        var id = '';
        var data = '';
        var url = '';
        var form_data = new FormData();

        Swal.fire({

            title: 'Are you sure? File will be rejected.',

            html:
            '<textarea class="swal2-textarea" placeholder="Enter your Remarks.."></textarea>' +
            '<div class="custom-file text-left"><input type="file" name="input-import-attach" class="form-control-file" id="input-import-attach" accept=".pdf, .zip, .jpeg, .png, .jpg, .txt, .docx"><label class="custom-file-label" for="input-import-attach">Attach file</label></div>',

            showCancelButton: true,
            confirmButtonText: 'Proceed',
            focusConfirm: false,

            preConfirm: () => {
                
                var rmk = $('textarea.swal2-textarea').val();
                var file = $('#input-import-attach').prop('files')[0];

                if (uploadtype == 'doc') {

                    id = this.dataset.id;
                    url = 'controllers/document.php';

                    form_data.append('file', file);
                    form_data.append('action', action);
                    form_data.append('did', id);
                    form_data.append('rmk', rmk);
        
                    data = form_data;
        
                } else if (uploadtype == 'rev') {
        
                    id = this.dataset.id;
                    url = 'controllers/revision.php';
        
                    form_data.append('file', file);
                    form_data.append('action', action);
                    form_data.append('rid', id);
                    form_data.append('rmk', rmk);
        
                    data = form_data;
                }

                // console.log(rmk + ' ' + id + ' ' + url + ' ');
                // console.log(data);

                $.ajax({
                    url: url,
                    data: data,
                    datatype: 'json/html/text', 
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'post',
                    success: function(response){
            
                        var response = JSON.parse(response);
            
                        if (response['type'] == 'success') {

                            Swal.fire({
        
                                icon: 'success',
                                text: response['msg'],
        
                            }).then(function() {
        
                                loadDataApproval();
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

    $(document).on('click','#table-approval .cust-btn-filename',function(e) { 
        e.preventDefault();
    
        var uploadtype = this.dataset.uploadtype;
        var action = '';
        var id = '';
        var url = '';
        var data = '';

        if (uploadtype == 'doc') {

            id = this.dataset.id;
            url = 'controllers/document.php';
            action = 'open-doc';

            data = {
                'action': action,
                'did': id
            }

        } else if (uploadtype == 'rev') {

            id = this.dataset.id;
            url = 'controllers/revision.php';
            action = 'open-rev';

            data = {
                'action': action,
                'rid': id
            }
        }

        // console.log(id);
        // console.log(url);
        // console.log(data);

        $.ajax({
            url: url,
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: data,

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

    $(document).on('click','#table-approval .cust-btn-view',function(e) {
        e.preventDefault();

        var uploadtype = this.dataset.uploadtype;
        var action = '';
        var id = '';
        var url = '';
        var data = '';

        if (uploadtype == 'doc') {

            id = this.dataset.id;
            url = 'controllers/document.php';
            action = 'doc-details';

            data = {
                'action': action,
                'did': id
            }

        } else if (uploadtype == 'rev') {

            id = this.dataset.id;
            url = 'controllers/revision.php';
            action = 'rev-details';

            data = {
                'action': action,
                'rid': id
            }
        }

        $.ajax({
            url: url,
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: data,

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

    $(document).on('click', '#table-approval .cust-btn-dld', function(e) {
        e.preventDefault();

        var uploadtype = this.dataset.uploadtype;
        var did = this.dataset.id;
        var action = 'download';
        var type = '';

        if (uploadtype == 'doc') {

            type = 'doc';

        } else if (uploadtype == 'rev') {

            type = 'rev';
        }

        window.open('controllers/download.php?did='+did+'&type='+type);
       
        return false;
    });

    $(document).on('change',"#input-import-attach", function(e) {
        var label = $('label[for="input-import-attach"]').text();
        var input = $(this).val().replace('C:\\fakepath\\', " ").substring(0, 35)+"...";
        
        if (input) {
            $('label[for="input-import-attach"]').text(input);
            // console.dir(input);
        }
    });
});
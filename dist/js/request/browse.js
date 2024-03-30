$(function() {
    "use strict";

    $(document).ready(function(){
        loadDataPubCount();
        loadAnnouncement();
    });

    $(document).on('click','#loadmore',function(e) {
        e.preventDefault();
    
        $(".cust-result-item:hidden").slice(0, 4).slideDown();

        if ($(".cust-result-item:hidden").length == 0) {
            $("#loadmore").fadeOut('slow');
        }
       
        return false;
    });

    $(document).on('click','#loadmore-announce',function(e) {
        e.preventDefault();
    
        $(".cust-announce-item:hidden").slice(0, 3).slideDown();

        if ($(".cust-announce-item:hidden").length == 0) {
            $("#loadmore-announce").fadeOut('slow');
        }
       
        return false;
    });

    function loadDataPubCount() {
        var action = 'pubcount';

        $.ajax({
            url: 'controllers/document.php',
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: {
                'action': action
            },

            success: function(response, textStatus, jQxhr) {
                $("#publish-ctr").text(response);
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    function searchDocument(filter = '') {
        
        var action = 'search-doc';
        var search = $('.input-cust-search').val();

        if (search) {
            
            $.ajax({
                url: 'controllers/document.php',
                type: 'post',
                contentType: 'application/x-www-form-urlencoded',
                datatype: 'json/html',
                data: {
                    'action': action,
                    'search': search,
                    'filter': filter
                },
    
                beforeSend: function( xhr ) {
                    // $('.cust-loader-document').show();
                    var loader = '<div class="text-center"><img src="assets/images/loader.gif" class="light-logo" width="40" height="40"/></div>';
                    $(".search-result-wrapper").html(loader);
                },
    
                success: function(response, textStatus, jQxhr) {
                    $(".search-result-wrapper").html(response);
                    // $('.cust-loader-document').hide();
                    // console.log(response);
                },
    
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    }

    function loadAnnouncement() {
        var action = 'announcement';
            
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
                var loader = '<div class="text-center"><img src="assets/images/loader.gif" class="light-logo" width="40" height="40"/></div>';
                $("#annoucement-wrapper").html(loader);
            },

            success: function(response, textStatus, jQxhr) {
                $("#annoucement-wrapper").html(response);
                // $('.cust-loader-document').hide();
                // console.log(response);
            },

            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    }

    $(document).on('click','.btn-custom-search', function(e) {
        e.preventDefault();
    
        searchDocument();
       
        return false;
    });

    $(document).on('keyup','.input-cust-search', function(e) {
        e.preventDefault();
    
        if (e.keyCode == 13) {
            
            searchDocument();
        }
       
        return false;
    });

    $(document).on('change','#combo-filtersearch', function(e) {
        e.preventDefault();
    
        var filter = $(this).val();

        if (filter != 'null' && filter != '') {
            searchDocument(filter);
        }
       
        return false;
    });

    $(document).on('click','#cust-results .cust-btn-filename',function(e) {
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

        // console.log(uploadtype + ' ' + action + ' ' + id + ' ' + url);
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

    $(document).on('click','#cust-announcement .cust-btn-filename',function(e) {
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

        // console.log(uploadtype + ' ' + action + ' ' + id + ' ' + url);
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

    $(document).on('click', '.cust-btn-attachment', function(e) {
        e.preventDefault();

        var uploadtype = this.dataset.uploadtype;
        var action = '';
        var id = '';
        var url = '';
        var data = '';

        if (uploadtype == 'doc') {

            id = this.dataset.id;
            url = 'controllers/document.php';
            action = 'open-doc-attachment';

            data = {
                'action': action,
                'did': id
            }

        } else if (uploadtype == 'rev') {

            id = this.dataset.id;
            url = 'controllers/revision.php';
            action = 'open-rev-attachment';

            data = {
                'action': action,
                'rid': id
            }
        }

        // console.log(uploadtype + ' ' + action + ' ' + id + ' ' + url);
        // console.log(data);

        $.ajax({
            url: url,
            type: 'post',
            contentType: 'application/x-www-form-urlencoded',
            datatype: 'json/html',
            data: data,

            success: function(response, textStatus, jQxhr) {
                $(".modal-doc-preview-wrapper-attachment").html(response);
                // console.log(response);
            },
 
            error: function(jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
       
        return false;
    });
});
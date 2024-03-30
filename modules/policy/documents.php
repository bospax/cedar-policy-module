<?php include_once 'inc/header.php'; ?>

    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>

    <div id="main-wrapper">
        
        <div class="top-header-wrapper">

            <?php include_once 'inc/topbar.php'; ?>

            <?php include_once 'modules/policy/inc/sidebar.php'; ?>

        </div>

        <div class="page-wrapper cust-page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Documents</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php?action=policy">Policy</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Upload new Documents</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            <div class="mr-2">
                                <div class="lastmonth"></div>
                            </div>
                            <div class=""><small>UPLOAD</small>
                                <h4 id="document-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-right wrapper-add-doc">
                                    <button class="btn btn-info add-new-doc pull-right" data-action="add"><i class="mdi mdi-plus"></i> Upload</button>
                                </div>
                                
                                <div id="add-doc-form">
                                    <form>
                                        <div class="row">
                                            <input type="hidden" class="form-control cust-input-field" id="input-did">

                                            <div class="form-group col-lg-6">
                                                <input type="text" class="form-control cust-input-field" id="input-title" placeholder="Title *" required>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <input type="text" class="form-control cust-input-field" id="input-tag" placeholder="Tags1, Tags2, Control # (comma separated)" data-role="tagsinput" required>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <textarea type="text" class="form-control cust-input-field" id="input-description" placeholder="Description *" required></textarea>
                                            </div>
                                            <div class="form-group col-lg-6">
                                                <textarea type="text" class="form-control cust-input-field" id="input-remarks" placeholder="Remarks" required></textarea>
                                            </div>
                                            <div class="form-group col-lg-11 import-doc-wrapper">
                                                <div class="custom-file">
                                                    <input type="file" name="input-import-doc" class="form-control-file" id="input-import-doc" accept=".pdf">
                                                    <label class="custom-file-label" for="input-import-doc">Choose file</label>
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-1">
                                                <input type="file" name="input-import-attachment" id="input-import-attachment" accept=".pdf">
                                                <label for="input-import-attachment"><i class="mdi mdi-clipboard-text attach-icon"></i></label>
                                            </div>
                                            <div class="form-group col-lg-12">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" value="announce" class="custom-control-input" id="chk-announce">
                                                    <label class="custom-control-label" for="chk-announce">Pin on the Bulletin Board.</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button id="btn-add-doc" class="btn btn-info cust-btn-info" data-action="add">Upload</button>
                                            <button id="btn-clear-doc" class="btn btn-danger cust-btn-danger">Cancel</button>
                                        </div>
                                        <div class="upload-action-wrapper"></div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div id="modal-doc-details" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-sm modal-doc-wrapper">
                            <!-- response code here -->
                        </div>
                    </div>

                    <div id="modal-doc-preview" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-sm modal-doc-preview-wrapper">
                            <!-- response code here -->
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-document">
                                    <!-- <img src="assets/images/loader.gif" class="cust-loader-document" class="light-logo" width="40" height="40"/> -->
                                    <!-- response table here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div id="modal-doc-revision" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-sm modal-doc-revision-wrapper">
                            <!-- response code here -->
                            
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- <img src="assets/images/loader.gif" class="cust-loader-revision" class="light-logo" width="40" height="40"/> -->
                                <div class="table-responsive table-wrapper-revision">
                                    
                                    <!-- response table here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once 'inc/copyright.php'; ?>
        </div>
    </div>

<?php include_once 'modules/policy/inc/footer-policy.php'; ?>
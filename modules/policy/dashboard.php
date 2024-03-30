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
                        <h4 class="page-title">Dashboard</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php?action=policy">Policy</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Data Analytics</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            <div class="mr-2">
                                <div class="lastmonth"></div>
                            </div>
                            <div class=""><small>PUBLISHED</small>
                                <h4 id="publish-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <!-- <div id="modal-doc-details" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-sm modal-doc-wrapper">
                            
                        </div>
                    </div>

                    <div id="modal-doc-preview" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-sm modal-doc-preview-wrapper">
                            
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-approval">
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-approval-rev">
                                    
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include_once 'inc/copyright.php'; ?>

        </div>
    </div>

<?php include_once 'modules/policy/inc/footer-policy.php'; ?>
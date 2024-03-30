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

            <?php include_once 'inc/sidebar.php'; ?>

        </div>

        <div class="page-wrapper cust-page-wrapper">

            <div class="container-fluid">
                <div class="row">
                    <!-- column -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="access-denied"><i class="mdi mdi-alert-octagon"></i> Access Denied!</h6>
                                <p>You don't have the permission to view this page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include_once 'inc/copyright.php'; ?>

        </div>
    </div>

<?php include_once 'inc/footer.php'; ?>
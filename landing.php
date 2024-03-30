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
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Welcome! <?php echo $logged_userfullname; ?>,</h4>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            <div class="mr-2">
                                <div class="lastmonth"></div>
                            </div>
                            <div class="mr-2"><small>USER</small>
                                <h4 id="user-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                            <div class="mr-2"><small>TERMINAL</small>
                                <h4 id="terminal-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <!-- column -->
                    <div class="col-sm-12 col-lg-4">
                        <div class="card card-hover cust-card-menu" data-module="policy">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <span>Documents, Articles..</span>
                                        <h4>Policy & Procedure</h4>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="mdi mdi-book-minus"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- column -->
                    <div class="col-sm-12 col-lg-4">
                        <div class="card card-hover cust-card-menu" data-module="recruitment">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <span>Forms, Hiring..</span>
                                        <h4>Recruitment</h4>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="mdi mdi-account-multiple"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- column -->
                    <div class="col-sm-12 col-lg-4">
                        <div class="card card-hover cust-card-menu" data-module="seminar">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <span>Orientations, Startup..</span>
                                        <h4>Training & Seminar</h4>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="mdi mdi-checkerboard"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="card card-hover cust-card-menu" data-module="payroll">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="mr-2">
                                        <span>Salary, Miscellaneous..</span>
                                        <h4>Payroll</h4>
                                    </div>
                                    <div class="ml-auto">
                                        <i class="mdi mdi-cash-multiple"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include_once 'inc/copyright.php'; ?>

        </div>
    </div>

<?php include_once 'inc/footer.php'; ?>
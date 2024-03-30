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
                        <h4 class="page-title">Settings</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Account Credentials</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <!-- <div class="d-flex no-block justify-content-end align-items-center">
                            <div class="mr-2">
                                <div class="lastmonth"></div>
                            </div>
                            <div class="mr-2"><small>TERMINAL</small>
                                <h4 id="terminal-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                            <div class=""><small>SUBUNIT</small>
                                <h4 id="subunit-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form class="col-lg-6">
                                    <div class="form-group">
                                        <input type="password" class="form-control cust-input-field" id="input-oldpassword" placeholder="Old Password" required>
                                    </div>
                                    <div class="form-group" id="password-wrapper">
                                        <input type="password" class="form-control cust-input-field" id="input-password" placeholder="New Password" required>
                                    </div>
                                    <div class="form-group">
                                        <button id="btn-change-pass" class="btn btn-info cust-btn-info" data-action="changepass">Change Password</button>
                                        <button id="btn-clear-pass" class="btn btn-danger cust-btn-danger">Clear</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <?php include_once 'inc/copyright.php'; ?>

        </div>

    </div>

<?php include_once 'inc/footer.php'; ?>
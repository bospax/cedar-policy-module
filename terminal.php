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
                        <h4 class="page-title">Terminals</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add new Departments</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            <div class="mr-2">
                                <div class="lastmonth"></div>
                            </div>
                            <div class="mr-2"><small>TERMINAL</small>
                                <h4 id="terminal-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                            <div class=""><small>SUBUNIT</small>
                                <h4 id="subunit-ctr" class="text-info mb-0 font-medium">0</h4>
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
                                <form>
                                    <div class="row">
                                        <input type="hidden" class="form-control cust-input-field" id="input-tid">
                                        <div class="form-group col-lg-6">
                                            <input type="text" class="form-control cust-input-field" id="input-termcode" placeholder="Terminal Code" required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <input type="text" class="form-control cust-input-field" id="input-termname" placeholder="Terminal Name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button id="btn-add-terminal" class="btn btn-info cust-btn-info" data-action="add">Save</button>
                                        <button id="btn-clear-terminal" class="btn btn-danger cust-btn-danger">Clear</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-terminal">
                                    <!-- <img src="assets/images/loader.gif" class="cust-loader" class="light-logo" width="40" height="40"/> -->
                                    <!-- response table here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="row">
                                        <input type="hidden" class="form-control cust-input-field" id="input-sid">
                                        <div class="form-group col-lg-6 subunit-combo-wrapper text-center">
                                            <!-- <img src="assets/images/loader.gif" class="cust-loader-combo" class="light-logo" width="20" height="20"/> -->
                                            <!-- combo here -->
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <input type="text" class="form-control cust-input-field" id="input-subname" placeholder="Subunit Name" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button id="btn-add-subunit" class="btn btn-info cust-btn-info" data-action="add">Save</button>
                                        <button id="btn-clear-subunit" class="btn btn-danger cust-btn-danger">Clear</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-subunit">
                                    <!-- <img src="assets/images/loader.gif" class="cust-loader-subunit" class="light-logo" width="40" height="40"/> -->
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

<?php include_once 'inc/footer.php'; ?>
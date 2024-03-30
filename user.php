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
                        <h4 class="page-title">Users</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Create an Account</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            <div class="mr-2">
                                <div class="lastmonth"></div>
                            </div>
                            <div class="mr-2"><small>USER</small>
                                <h4 id="user-ctr" class="text-info mb-0 font-medium">0</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" name="input-import-user" class="form-control-file" id="input-import-user" accept=".csv">
                                            <label class="custom-file-label" for="input-import-user">Choose file</label>
                                        </div>
                                        <div class="input-group-append">
                                            <button id="btn-import-user" class="btn btn-info cust-btn-info" type="button">Import</button>
                                        </div>
                                    </div>
                                </form>
                                <div class="import-user-wrapper"></div>
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
                                        <input type="hidden" class="form-control cust-input-field" id="input-uid">
                                        <div class="form-group col-lg-6">
                                            <input type="text" class="form-control cust-input-field" id="input-fullname" placeholder="Fullname" required>
                                        </div>
                                        <div class="form-group col-lg-6 position-combo-wrapper text-center">
                                            <img src="assets/images/loader.gif" class="cust-loader-position" class="light-logo" width="20" height="20"/>
                                            <!-- response code here -->
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <input type="text" class="form-control cust-input-field" id="input-username" placeholder="Username" required>
                                        </div>
                                        <div class="form-group col-lg-6" id="oldpassword-wrapper">
                                            <input type="password" class="form-control cust-input-field" id="input-oldpassword" placeholder="Old Password" required>
                                        </div>
                                        <div class="form-group col-lg-6" id="password-wrapper">
                                            <input type="password" class="form-control cust-input-field" id="input-password" placeholder="Password" required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <input type="email" class="form-control cust-input-field" id="input-email" placeholder="Email" required>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <select name="combo-usertype" id="combo-usertype" class="select2 form-control custom-select col-12 cust-input-field">
                                                <option value="null">User Type</option>
                                                <option value="user">User</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 user-combo-terminal-wrapper text-center">
                                            <img src="assets/images/loader.gif" class="cust-loader-user-terminal" class="light-logo" width="20" height="20"/>
                                            <!-- response code here -->
                                        </div>
                                        <div class="form-group col-lg-6 user-combo-subunit-wrapper text-center">
                                            <img src="assets/images/loader.gif" class="cust-loader-user-subunit" class="light-logo" width="20" height="20"/>
                                            <!-- response code here -->
                                        </div>
                                    </div>

                                    <div class="row form-group permissions-wrapper">
                                        <img src="assets/images/loader.gif" class="cust-loader-user-permission" class="light-logo" width="20" height="20"/>
                                        <!-- response code here -->
                                    </div>
                                    <div class="form-group">
                                        <button id="btn-add-user" class="btn btn-info cust-btn-info" data-action="add">Save</button>
                                        <button id="btn-clear-user" class="btn btn-danger cust-btn-danger">Clear</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div id="modal-user-details" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-sm modal-user-wrapper">
                            <!-- response code here -->
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive table-wrapper-user">
                                    <img src="assets/images/loader.gif" class="cust-loader-user" class="light-logo" width="40" height="40"/>
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
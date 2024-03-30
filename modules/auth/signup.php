<?php include_once 'inc/header.php'; ?>

<div class="preloader">
    <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
    </div>
</div>

<div id="main-wrapper">
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
        <div class="auth-box">
            <div id="signupform">
                <div class="logo">
                    <span class="db"><img src="assets/images/logo-light-icon.png" alt="logo" width="50" height="50"/></span>
                </div>
                <!-- Form -->
                <div class="row">
                    <div class="col-12">
                        <form class="form-horizontal mt-3" id="user-signup" action="">
                            <div class="form-group col-lg-12">
                                <input type="text" class="form-control cust-input-field" id="input-fullname" placeholder="Fullname" required>
                            </div>
                            <div class="form-group col-lg-12">
                                <input type="text" class="form-control cust-input-field" id="input-username" placeholder="Username" required>
                            </div>
                            <div class="form-group col-lg-12" id="password-wrapper">
                                <input type="password" class="form-control cust-input-field" id="input-password" placeholder="Password" required>
                            </div>
                            <div class="form-group col-lg-12">
                                <input type="email" class="form-control cust-input-field" id="input-email" placeholder="Email" required>
                            </div>
                            <div class="form-group col-lg-12 user-combo-terminal-wrapper text-center">
                                <img src="assets/images/loader.gif" class="cust-loader-user-terminal" class="light-logo" width="20" height="20"/>
                                <!-- response code here -->
                            </div>
                            <div class="form-group col-lg-12 user-combo-subunit-wrapper text-center">
                                <img src="assets/images/loader.gif" class="cust-loader-user-subunit" class="light-logo" width="20" height="20"/>
                                <!-- response code here -->
                            </div>

                            <div class="form-group text-center">
                                <button id="btn-signup-submit" class="btn btn-block btn-lg btn-info" type="submit" data-action="signup">Sign Up</button>
                            </div>
                            <div class="form-group mb-0 mt-2 ">
                                <div class="col-sm-12 text-center ">
                                    Already have an account? <a href="index.php" class="text-info"><b>Log In</b></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'inc/footer.php'; ?>
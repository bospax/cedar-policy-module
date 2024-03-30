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
            <div id="loginform">
                <div class="logo">
                    <span class="db"><img src="assets/images/logo-light-icon.png" alt="logo" width="50" height="50"/></span>
                </div>
                <!-- Form -->
                <div class="row">
                    <div class="col-12">
                        <form class="form-horizontal mt-3" id="user-login" action="">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                </div>
                                <input type="text" id="input-login-username" class="form-control form-control-lg cust-input-field" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                </div>
                                <input type="password" id="input-login-password" class="form-control form-control-lg cust-input-field" placeholder="Password" aria-label="Password" aria-describedby="basic-addon1">
                            </div>

                            <div class="form-group text-center">
                                <button id="btn-login-submit" class="btn btn-block btn-lg btn-info" type="submit" data-action="login">Log In</button>
                            </div>
                            <div class="form-group mb-0 mt-2 ">
                                <div class="col-sm-12 text-center ">
                                    <!-- Don't have an account? <a href="index.php?route=signup" class="text-info"><b>Sign Up</b></a> -->
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
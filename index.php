<?php
require_once 'core/init.php';

$route = '';

if (isset($_GET['route'])) {
    $route = sanitize($_GET['route']);
}

if (!isset($_SESSION['user_id'])) {

    if (isset($_GET['route']) && $_GET['route'] == 'signup') {

        // require_once 'modules/auth/signup.php';

    } else {
        
        require_once 'modules/auth/login.php';
    }

} else {

    switch ($route) {
        case 'policy':
            
            if (in_array('policy', $module_permissions) && in_array('policy-dashboard', $logged_userpermissions) && in_array('policy-upload', $logged_userpermissions)) {

                require_once 'modules/policy/dashboard.php';
                break;

            } elseif (in_array('policy', $module_permissions) && in_array('policy-browse', $logged_userpermissions)) {

                require_once 'modules/policy/browse.php';
                break;

            } elseif (in_array('policy', $module_permissions) && in_array('policy-document', $logged_userpermissions) && in_array('policy-upload', $logged_userpermissions)) {

                require_once 'modules/policy/documents.php';
                break;

            } else {

                require_once 'denied.php';
                break;
            }
    
        case 'policy/documents':

            if (in_array('policy', $module_permissions) && in_array('policy-document', $logged_userpermissions) && in_array('policy-upload', $logged_userpermissions)) {

                require_once 'modules/policy/documents.php';
                break;

            } else {

                require_once 'denied.php';
                break;
            }
    
        case 'policy/browse':

            if (in_array('policy', $module_permissions)) {

                require_once 'modules/policy/browse.php';
                break;

            } else {

                require_once 'denied.php';
                break;
            }
    
        case 'terminal':

            if ($logged_userusertype == 'admin' && in_array('terminal', $logged_userpermissions)) {

                require_once 'terminal.php';
                break;

            } else {

                require_once 'denied.php';
                break;
            }
        
        case 'user':

            if ($logged_userusertype == 'admin' && in_array('user', $logged_userpermissions)) {

                require_once 'user.php';
                break;

            } else {

                require_once 'denied.php';
                break;
            }

        case 'profile':

            require_once 'profile.php';
            break;

        case 'setting':

            require_once 'setting.php';
            break;

        case 'logout':

            require_once 'modules/auth/logout.php';
            break;
    
        default:
            require_once 'landing.php';
            break;
    }
}
?>
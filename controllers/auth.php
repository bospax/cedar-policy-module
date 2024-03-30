<?php

require_once '../core/init_ajax.php'; 

$response = [];
$errors = [];
$action = '';
$username = '';
$password = '';
$empty = false;

$fullname = '';
$email = '';
$pos = '';
$usertype = '';
$term_id = '';
$sub_id = '';
$permission = [];

$user = new User();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);
    $username = (isset($_POST['username'])) ? sanitize($_POST['username']) : '';
    $password = (isset($_POST['password'])) ? sanitize($_POST['password']) : '';
    $fullname = (isset($_POST['fullname'])) ? sanitize($_POST['fullname']) : '';
    $email = (isset($_POST['email'])) ? sanitize($_POST['email']) : '';
    $sub_id = (isset($_POST['sub_id'])) ? sanitize($_POST['sub_id']) : '';
    $term_id = (isset($_POST['term_id'])) ? sanitize($_POST['term_id']) : '';

    // validate input
    if ($action == 'login') {

        $required = array('username', 'password');

        foreach($required as $field) {
            if ($_POST[$field] == '' || $_POST[$field] == 'null') {
                $empty = true;
                break;
            }
        }

        if ($empty == false) {

            $userAuth = $user->authenticateUsername($username);

            if (!empty($userAuth)) {

                $hashed = $userAuth[0]['password'];

                if (!password_verify($password, $hashed)) {
                    $errors[] = 'Authentication Failed.';
                }

            } else {
                $errors[] = 'Authentication Failed.';
            }
            
        } else {
            $errors[] = 'Please fill up all required fields';
        }
    }

    if ($action == 'signup') {

        $required = array('fullname', 'username', 'password', 'email', 'term_id');

        foreach($required as $field) {
            if ($_POST[$field] == '' || $_POST[$field] == 'null') {
                $empty = true;
                break;
            }
        }

        if ($empty == false) {

            if (preg_match('/[^a-zA-Z0-9,.()_ -]/', $fullname) || preg_match('/[^a-zA-Z0-9,.()_ -]/', $username)) {
                $errors[] = 'Special characters are not allowed.';
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'You must enter a valid email.';
            }

            if (strlen($password) < 8) {
                $errors[] = 'Password must be atleast 8 characters.';
            }

            $duplicate = $user->checkDuplicateUsername($username);
            $duplicate_email = $user->checkDuplicateEmail($email);

            if (!empty($duplicate)) {
                $errors[] = 'Username already exists.';
            }

            if (!empty($duplicate_email)) {
                $errors[] = 'Email already exists.';
            }
            
        } else {
            $errors[] = 'Please fill up all required fields';
        }
    }

    if (!empty($errors)) {

        $errors = implode('<br>', $errors);
        
        $response['type'] = 'error';
        $response['msg'] = $errors;

        echo encode($response);

    } else {
        
        if ($action == 'login') {

            // login code
            $userAuth = $user->authenticateUsername($username);

            $_SESSION['user_id'] = $userAuth[0]['id'];
            $response['type'] = 'success';

            echo encode($response);

        } elseif ($action == 'signup') {

            $pos = 5;
            $usertype = 'user';
            $permission_str = 'policy-read,policy-dld,policy-browse';
            $sub_id = (int)$sub_id;
            $term_id = (int)$term_id;
            $password = password_hash($password, PASSWORD_DEFAULT);

            $addUser = $user->addUser($fullname, $pos, $email, $username, $password, $usertype, $permission_str, $sub_id, $term_id);

            $response['type'] = 'success';
            $response['msg'] = 'You are now registered.';

            echo encode($response);
        }
    }
endif;

?>
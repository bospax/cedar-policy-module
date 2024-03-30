<?php

require_once 'database/DBCotroller.php';
require_once 'vendor/password/password.php';
require_once 'vendor/autoload.php';
require_once 'vendor/pdfparser/pdfparser.php';
require_once 'helpers/functions.php';

spl_autoload_register(function($class) {
	require_once "class/{$class}.php";
});

session_start();

if (isset($_SESSION['user_id'])) {

    $user = new User();
    $position = new Position();
    $subunit = new Subunit();
    $terminal = new Terminal();

    $user_id = $_SESSION['user_id'];

    $logged_userdata = $user->getUserByID($user_id);

    $logged_userfullname = $logged_userdata[0]['fullname'];
    $logged_userposid = $logged_userdata[0]['position'];
    $logged_useremail = $logged_userdata[0]['email'];
    $logged_userusername = $logged_userdata[0]['username'];
    $logged_userusertype = $logged_userdata[0]['usertype'];
    $logged_usersubid = $logged_userdata[0]['sub_id'];
    $logged_usertermid = $logged_userdata[0]['term_id'];
    $logged_userpermissions = $logged_userdata[0]['permission'];
    $logged_userpermissions = explode(',', $logged_userpermissions);

    $logged_positiondata = $position->getPosByID($logged_userposid);
    $logged_subunitdata = $subunit->getSubunitByID($logged_usersubid);
    $logged_terminaldata = $terminal->getTerminalByID($logged_usertermid);

    $logged_userposname = $logged_positiondata[0]['name'];
    $logged_userposlevel = $logged_positiondata[0]['level'];
    $logged_usersubunit = ($logged_subunitdata) ? $logged_subunitdata[0]['subname'] : '';
    $logged_usertermcode = $logged_terminaldata[0]['termcode'];
    $logged_usertermname = $logged_terminaldata[0]['termname'];

    $module_permissions = [];

    foreach ($logged_userpermissions as $value) {
        $value = explode('-', $value);
        $module = $value[0];

        if (!in_array($module, $module_permissions)) {
            $module_permissions[] = $module;
        }
    }
}

?>
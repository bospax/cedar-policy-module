<?php
require_once '../core/init_ajax.php';

$action = '';
$response = [];
$errors = [];
$empty = false;

$user = new User();
$position = new Position();
$terminal = new Terminal();
$subunit = new Subunit();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);

    // // validate input
    // if ($action == 'add' || $action == 'edit') {

    //     $required = array('termcode', 'termname');

    //     foreach($required as $field) {
    //         if ($_POST[$field] == '' || $_POST[$field] == 'null') {
    //             $empty = true;
    //             break;
    //         }
    //     }

    //     if ($empty == false) {

    //         // validation
            
    //     } else {
    //         $errors[] = 'Please fill up all required fields';
    //     }
    // }

    // if (!empty($errors)) {

    //     $errors = implode('<br>', $errors);
        
    //     $response['type'] = 'error';
    //     $response['msg'] = $errors;

    //     $response = json_encode($response);
    //     echo $response;

    // } else {
        
    //     if ($action == 'add') {

    //         $response['type'] = 'success';
    //         $response['msg'] = 'Data successfully added.';

    //         echo encode($response);

    //     } elseif ($action == 'edit') {

    //         // edit code

    //         $response['type'] = 'success';
    //         $response['msg'] = 'Data successfully updated.';

    //         echo encode($response);

    //     } elseif ($action == 'delete') {

    //         // delete code

    //         $response['type'] = 'success';
    //         $response['msg'] = 'Data successfully deleted.';

    //         echo encode($response);
    //     }
    // }

    if ($action == 'read') {

        $uid = $user_id;
        $user_data = $user->getUserByID($uid);

        $position_data = ($user_data) ? $position->getPosByID($user_data[0]['position']) : '';
        $position_name = ($position_data) ? $position_data[0]['name'] : '--';
        $position_level = ($position_data) ? $position_data[0]['level'] : '0';

        $subunit_data = ($user_data) ? $subunit->getSubunitByID($user_data[0]['sub_id']) : '';
        $subunit_name = ($subunit_data) ? $subunit_data[0]['subname'] : '--';

        $terminal_data = ($user_data) ? $terminal->getTerminalByID($user_data[0]['term_id']) : '';
        $terminal_code = ($terminal_data) ? $terminal_data[0]['termcode'] : '--';
        $terminal_name = ($terminal_data) ? $terminal_data[0]['termname'] : '--';

        $fullname = ($user_data) ? $user_data[0]['fullname'] : '';
        $email = ($user_data) ? $user_data[0]['email'] : '';
        $username = ($user_data) ? $user_data[0]['username'] : '';
        $usertype = ($user_data) ? $user_data[0]['usertype'] : '';

        $permission_str = ($user_data) ? $user_data[0]['permission'] : '';
        $permission_arr = explode(',', $permission_str);
    }

    if ($action == 'read') :  ?>
        <!-- table html here -->
        <img src="assets/images/users/1.png" alt="user" class="profile-image img-circle" width="70">
        <h4><?php echo $fullname; ?></h4>
        <h5><?php echo $position_name; ?></h5>
        <h6>User Info:</h6>
        <ul>
            <li><b>Level: </b><?php echo $position_level; ?></li>
            <li><b>Email: </b><?php echo $email; ?></li>
            <li><b>Username: </b><?php echo $username; ?></li>
            <li><b>Usertype: </b><?php echo $usertype; ?></li>
            <li><b>Terminal: </b><?php echo $terminal_name; ?></li>
            <li><b>Subunit: </b><?php echo $subunit_name; ?></li>
        </ul>

        <h6>Permissions:</h6>
        <ul class="list-permission">
        <?php if (!empty($permission_arr)) : ?>
            <?php foreach ($permission_arr as $v) : ?>

                <?php
                    if ($v == 'policy-pub') { $v = 'publish'; }
                    if ($v == 'policy-unpub') { $v = 'unpublish'; }
                    if ($v == 'policy-rev') { $v = 'revise'; }
                    if ($v == 'policy-dld') { $v = 'download'; }
                ?>

                <li><?php echo $v; ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>
    <?php
    endif;

endif;
?>
<?php

require_once '../core/init_ajax.php';

$action = '';
$uid = '';
$fullname = '';
$username = '';
$password = '';
$oldpassword = '';
$email = '';
$pos = '';
$usertype = '';
$term_id = '';
$sub_id = '';
$permission = [];

$response = [];
$errors = [];
$duplicate = '';
$duplicate_email = '';
$authPass = true;
$empty = false;

$user = new User();
$terminal = new Terminal();
$position = new Position();
$subunit = new Subunit();
$users = $user->getAllUser();
$positions = $position->getAllPosition();
$terminals = $terminal->getAllTerminal();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);
    $uid = (isset($_POST['uid'])) ? sanitize($_POST['uid']) : '';
    $fullname = (isset($_POST['fullname'])) ? sanitize($_POST['fullname']) : '';
    $username = (isset($_POST['username'])) ? sanitize($_POST['username']) : '';
    $password = (isset($_POST['password'])) ? sanitize($_POST['password']) : '';
    $oldpassword = (isset($_POST['oldpassword'])) ? sanitize($_POST['oldpassword']) : '';
    $email = (isset($_POST['email'])) ? sanitize($_POST['email']) : '';
    $pos = (isset($_POST['position'])) ? sanitize($_POST['position']) : '';
    $usertype = (isset($_POST['usertype'])) ? sanitize($_POST['usertype']) : '';
    $sub_id = (isset($_POST['sub_id'])) ? sanitize($_POST['sub_id']) : '';
    $term_id = (isset($_POST['term_id'])) ? sanitize($_POST['term_id']) : '';
    $permission = (isset($_POST['permission'])) ? $_POST['permission'] : [];

    if ($action == 'resetpass') {
        
        if (empty($password)) {
            $errors[] = 'Please fill up all required fields';
        }
    }

    if ($action == 'changepass') {

        $uid = $user_id;
        
        if (empty($password) || empty($oldpassword)) {

            $errors[] = 'Please fill up all required fields';

        } else {

            $authPass = $user->authenticatePassword($oldpassword, $uid);
    
            if (!$authPass) {
                $errors[] = 'Failed to authenticate your old password.';
            }

            if (strlen($password) < 8) {
                $errors[] = 'Password must be atleast 8 characters.';
            }
        }
    }

    // validate input
    if ($action == 'add' || $action == 'edit') {

        $required = array('fullname', 'username', 'email', 'position', 'usertype', 'sub_id', 'term_id');

        if (empty($password) && $action == 'add') {
            array_push($required, 'password');
        }

        // if (empty($password) && empty($oldpassword) && $action == 'changepass') {
        //     array_push($required, 'password');
        //     array_push($required, 'oldpassword');
        // }

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

            if ($action == 'add'  && strlen($password) < 8) {
                $errors[] = 'Password must be atleast 8 characters.';
            }

            if ($action == 'add') {

                $duplicate = $user->checkDuplicateUsername($username);
                $duplicate_email = $user->checkDuplicateEmail($email);

            } elseif ($action == 'edit') {

                $duplicate = $user->checkDuplicateUsername($username, $uid);
                $duplicate_email = $user->checkDuplicateEmail($email, $uid);

            }

            if (!empty($duplicate)) {
                $errors[] = 'Username already exists.';
            }

            if (!empty($duplicate_email)) {
                $errors[] = 'Email already exists.';
            }

            if (empty($permission)) {
                $errors[] = 'Select atleast one (1) user\'s privilege.';
            }
            
        } else {
            $errors[] = 'Please fill up all required fields';
        }
    }

    if (!empty($errors)) {

        $errors = implode('<br>', $errors);
        
        $response['type'] = 'error';
        $response['msg'] = $errors;

        $response = json_encode($response);
        echo $response;

    } else {
        
        if ($action == 'add') {

            // add code
            $permission_str = implode(',', $permission);
            $sub_id = (int)$sub_id;
            $term_id = (int)$term_id;
            $password = password_hash($password, PASSWORD_DEFAULT);

            $addUser = $user->addUser($fullname, $pos, $email, $username, $password, $usertype, $permission_str, $sub_id, $term_id);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully added.';

            echo encode($response);

        } elseif ($action == 'edit') {

            // edit code
            $permission_str = implode(',', $permission);
            $sub_id = (int)$sub_id;
            $term_id = (int)$term_id;

            $updateUser = $user->updateUser($fullname, $pos, $email, $username, $usertype, $permission_str, $sub_id, $term_id, $uid);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully updated.';

            echo encode($response);

        } elseif ($action == 'delete') {

            // delete code
            $deleteUser = $user->deleteUser($uid);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully deleted.';

            echo encode($response);

        } elseif ($action == 'changepass') {

            $uid = $user_id;

            // change pass code
            $password = password_hash($password, PASSWORD_DEFAULT);

            $changePass = $user->changePassword($password, $uid);

            $response['type'] = 'success';
            $response['msg'] = 'Password successfully changed.';

            echo encode($response);
            
        } elseif ($action == 'resetpass') {

            // change pass code
            $password = password_hash($password, PASSWORD_DEFAULT);

            $changePass = $user->changePassword($password, $uid);

            $response['type'] = 'success';
            $response['msg'] = 'Password successfully changed.';

            echo encode($response);
        }
    }

    if ($action == 'combo-position') {

        $position_entries = [];
        $position_entry = [];

        if (!empty($positions)) {
            foreach ($positions as $k => $v) {
                $position_entry['id'] = $positions[$k]['id'];
                $position_entry['name'] = $positions[$k]['name'];
                $position_entry['level'] = $positions[$k]['level'];

                $position_entries[] = $position_entry;
            }
        }
    }

    if ($action == 'combo-terminal') {

        $terminal_entries = [];
        $terminal_entry = [];

        if (!empty($terminals)) {
            foreach ($terminals as $k => $v) {
                $terminal_entry['id'] = $terminals[$k]['id'];
                $terminal_entry['termcode'] = $terminals[$k]['termcode'];
                $terminal_entry['termname'] = $terminals[$k]['termname'];

                $terminal_entries[] = $terminal_entry;
            }
        }
    }

    if ($action == 'combo-subunit') {

        $subunit_entries = [];
        $subunit_entry = [];

        $subunits = $subunit->getSubunitByTermID($term_id);

        if (!empty($subunits)) {
            foreach ($subunits as $k => $v) {
                $subunit_entry['id'] = $subunits[$k]['id'];
                $subunit_entry['term_id'] = $subunits[$k]['term_id'];
                $subunit_entry['subname'] = $subunits[$k]['subname'];

                $subunit_entries[] = $subunit_entry;
            }
        }
    }

    if ($action == 'usercount') {
        $usercount = 0;

        if (!empty($users)) {
            $usercount = count($users);
        }

        echo $usercount;
    }

    if ($action == 'read') {

        $user_entries = [];
        $user_entry = [];

        if (!empty($users)) {
            foreach ($users as $k => $v) {
    
                $position_data = $position->getPosByID($users[$k]['position']);
                $position_name = ($position_data) ? $position_data[0]['name'] : '--';
                $position_level = ($position_data) ? $position_data[0]['level'] : '0';

                $subunit_data = $subunit->getSubunitByID($users[$k]['sub_id']);
                $subunit_name = ($subunit_data) ? $subunit_data[0]['subname'] : '--';

                $terminal_data = $terminal->getTerminalByID($users[$k]['term_id']);
                $terminal_code = ($terminal_data) ? $terminal_data[0]['termcode'] : '--';
                $terminal_name = ($terminal_data) ? $terminal_data[0]['termname'] : '--';

                $user_entry['id'] = $users[$k]['id'];
                $user_entry['fullname'] = $users[$k]['fullname'];
                $user_entry['position'] = $users[$k]['position'];
                $user_entry['position_name'] = $position_name;
                $user_entry['position_level'] = $position_level;
                $user_entry['email'] = $users[$k]['email'];
                $user_entry['username'] = $users[$k]['username'];
                $user_entry['usertype'] = $users[$k]['usertype'];
                $user_entry['permission'] = $users[$k]['permission'];
                $user_entry['sub_id'] = $users[$k]['sub_id'];
                $user_entry['subunit_name'] = $subunit_name;
                $user_entry['term_id'] = $users[$k]['term_id'];
                $user_entry['terminal_code'] = $terminal_code;
                $user_entry['terminal_name'] = $terminal_name;

                $user_entries[] = $user_entry;
            }
        }

        // var_dump($user_entries);
    }

    if ($action == 'import-user') {

        $invalid = [];
        
        if (!empty($_FILES)) {

            $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/octet-stream'];
          
            if (in_array($_FILES["file"]["type"], $allowedFileType)) {
        
                $file = $_FILES["file"];
                $file = $_FILES['file']['tmp_name'];
        
                $handle = fopen($file, "r");
                $ctr = 1;
        
                while(($filesop = fgetcsv($handle, 1000, ",")) !== false) {
        
                    if ($ctr == 1) { 
                        $ctr++;
                        continue; 
                    }
        
                    $pid   = sanitize($filesop[0]);
                    $id_number = sanitize($filesop[1]);
                    $fullname = sanitize($filesop[2]);
                    $email = sanitize($filesop[3]);
                    $termcode = sanitize($filesop[4]);
                    $username = $id_number;
                    $password = $id_number;
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $permission_str = 'policy-read,policy-dld,policy-browse';

                    if (preg_match('/[^a-zA-Z0-9,.()_ -]/', $fullname)) {
                        $err = 'Special characters are not allowed.';
                        $invalid[] = 'ID: ['.$pid.'] - '.$err;
                        continue;
                    }
        
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $err = 'Email is not valid.';
                        $invalid[] = 'ID: ['.$pid.'] - '.$err;
                        continue;
                    }

                    $duplicate = $user->checkDuplicateUsername($username);
                    $duplicate_email = $user->checkDuplicateEmail($email);

                    if (!empty($duplicate)) {
                        $err = 'Username already exists.';
                        $invalid[] = 'ID: ['.$pid.'] - '.$err;
                        continue;
                    }
        
                    if (!empty($duplicate_email)) {
                        $err = 'Email already exists.';
                        $invalid[] = 'ID: ['.$pid.'] - '.$err;
                        continue;
                    }
        
                    $terminal_data = $terminal->getTerminalByCode($termcode);

                    if (empty($terminal_data)) {

                        $err = 'Terminal Code does not exists.';
                        $invalid[] = 'ID: ['.$pid.'] - '.$err;
                        continue;

                    }

                    $term_id = (!empty($terminal_data)) ? $terminal_data[0]['id'] : '';

                    $pos = 5;
                    $usertype = 'user';
                    $sub_id = 0;

                    if (!empty($id_number) && !empty($fullname) && !empty($email) && !empty($termcode)) {
                        
                        // insert query here
                        $result = $user->addUser($fullname, $pos, $email, $username, $password, $usertype, $permission_str, $sub_id, $term_id);

                        if (!empty($result) && !empty($invalid)) {

                            $invalid_str = implode('<br>', $invalid);
        
                            $response['type'] = "invalid";
                            $response['msg'] = "Not all data has been imported";
                            $response['err'] = $invalid_str;
        
                        } elseif (!empty($result) && empty($invalid)) {
        
                            $response['type'] = "success";
                            $response['msg'] = "CSV Data successfully imported!";
        
                        } else {
        
                            $response['type'] = "error";
                            $response['msg'] = "Problem in importing Excel Data";
                        }
        
                    } else {
                        $response['type'] = "error";
                        $response['msg'] = "Problem in importing Excel Data";
                    }
                }
        
            } else { 
                $response['type'] = "error";
                $response['msg'] = "Invalid file type. Upload a CSV file.";
            }
        
        } else {
            $response['type'] = "error";
            $response['msg'] = "Please upload a CSV file.";
        }

        $response = json_encode($response);
        echo $response;
    }

    if ($action == 'read') :  ?>
        <!-- table html here -->
        <table id="table-user" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fullname</th>
                    <th>Position</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($user_entries)) : ?>
                <?php foreach ($user_entries as $k => $v) : ?>
                <tr>
                    <td><?php echo $user_entries[$k]['id']; ?></td>
                    <td><?php echo $user_entries[$k]['fullname']; ?></td>
                    <td><?php echo $user_entries[$k]['position_name']; ?></td>
                    <td>
                        <button class="btn btn-success btn-xs cust-btn-view" data-id="<?php echo $user_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-user-details"><i class="mdi mdi-information-outline"></i></button>
                        <button class="btn btn-success btn-xs cust-btn-edit"
                            data-id="<?php echo $user_entries[$k]['id']; ?>" 
                            data-fname = "<?php echo $user_entries[$k]['fullname']; ?>" 
                            data-utype = "<?php echo $user_entries[$k]['usertype']; ?>" 
                            data-posid="<?php echo $user_entries[$k]['position']; ?>" 
                            data-sid="<?php echo $user_entries[$k]['sub_id']; ?>" 
                            data-tid="<?php echo $user_entries[$k]['term_id']; ?>" 
                            data-uname="<?php echo $user_entries[$k]['username']; ?>" 
                            data-email="<?php echo $user_entries[$k]['email']; ?>">
                            <i class="mdi mdi-border-color"></i>
                        </button>
                        <button class="btn btn-danger btn-xs cust-btn-chg" 
                            data-id="<?php echo $user_entries[$k]['id']; ?>" 
                            data-fname = "<?php echo $user_entries[$k]['fullname']; ?>" 
                            data-utype = "<?php echo $user_entries[$k]['usertype']; ?>" 
                            data-posid="<?php echo $user_entries[$k]['position']; ?>" 
                            data-sid="<?php echo $user_entries[$k]['sub_id']; ?>" 
                            data-tid="<?php echo $user_entries[$k]['term_id']; ?>" 
                            data-uname="<?php echo $user_entries[$k]['username']; ?>" 
                            data-email="<?php echo $user_entries[$k]['email']; ?>">
                            <i class="mdi mdi-key"></i></button>
                        <button class="btn btn-danger btn-xs cust-btn-del" data-id="<?php echo $user_entries[$k]['id']; ?>"><i class="mdi mdi-window-close"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <script>
            $('#table-user').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel'
                ]
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
        </script>
    <?php
    endif;

    if ($action == 'combo') : ?>
        <!-- combo html here -->
    <?php
    endif;

    if ($action == 'user-details') : 
        
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

    ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h6>User Info</h6>
                <ul>
                    <li><b>Fullname: </b><?php echo $fullname; ?></li>
                    <li><b>Position: </b><?php echo $position_name; ?></li>
                    <li><b>Level: </b><?php echo $position_level; ?></li>
                    <li><b>Email: </b><?php echo $email; ?></li>
                    <li><b>Username: </b><?php echo $username; ?></li>
                    <li><b>Usertype: </b><?php echo $usertype; ?></li>
                    <li><b>Terminal: </b><?php echo $terminal_name; ?></li>
                    <li><b>Subunit: </b><?php echo $subunit_name; ?></li>
                </ul>

                <h6>Permissions</h6>
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
            </div>
        </div>
    <?php
    endif;

    if ($action == 'combo-position') : ?>
        <!-- combo html here -->
        <select name="combo-position" id="combo-position" class="select2 form-control custom-select col-12 cust-input-field">
            <option value="null">Position</option>
            <?php if (!empty($position_entries)) : ?>
                <?php foreach ($position_entries as $k => $v) : ?>
                    <option value="<?php echo $position_entries[$k]['id']; ?>"><?php echo $position_entries[$k]['name']; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    <?php
    endif;

    if ($action == 'combo-terminal') : ?>
        <!-- combo html here -->
        <select name="user-combo-terminal" id="user-combo-terminal" class="select2 form-control custom-select col-12 cust-input-field">
            <option value="null">Department</option>
            <?php if (!empty($terminal_entries)) : ?>
                <?php foreach ($terminal_entries as $k => $v) : ?>
                    <option value="<?php echo $terminal_entries[$k]['id']; ?>"><?php echo $terminal_entries[$k]['termname']; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    <?php
    endif;

    if ($action == 'combo-subunit') : ?>
        
        <?php if (!empty($term_id) && $term_id != 'null') : ?>

            <select name="user-combo-subunit" id="user-combo-subunit" class="select2 form-control custom-select col-12 cust-input-field">
                <option value="0">Subunit</option>
                <?php if (!empty($subunit_entries)) : ?>
                <?php foreach ($subunit_entries as $k => $v) : ?>
                    <option value="<?php echo $subunit_entries[$k]['id']; ?>"><?php echo $subunit_entries[$k]['subname']; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
            </select>
            
        <?php else : ?>

            <select name="user-combo-subunit" id="user-combo-subunit" class="select2 form-control custom-select col-12 cust-input-field">
                <option value="0">Subunit</option>
            </select>

        <?php endif; ?>
    <?php
    endif;

    if ($action == 'combo-permission') : 
    
        if (!empty($uid)) {

            $user_data = $user->getUserByID($uid);

            if (!empty($user_data)) {

                $permission_str = $user_data[0]['permission'];
                $permission_arr = explode(',', $permission_str);
            }
        }

    ?>
        <!-- combo html here -->                             
        <div class="row col-lg-12 form-group">
            <h6 class="ml-2 card-title form-control form-control-custom">Policy Module</h6>

            <div class="col-lg-2">
                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-all" class="policy-permission custom-control-input" id="chk-all" <?php echo (!empty($permission_arr) && in_array('policy-all', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-all">All</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-read" class="policy-permission custom-control-input" id="chk-read" <?php echo (!empty($permission_arr) && in_array('policy-read', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-read">Read</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-edit" class="policy-permission custom-control-input" id="chk-edit" <?php echo (!empty($permission_arr) && in_array('policy-edit', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-edit">Edit</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-delete" class="policy-permission custom-control-input" id="chk-delete" <?php echo (!empty($permission_arr) && in_array('policy-delete', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-delete">Delete</label>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-approve" class="policy-permission custom-control-input" id="chk-approve" <?php echo (!empty($permission_arr) && in_array('policy-approve', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-approve">Approve</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-reject" class="policy-permission custom-control-input" id="chk-reject" <?php echo (!empty($permission_arr) && in_array('policy-reject', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-reject">Reject</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-pub" class="policy-permission custom-control-input" id="chk-pub" <?php echo (!empty($permission_arr) && in_array('policy-pub', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-pub">Publish</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-unpub" class="policy-permission custom-control-input" id="chk-unpub" <?php echo (!empty($permission_arr) && in_array('policy-unpub', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-unpub">Unpublish</label>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-upload" class="policy-permission custom-control-input" id="chk-upload" <?php echo (!empty($permission_arr) && in_array('policy-upload', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-upload">Upload</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-rev" class="policy-permission custom-control-input" id="chk-rev" <?php echo (!empty($permission_arr) && in_array('policy-rev', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-rev">Revise</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-dld" class="policy-permission custom-control-input" id="chk-dld" <?php echo (!empty($permission_arr) && in_array('policy-dld', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-dld">Download</label>
                    </div>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-dashboard" class="policy-permission custom-control-input" id="chk-dashboard" <?php echo (!empty($permission_arr) && in_array('policy-dashboard', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-dashboard">Dashboard</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-browse" class="policy-permission custom-control-input" id="chk-browse" <?php echo (!empty($permission_arr) && in_array('policy-browse', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-browse">Browse</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="policy-document" class="policy-permission custom-control-input" id="chk-document" <?php echo (!empty($permission_arr) && in_array('policy-document', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-document">Documents</label>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row col-lg-12 form-group">
            <h6 class="ml-2 card-title form-control form-control-custom">General</h6>

            <div class="col-lg-2">
                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="terminal" class="custom-control-input" id="chk-terminal" <?php echo (!empty($permission_arr) && in_array('terminal', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-terminal">Terminal</label>
                    </div>
                </div>

                <div class="form-check">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" name="permissions[]" value="user" class="custom-control-input" id="chk-user" <?php echo (!empty($permission_arr) && in_array('user', $permission_arr)) ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="chk-user">User</label>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endif;
endif;
?>
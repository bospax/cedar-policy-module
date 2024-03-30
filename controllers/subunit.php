<?php

require_once '../core/init_ajax.php';

$action = '';
$sid = '';
$term_id = '';
$subname = '';
$response = [];
$errors = [];
$duplicate = '';
$empty = false;

$terminal = new Terminal();
$subunit = new Subunit();
$terminals = $terminal->getAllTerminal();
$subunits = $subunit->getAllSubunit();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);
    $sid = (isset($_POST['sid'])) ? sanitize($_POST['sid']) : '';
    $term_id = (isset($_POST['term_id'])) ? sanitize($_POST['term_id']) : '';
    $subname = (isset($_POST['subname'])) ? sanitize($_POST['subname']) : '';

    // validate input
    if ($action == 'add' || $action == 'edit') {

        $required = array('term_id', 'subname');

        foreach($required as $field) {
            if ($_POST[$field] == '' || $_POST[$field] == 'null') {
                $empty = true;
                break;
            }
        }

        if ($empty == false) {

            if (preg_match('/[^a-zA-Z0-9,.()_ -]/', $term_id) || preg_match('/[^a-zA-Z0-9,.()_ -]/', $subname)) {
                $errors[] = 'Special characters are not allowed.';
            }

            if ($action == 'add') {

                // duplicate code
                $duplicate = $subunit->checkDuplicateSubunit($term_id, $subname);

            } elseif ($action == 'edit') {

                // duplicate code
                $duplicate = $subunit->checkDuplicateSubunit($term_id, $subname, $sid);
            }

            if (!empty($duplicate)) {
                $errors[] = 'Subunit already exists.';
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
        
        if ($action == 'add') {

            // add code
            $addSub = $subunit->addSubunit($term_id, $subname);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully added.';

            echo encode($response);

        } elseif ($action == 'edit') {

            // edit code
            $updateSubunit = $subunit->updateSubunit($term_id, $subname, $sid);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully updated.';

            echo encode($response);

        } elseif ($action == 'delete') {

            // delete code
            $deleteSubunit = $subunit->deleteSubunit($sid);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully deleted.';

            echo encode($response);
        }
    }

    if ($action == 'subcount') {
        $subcount = 0;

        if (!empty($subunits)) {
            $subcount = count($subunits);
        }

        echo $subcount;
    }

    if ($action == 'read' || $action == 'combo') {

        $terminal_entries = [];
        $terminal_entry = [];
        $subunit_entries = [];
        $subunit_entry = [];

        if (!empty($terminals)) {
            foreach ($terminals as $k => $v) {
                $terminal_entry['id'] = $terminals[$k]['id'];
                $terminal_entry['termcode'] = $terminals[$k]['termcode'];
                $terminal_entry['termname'] = $terminals[$k]['termname'];

                $terminal_entries[] = $terminal_entry;
            }
        }

        if (!empty($subunits)) {
            foreach ($subunits as $k => $v) {

                $t = '';
                $tcode = '--';

                if (!empty($subunits[$k]['term_id'])) {
                    $t = $terminal->getTerminalByID($subunits[$k]['term_id']);
                    
                    if (!empty($t)) {
                        $tcode = $t[0]['termcode'];
                    }
                }

                $subunit_entry['id'] = $subunits[$k]['id'];
                $subunit_entry['tcode'] = $tcode;
                $subunit_entry['term_id'] = $subunits[$k]['term_id'];
                $subunit_entry['subname'] = $subunits[$k]['subname'];

                $subunit_entries[] = $subunit_entry;
            }
        }
    }

    if ($action == 'read') :  ?>
        <!-- table html here -->
        <table id="table-subunit" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Terminal Code</th>
                    <th>Subunit Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($subunit_entries)) : ?>
                <?php foreach ($subunit_entries as $k => $v) : ?>
                <tr>
                    <td><?php echo $subunit_entries[$k]['id']; ?></td>
                    <td class="tcode"><?php echo $subunit_entries[$k]['tcode']; ?></td>
                    <td class="subname"><?php echo $subunit_entries[$k]['subname']; ?></td>
                    <td>
                        <button class="btn btn-success btn-xs cust-btn-edit" 
                        data-subname="<?php echo $subunit_entries[$k]['subname']; ?>" 
                        data-termid="<?php echo $subunit_entries[$k]['term_id']; ?>" 
                        data-id="<?php echo $subunit_entries[$k]['id']; ?>"><i class="mdi mdi-border-color"></i></button>
                        <button class="btn btn-danger btn-xs cust-btn-del" data-id="<?php echo $subunit_entries[$k]['id']; ?>"><i class="mdi mdi-window-close"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <script>
            $('#table-subunit').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel'
                ]
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
        </script>
    <?php

    endif;

    if ($action == 'combo') : 

    ?>
        <!-- combo html here -->
        <select name="combo-subcode" id="combo-subcode" class="select2 form-control custom-select col-12 cust-input-field">
            <option value="null">Terminal Code</option>
            <?php if (!empty($terminal_entries)) : ?>
                <?php foreach ($terminal_entries as $k => $v) : ?>
                    <option value="<?php echo $terminal_entries[$k]['id']; ?>"><?php echo $terminal_entries[$k]['termname']; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    <?php
    endif;
endif;
?>
<?php

require_once '../core/init_ajax.php';

$action = '';
$tid = '';
$termcode = '';
$termname = '';
$response = [];
$errors = [];
$duplicate = '';
$empty = false;

$terminal = new Terminal();
$terminals = $terminal->getAllTerminal();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);
    $tid = (isset($_POST['tid'])) ? sanitize($_POST['tid']) : '';
    $termcode = (isset($_POST['termcode'])) ? sanitize($_POST['termcode']) : '';
    $termname = (isset($_POST['termname'])) ? sanitize($_POST['termname']) : '';

    // validate input
    if ($action == 'add' || $action == 'edit') {

        $required = array('termcode', 'termname');

        foreach($required as $field) {
            if ($_POST[$field] == '' || $_POST[$field] == 'null') {
                $empty = true;
                break;
            }
        }

        if ($empty == false) {

            if (preg_match('/[^a-zA-Z0-9,.()_ -]/', $termcode) || preg_match('/[^a-zA-Z0-9,.()_ -]/', $termname)) {
                $errors[] = 'Special characters are not allowed.';
            }

            if ($action == 'add') {

                $duplicate = $terminal->checkDuplicateTermcode($termcode);

            } elseif ($action == 'edit') {

                $duplicate = $terminal->checkDuplicateTermcode($termcode, $tid);
            }

            if (!empty($duplicate)) {
                $errors[] = 'Terminal Code already exists.';
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

            $addTerminal = $terminal->addTerminal($termcode, $termname);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully added.';

            echo encode($response);

        } elseif ($action == 'edit') {

            // edit code
            $updateTerminal = $terminal->updateTerminal($termcode, $termname, $tid);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully updated.';

            echo encode($response);

        } elseif ($action == 'delete') {

            // delete code
            $deleteTerminal = $terminal->deleteTerminal($tid);

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully deleted.';

            echo encode($response);
        }
    }

    if ($action == 'termcount') {
        $termcount = 0;

        if (!empty($terminals)) {
            $termcount = count($terminals);
        }

        echo $termcount;
    }

    if ($action == 'read' || $action == 'combo') {

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

    if ($action == 'read') :  ?>
        <!-- table html here -->
        <table id="table-terminal" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Terminal Code</th>
                    <th>Terminal Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($terminal_entries)) : ?>
                <?php foreach ($terminal_entries as $k => $v) : ?>
                <tr>
                    <td><?php echo $terminal_entries[$k]['id']; ?></td>
                    <td class="termcode"><?php echo $terminal_entries[$k]['termcode']; ?></td>
                    <td class="termname"><?php echo $terminal_entries[$k]['termname']; ?></td>
                    <td>
                        <button class="btn btn-success btn-xs cust-btn-edit" 
                        data-id="<?php echo $terminal_entries[$k]['id']; ?>" 
                        data-tcode="<?php echo $terminal_entries[$k]['termcode']; ?>" 
                        data-tname="<?php echo $terminal_entries[$k]['termname']; ?>" 
                        ><i class="mdi mdi-border-color"></i></button>
                        <button class="btn btn-danger btn-xs cust-btn-del" data-id="<?php echo $terminal_entries[$k]['id']; ?>"><i class="mdi mdi-window-close"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <script>
            $('#table-terminal').DataTable({
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
<?php

require_once '../core/init_ajax.php';

$action = '';
$rid = '';
$title = '';
$tags = '';
$description = '';
$remarks = '';
$rmk = '';
$status = '';
$announce = 0;
$filename_attachment = '';

$type = '';
$filename = '';
$date_uploaded = '';
$version = '';
$author = '';
$size = '';
$level = '';
$status = [];
$state = [];

$cdate = date('m/d/Y h:i a');

$response = [];
$errors = [];
$duplicate = '';
$empty = false;

$user = new User();
$position = new Position();
$document = new Document();
$revision = new Revision();
$pdfparser = new PDF2Text();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);
    $rid = (isset($_POST['rid'])) ? sanitize($_POST['rid']) : '';
    $title = (isset($_POST['title'])) ? sanitize($_POST['title']) : '';
    $tags = (isset($_POST['tags'])) ? sanitize($_POST['tags']) : '';
    $description = (isset($_POST['description'])) ? sanitize($_POST['description']) : '';
    $remarks = (isset($_POST['remarks'])) ? sanitize($_POST['remarks']) : '';
    $rmk = (isset($_POST['rmk'])) ? sanitize($_POST['rmk']) : '';
    $attach = (isset($_POST['attach'])) ? sanitize($_POST['attach']) : '';
    $announce = (isset($_POST['announce'])) ? sanitize($_POST['announce']) : 0;

    // validate input
    if ($action == 'add' || $action == 'edit') {

        $required = array('title', 'description');

        foreach($required as $field) {
            if ($_POST[$field] == '' || $_POST[$field] == 'null') {
                $empty = true;
                break;
            }
        }

        if ($action == 'add') {

            if (empty($_FILES['file'])) {
                $errors[] = 'Upload a pdf file.';
            }
    
            if (!empty($_FILES['file'])) {
    
                $filename = $_FILES['file']['name'];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $format = ['pdf'];
    
                if (!in_array($extension, $format)) {
                    $errors[] = 'Invalid file format.';
                }

                if (preg_match('/[^a-zA-Z0-9,.()_ ]/', $filename)) {
                    $errors[] = 'Filenames with dashes or special characters are not allowed.';
                }
            }

            if (!empty($_FILES['file_attachment'])) {
    
                $filename = $_FILES['file_attachment']['name'];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $format = ['pdf'];
    
                if (!in_array($extension, $format)) {
                    $errors[] = 'Invalid file format.';
                }

                if (preg_match('/[^a-zA-Z0-9,.()_ ]/', $filename)) {
                    $errors[] = 'Filenames with dashes or special characters are not allowed.';
                }
            }
        }

        if ($action == 'edit') {
    
            if (!empty($_FILES['file'])) {
    
                $filename = $_FILES['file']['name'];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $format = ['pdf'];
    
                if (!in_array($extension, $format)) {
    
                    $errors[] = 'Invalid file format.';
                }
            }

            if (!empty($_FILES['file_attachment'])) {
    
                $filename_attachment = $_FILES['file_attachment']['name'];
                $extension_attachment = pathinfo($filename_attachment, PATHINFO_EXTENSION);
                $format = ['pdf'];
    
                if (!in_array($extension_attachment, $format)) {
    
                    $errors[] = 'Invalid file format.';
                }
            }
        }

        if ($empty == false) {

            if (preg_match('/[^a-zA-Z0-9,.()_ -]/', $title) || preg_match('/[^a-zA-Z0-9,.()_ -]/', $description) || preg_match('/[^a-zA-Z0-9,.()_ -]/', $remarks) || preg_match('/[^a-zA-Z0-9,.()_ -]/', $tags)) {
                $errors[] = 'Special characters are not allowed.';
            }

            if ($action == 'add') {

                // duplicate code

            } elseif ($action == 'edit') {

                // duplicate code
            }

            if ($action == 'add' || $action == 'edit') {
    
                $revisions = $revision->getAllRevision();
                $upload = (!empty($_FILES['file'])) ? $_FILES['file']['name'] : '';

                if (!empty($revisions)) {
                    
                    foreach ($revisions as $k => $v) {
                        $fetched_title = $revisions[$k]['title'];
                        $filename = $revisions[$k]['filename'];
                        $filename = explode('-', $filename);
                        $rev_id = $revisions[$k]['id'];

                        if (($upload == $filename[1] && $action == 'add') || ($fetched_title == $title && $action == 'add')) {
                            $duplicate = 1;
                        }

                        if (($upload == $filename[1] && $rev_id != $rid && $action == 'edit') || ($fetched_title == $title && $rev_id != $rid && $action == 'edit')) {
                            $duplicate = 1;
                        }
                    }
                }
            }

            if (!empty($duplicate)) {
                $errors[] = 'Document already exists.';
            }
            
        } else {
            $errors[] = 'Please fill up all required fields';
        }
    }

    if ($action == 'delete') {
        if (preg_match('/[^a-zA-Z0-9,.()_ -]/', $rmk)) {
            $errors[] = 'Special characters are not allowed.';
        }
    }

    if (!empty($errors)) {

        $errors = implode('<br>', $errors);
        
        $response['type'] = 'error';
        $response['msg'] = $errors;

        echo encode($response);

    } else {
        
        if ($action == 'add') {

            $doc_id = $rid;

            $file = $_FILES['file']['tmp_name'];
            $filename = time().'-'.$_FILES['file']['name'];
            $size = $_FILES['file']['size'];
            $destination = '../uploads/documents/'.$filename;
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $author = (int)$user_id;

            if (isset($_FILES['file_attachment'])) {

                $file_attachment = $_FILES['file_attachment']['tmp_name'];
                $filename_attachment = time().'-'.$_FILES['file_attachment']['name'];
                $destination_attachment = '../uploads/attachments/'.$filename_attachment;

            } else {
                
                $file_attachment = '';
                $filename_attachment = '';
                $destination_attachment = '';
            }

            $user_data = $user->getUserByID($user_id);
            $pos_id = ($user_data) ? $user_data[0]['position'] : '';

            $position_data = $position->getPosByID($pos_id);
            $level = ($position_data) ? $position_data[0]['level'] : '';

            $document_data = $document->getDocByID($doc_id);
            $version = (int)$document_data[0]['revision'] + 1;
            $version = 'ver.'.$version;

            $doc_rev = (int)$document_data[0]['revision'] + 1;

            $max_level = $position->getMaxLevel();
            $max_level = $max_level[0]['max_level'];

            $type = ($level == $max_level) ? 'approved' : 'approval';

            // add code
            $status = [
                'type' => $type,
                'date' => $cdate,
                'user_id' => $author,
                'rmk' => '',
                'attach' => $filename_attachment
            ];

            $state = [
                'type' => 'uploaded',
                'date' => $cdate,
                'user_id' => $author,
                'rmk' => ''
            ];

            $status = encode($status);
            $state = encode($state);

            $extension = $announce;

            move_uploaded_file($file, $destination);
            move_uploaded_file($file_attachment, $destination_attachment);

            $pdfparser->setFilename($destination); 
            $pdfparser->decodePDF();

            $content = $pdfparser->output();

            $addRev = $revision->addRev($doc_id, $title, $extension, $description, $tags, $filename, $cdate, $version, $author, $size, $remarks, $level, $status, $state, $content);

            $document->updateRev($doc_rev, $doc_id);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully uploaded.';

            echo encode($response);

        } elseif ($action == 'edit') {

            // edit code
            if (!empty($_FILES['file'])) {

                $document_data = $revision->getRevByID($rid);
                $old_filename = (!empty($document_data)) ? $document_data[0]['filename'] : '';
                $old_filename = '../uploads/documents/'.$old_filename;

                unlink($old_filename);
    
                $file = $_FILES['file']['tmp_name'];
                $filename = time().'-'.$_FILES['file']['name'];
                $size = $_FILES['file']['size'];
                $destination = '../uploads/documents/'.$filename;
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                $author = (int)$user_id;

                $extension = $announce;

                move_uploaded_file($file, $destination);

                $pdfparser->setFilename($destination); 
                $pdfparser->decodePDF();

                $content = $pdfparser->output();

                $updateRev = $revision->updateRevAndFile($title, $extension, $description, $tags, $filename, $size, $remarks, $rid, $content);
            } 

            if (!empty($_FILES['file_attachment'])) {

                $document_data = $revision->getRevByID($rid);

                $status = (!empty($document_data)) ? $document_data[0]['status'] : '';
                $status = decode($status);

                $old_filename_attachment = (!empty($status)) ? $status['attach'] : '';
                $old_filename_attachment = '../uploads/attachments/'.$old_filename_attachment;

                if (!empty($status['attach'])) {
                    unlink($old_filename_attachment);
                }
    
                $file_attachment = $_FILES['file_attachment']['tmp_name'];
                $filename_attachment = time().'-'.$_FILES['file_attachment']['name'];
                $destination_attachment = '../uploads/attachments/'.$filename_attachment;

                move_uploaded_file($file_attachment, $destination_attachment);

                $status['attach'] = (!empty($status)) ? $filename_attachment : $status['attach'];
                $status = encode($status);

                $updateRev = $revision->updateDocAndFileAttachment($title, $description, $tags, $remarks, $status, $rid);
            }
            
            if (empty($_FILES)) {

                $extension = $announce;

                $updateRev = $revision->updateRev($title, $extension, $description, $tags, $remarks, $rid);
            }

            $response['type'] = 'success';
            $response['msg'] = 'File successfully updated.';

            echo encode($response);

        } elseif ($action == 'delete') {

            // delete code
            $revision_data = $revision->getRevByID($rid);
            
            if (!empty($revision_data)) {

                $state = $revision_data[0]['state'];
                $state = decode($state);

                $state['type'] = 'archived';
                $state['user_id'] = (int)$user_id;
                $state['date'] = $cdate;
                $state['rmk'] = $rmk;

                $state = encode($state);
            }

            $deleteRev = $revision->updateState($state, $rid);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully archived.';

            echo encode($response);

        } elseif ($action == 'approve') {

            // approve code
            $document_data = $revision->getRevByID($rid);
            
            if (!empty($document_data)) {

                $status = $document_data[0]['status'];
                $status = decode($status);

                $status['type'] = 'approved';
                $status['user_id'] = (int)$user_id;
                $status['date'] = $cdate;
                $status['rmk'] = $rmk;

                $status = encode($status);

                $level = $document_data[0]['level'];
                $level = (int)$level + 1;
            }

            $approveDoc = $revision->updateStatus($status, $rid);
            $levelUpDoc = $revision->updateLevel($level, $rid);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully approved.';

            echo encode($response);

        } elseif ($action == 'reject') {

            // approve code
            $document_data = $revision->getRevByID($rid);
            
            if (!empty($document_data)) {

                $status = $document_data[0]['status'];
                $status = decode($status);

                $status['type'] = 'rejected';
                $status['user_id'] = (int)$user_id;
                $status['date'] = $cdate;
                $status['rmk'] = $rmk;
                $status['attach'] = $attach;

                $status = encode($status);
            }

            $rejectDoc = $revision->updateStatus($status, $rid);

            $response['type'] = 'success';
            $response['msg'] = 'File has been rejected.';

            echo encode($response);

        } elseif ($action == 'publish') {

            // delete code
            $revisions = $revision->getAllRevision();
            $revision_data = $revision->getRevByID($rid);
            $document_data = (!empty($revision_data)) ? $document->getDocByID($revision_data[0]['doc_id']) : '';
            
            if (!empty($revision_data)) {

                $state = $revision_data[0]['state'];
                $state = decode($state);

                $state['type'] = 'published';
                $state['user_id'] = (int)$user_id;
                $state['date'] = $cdate;
                $state['rmk'] = $rmk;

                $state = encode($state);
            }

            $publishDoc = $revision->updateState($state, $rid);

            if (!empty($document_data)) {

                foreach ($document_data as $k => $v) {

                    $did = $document_data[$k]['id'];
                    $state = decode($document_data[$k]['state']);

                    if ($state['type'] == 'published') {

                        $state['type'] = 'unpublished';
                        $state['user_id'] = (int)$user_id;
                        $state['date'] = $cdate;
                        $state['rmk'] = $rmk;

                        $state = encode($state);

                        $updateState = $document->updateState($state, $did);
                    }
                }
            }

            // update state of revision's siblings
            if (!empty($revisions)) {

                foreach ($revisions as $k => $v) {

                    $state = decode($revisions[$k]['state']);

                    if ($state['type'] == 'published' && $revisions[$k]['id'] != $rid) {

                        $rid_pub = $revisions[$k]['id'];

                        $state['type'] = 'unpublished';
                        $state['user_id'] = (int)$user_id;
                        $state['date'] = $cdate;
                        $state['rmk'] = $rmk;

                        $state = encode($state);

                        $updateState = $revision->updateState($state, $rid_pub);
                    }
                }
            }

            $response['type'] = 'success';
            $response['msg'] = 'File successfully published.';

            echo encode($response);

        } elseif ($action == 'unpublish') {

            // delete code
            $document_data = $revision->getRevByID($rid);
            
            if (!empty($document_data)) {

                $state = $document_data[0]['state'];
                $state = decode($state);

                $state['type'] = 'unpublished';
                $state['user_id'] = (int)$user_id;
                $state['date'] = $cdate;
                $state['rmk'] = $rmk;

                $state = encode($state);
            }

            $publishDoc = $revision->updateState($state, $rid);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully unpublished.';

            echo encode($response);
        }
    }

    if ($action == 'revcount') {
        
    }

    if ($action == 'read') :  

        $revisions = $revision->getRevByDocID($rid);
        $document_data = $document->getDocByID($rid);

        $title = (!empty($document_data)) ? $document_data[0]['title'] : '';
        $filename = (!empty($document_data)) ? $document_data[0]['filename'] : '';

        $revision_entries = [];
        $revision_entry = [];

        if (!empty($revisions)) {
            foreach ($revisions as $k => $v) {  
                
                $max_level = $position->getMaxLevel();
                $max_level = $max_level[0]['max_level'];

                $revision_entry['id'] = $revisions[$k]['id'];
                $revision_entry['doc_id'] = $revisions[$k]['doc_id'];
                $revision_entry['title'] = $revisions[$k]['title'];
                $revision_entry['type'] = $revisions[$k]['type'];
                $revision_entry['description'] = $revisions[$k]['description'];
                $revision_entry['tags'] = $revisions[$k]['tags'];
                $revision_entry['filename'] = $revisions[$k]['filename'];
                $revision_entry['date_uploaded'] = $revisions[$k]['date_uploaded'];
                $revision_entry['version'] = $revisions[$k]['version'];
                $revision_entry['author'] = $revisions[$k]['author'];
                $revision_entry['size'] = $revisions[$k]['size'];
                $revision_entry['remarks'] = $revisions[$k]['remarks'];
                $revision_entry['level'] = $revisions[$k]['level'];
                $revision_entry['status'] = $revisions[$k]['status'];
                $revision_entry['state'] = $revisions[$k]['state'];

                $state = decode($revisions[$k]['state']);
                $status = decode($revisions[$k]['status']);
                $state_type = $state['type'];
                $status_type = $status['type'];

                $revision_entry['state_type'] = $state_type;
                $revision_entry['status_type'] = $status_type;

                $revision_entries[] = $revision_entry;
            }

            // var_dump($revision_entries);
        }

    ?>
        <!-- html table code -->
        <button class="btn btn-success btn-xs cust-btn-back"><i class="mdi mdi-arrow-left-bold"></i> Back</button>

        <?php if (!empty($revision_entries)) : ?>
            <h6>Original File info:</h6>

            <ul>
                <li><b>ID: </b><?php echo $rid; ?></li>
                <li><b>Title: </b><?php echo $title; ?></li>
                <li>
                    <b>File: </b>
                    <button class="btn btn-success btn-xs view-org-doc cust-btn-filename" data-id="<?php echo $rid; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo $filename; ?></button>
                </li>
            </ul>
        <?php endif; ?>

        <h6>Revisions:</h6>

        <table id="table-revision" data-docid= "<?php echo $rid; ?>" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>File</th>
                    <th>Version</th>
                    <th>Status</th>
                    <th>State</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($revision_entries)) : ?>
                <?php foreach ($revision_entries as $k => $v) : ?>
                <tr>
                    <td><?php echo $revision_entries[$k]['id']; ?></td>
                    <td data-toggle="tooltip" data-placement="top" title="<?php echo $revision_entries[$k]['title']; ?>"><?php echo cutTitle($revision_entries[$k]['title']); ?></td>
                    <td>
                        <?php if (in_array('policy-read', $logged_userpermissions)) : ?>
                            <button class="btn btn-success btn-xs cust-btn-filename" data-id="<?php echo $revision_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo cutFilename($revision_entries[$k]['filename']); ?></button>
                        <?php else: ?>
                            <?php echo cutFilename($revision_entries[$k]['filename']); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $revision_entries[$k]['version']; ?></td>
                    <td><?php 

                        $indicator = '';
                    
                        if ($revision_entries[$k]['status_type'] == 'approved' && $revision_entries[$k]['level'] != $max_level) {
                            $indicator = ' <i class="mdi mdi-information partially-appr-indicator" data-toggle="tooltip" data-placement="top" title="still needs verification by the highest approver."></i>';
                        } elseif ($revision_entries[$k]['status_type'] == 'approved' && $revision_entries[$k]['level'] == $max_level) {
                            $indicator = ' <i class="mdi mdi-check-circle fully-appr-indicator" data-toggle="tooltip" data-placement="top" title="file is now ready to be published."></i>';
                        }

                        echo ($revision_entries[$k]['status_type'] == 'approval') ? 'for approval' : $revision_entries[$k]['status_type'].$indicator; 
                        
                    ?></td>
                    <td><?php 
                    
                        $indicator_ann = '';

                        if ($revision_entries[$k]['state_type'] == 'published' && $revision_entries[$k]['type'] == 1) {
                            $indicator_ann = ' <i class="mdi mdi-pin pinned-indicator" data-toggle="tooltip" data-placement="top" title="currently pinned on the Bulletin Board."></i>';
                        }

                        echo $revision_entries[$k]['state_type'].$indicator_ann; ?>
                    </td>
                    <td>
                        <button class="btn btn-success btn-xs cust-btn-view" data-id="<?php echo $revision_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-details" data-toggle="tooltip" data-placement="top" title="info"><i class="mdi mdi-information-outline"></i></button>
                        <?php if ($revision_entries[$k]['state_type'] != 'deleted') : ?>

                            <?php if ((in_array('policy-edit', $logged_userpermissions) && $logged_userusertype == 'admin') || ((in_array('policy-edit', $logged_userpermissions) && $logged_userusertype == 'user' && $revision_entries[$k]['author'] == $user_id))) : ?>
                                <button class="btn btn-success btn-xs cust-btn-edit" 
                                data-id="<?php echo $revision_entries[$k]['id']; ?>"
                                data-title="<?php echo $revision_entries[$k]['title']; ?>"
                                data-tags="<?php echo $revision_entries[$k]['tags']; ?>"
                                data-descr="<?php echo $revision_entries[$k]['description']; ?>"
                                data-rmk="<?php echo $revision_entries[$k]['remarks']; ?>"
                                data-type="<?php echo $revision_entries[$k]['type']; ?>"
                                data-toggle="modal" data-target="#modal-doc-revision" 
                                data-toggle="tooltip" data-placement="top" title="edit">
                                <i class="mdi mdi-border-color"></i>
                                </button>                            
                            <?php endif; ?>

                            <?php if (in_array('policy-upload', $logged_userpermissions) && ($revision_entries[$k]['status_type'] == 'approved' || $revision_entries[$k]['status_type'] == 'rejected')) : ?>
                                <button class="btn btn-success btn-xs cust-btn-upd" data-id="<?php echo $revision_entries[$k]['doc_id']; ?>" data-toggle="modal" data-target="#modal-doc-revision" data-toggle="tooltip" data-placement="top" title="revise"><i class="mdi mdi-arrow-up-bold"></i></button>
                            <?php endif; ?>

                            <!-- <?php //if (in_array('dld', $logged_userpermissions)) : ?>
                                <button class="btn btn-success btn-xs cust-btn-dld" data-id="<?php //echo $revision_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="download"><i class="mdi mdi-arrow-down-bold"></i></button>
                            <?php //endif; ?> -->

                            <?php if ($revision_entries[$k]['level'] == $max_level) : ?>
                                <?php if (in_array('policy-pub', $logged_userpermissions) && $revision_entries[$k]['state_type'] != 'published') : ?>
                                    <button class="btn btn-success btn-xs cust-btn-pub" data-id="<?php echo $revision_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="publish"><i class="mdi mdi-layers"></i></button>
                                <?php endif; ?>

                                <?php if (in_array('policy-unpub', $logged_userpermissions) && $revision_entries[$k]['state_type'] == 'published') : ?>
                                    <button class="btn btn-danger btn-xs cust-btn-unpub" data-id="<?php echo $revision_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="unpublish"><i class="mdi mdi-layers-off"></i></button>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if ((in_array('policy-delete', $logged_userpermissions) && $logged_userusertype == 'user' && $revision_entries[$k]['author'] == $user_id) || (in_array('policy-delete', $logged_userpermissions) && $logged_userusertype == 'admin')) : ?>
                                <button class="btn btn-danger btn-xs cust-btn-del" data-id="<?php echo $revision_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="archive"><i class="mdi mdi-archive"></i></button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <script>
            $('#table-revision').DataTable({
                dom: 'Bfrtip',
                buttons: []
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
        </script>
    <?php
    endif;

    if ($action == 'approval') :  

        if ($logged_userusertype == 'admin') {
            $documents = $revision->getAllRevision();
        } 

        if ($logged_userusertype == 'user') {

            $level = ((int)$logged_userposlevel != 1) ? (int)$logged_userposlevel - 1 : 0;

            // if ($logged_userposlevel == '2') {
            //     $level = 1;
            // } elseif ($logged_userposlevel == '3') {
            //     $level = 2;
            // }

            $documents = $revision->getRevByLevel($level);
        }

        $document_entries = [];
        $document_entry = [];

        if (!empty($documents)) {
            foreach ($documents as $k => $v) {     
                
                $user_data = $user->getUserByID($documents[$k]['author']);
                $term_data = ($user_data) ? $terminal->getTerminalByID($user_data[0]['term_id']) : '';
                $termcode = ($term_data) ? $term_data[0]['termcode'] : '';

                $max_level = $position->getMaxLevel();
                $max_level = $max_level[0]['max_level'];

                $document_entry['id'] = $documents[$k]['id'];
                $document_entry['title'] = $documents[$k]['title'];
                $document_entry['type'] = $documents[$k]['type'];
                $document_entry['description'] = $documents[$k]['description'];
                $document_entry['tags'] = $documents[$k]['tags'];
                $document_entry['filename'] = $documents[$k]['filename'];
                $document_entry['date_uploaded'] = $documents[$k]['date_uploaded'];
                $document_entry['version'] = $documents[$k]['version'];
                $document_entry['author'] = $documents[$k]['author'];
                $document_entry['size'] = $documents[$k]['size'];
                $document_entry['remarks'] = $documents[$k]['remarks'];
                $document_entry['level'] = $documents[$k]['level'];
                $document_entry['status'] = $documents[$k]['status'];
                $document_entry['state'] = $documents[$k]['state'];

                $document_entry['termcode'] = $termcode;

                $status = decode($documents[$k]['status']);
                $state = decode($documents[$k]['state']);
                $state_type = $state['type'];
                $status_type = $status['type'];

                $document_entry['state_type'] = $state_type;
                $document_entry['status_type'] = $status_type;

                $document_entries[] = $document_entry;
            }

            // var_dump($document_entries);
        }
    ?>
    <?php if (!empty($document_entries)) : ?>
        <h6>For Approval: (Revisions)</h6>

        <table id="table-revision" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>File</th>
                    <th>Date Uploaded</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($document_entries)) : ?>
                <?php foreach ($document_entries as $k => $v) : ?>
                    <?php if (($document_entries[$k]['status_type'] == 'approved' && $document_entries[$k]['state_type'] != 'archived' && $logged_userusertype == 'admin' && $document_entries[$k]['level'] != $max_level) || ($document_entries[$k]['level'] == ((int)$logged_userposlevel - 1) && $document_entries[$k]['status_type'] == 'approved' && $document_entries[$k]['state_type'] != 'archived' && $logged_userusertype == 'user' && $document_entries[$k]['termcode'] == $logged_usertermcode) || ($document_entries[$k]['state_type'] != 'archived' && $document_entries[$k]['status_type'] == 'approval' && $logged_userusertype == 'user' && $document_entries[$k]['termcode'] == $logged_usertermcode) || ($document_entries[$k]['state_type'] != 'archived' && $document_entries[$k]['status_type'] == 'approval' && $logged_userusertype == 'admin' && $document_entries[$k]['level'] != $max_level)) : ?>
                        <tr>
                            <td><?php echo $document_entries[$k]['id']; ?></td>
                            <td><?php echo $document_entries[$k]['title']; ?></td>
                            <td>
                                <?php if (in_array('policy-read', $logged_userpermissions)) : ?>
                                    <button class="btn btn-success btn-xs cust-btn-filename" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo $document_entries[$k]['filename']; ?></button>
                                <?php else: ?>
                                    <?php echo $document_entries[$k]['filename']; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $document_entries[$k]['date_uploaded']; ?></td>
                            <td><?php echo $document_entries[$k]['status_type']; ?></td>
                            <td>
                                <button class="btn btn-success btn-xs cust-btn-view" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-details" data-toggle="tooltip" data-placement="top" title="info"><i class="mdi mdi-information-outline"></i></button>
                                <?php if (in_array('policy-dld', $logged_userpermissions)) : ?>
                                    <button class="btn btn-success btn-xs cust-btn-dld" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="download"><i class="mdi mdi-arrow-down-bold"></i></button>
                                <?php endif; ?>
                                <button class="btn btn-success btn-xs cust-btn-approve" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="approve"><i class="mdi mdi-thumb-up"></i></button>
                                <button class="btn btn-success btn-xs cust-btn-reject" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="reject"><i class="mdi mdi-thumb-down"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <script>
            $('#table-revision').DataTable({
                dom: 'Bfrtip',
                buttons: []
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
        </script>
    <?php endif; ?>
    <?php
    endif;

    if ($action == 'edit-rev') : 

        $revision_data = $revision->getRevByID($rid);
        $did = ($revision_data) ? $revision_data[0]['doc_id'] : '';
        
        $document_data = $document->getDocByID($did);

        $id = ($revision_data) ? $document_data[0]['id'] : '';
        $title = ($revision_data) ? $document_data[0]['title'] : '';
        $filename = ($revision_data) ? $document_data[0]['filename'] : '';
    ?>
    <!-- modal code here -->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            <h5 id="modal-rev-label">Upload Revision</h5>
            <h6>Original File Info:</h6>
            <ul>
                <li><b>ID: </b><?php echo $id; ?></li>
                <li><b>Title: </b><?php echo $title; ?></li>
                <li><b>Filename: </b><?php echo $filename; ?></li>
            </ul>
            <form>
                <div class="row">
                    <input type="hidden" class="form-control cust-input-field" id="input-rid">

                    <div class="form-group col-lg-6">
                        <input type="text" class="form-control cust-input-field" id="input-rev-title" placeholder="Title *" required>
                    </div>
                    <div class="form-group col-lg-6">
                        <input type="text" class="form-control cust-input-field" id="input-rev-tag" placeholder="Tags (comma separated)" data-role="tagsinput" required>
                    </div>
                    <div class="form-group col-lg-6">
                        <textarea type="text" class="form-control cust-input-field" id="input-rev-description" placeholder="Description *" required></textarea>
                    </div>
                    <div class="form-group col-lg-6">
                        <textarea type="text" class="form-control cust-input-field" id="input-rev-remarks" placeholder="Remarks" required></textarea>
                    </div>
                    <div class="form-group col-lg-12 import-rev-wrapper">
                        <div class="custom-file">
                            <input type="file" name="input-import-rev" class="form-control-file" id="input-import-rev" accept=".pdf">
                            <label class="custom-file-label" for="input-import-rev">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group col-lg-11">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" value="announce" class="custom-control-input" id="chk-announce-rev">
                            <label class="custom-control-label" for="chk-announce-rev">Pin on the Bulletin Board.</label>
                        </div>
                    </div>
                    <div class="form-group col-lg-1">
                        <input type="file" name="input-import-attachment-rev" id="input-import-attachment-rev" accept=".pdf">
                        <label for="input-import-attachment-rev"><i class="mdi mdi-clipboard-text attach-icon"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <button id="btn-add-rev" class="btn btn-info cust-btn-info" data-action="add">Upload</button>
                    <button id="btn-clear-rev" class="btn btn-danger cust-btn-danger" data-toggle="modal" data-target="#modal-doc-revision">Cancel</button>
                </div>

                <div class="upload-action-wrapper"></div>
            </form>
        </div>
    </div>
    <?php
    endif;

    if ($action == 'open-rev') : 

        $revision_data = $revision->getRevByID($rid);

        $id = $revision_data[0]['id'];
        $title = $revision_data[0]['title'];
        $type = $revision_data[0]['type'];
        $description = $revision_data[0]['description'];
        $tags = $revision_data[0]['tags'];
        $filename = $revision_data[0]['filename'];
        $date_uploaded = $revision_data[0]['date_uploaded'];

        $path = 'uploads/documents/'.$filename;

    ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h6>Document Preview</h6>
                        <ul>
                            <li><b>Title: </b><?php echo $title; ?></li>
                            <li><b>Filename: </b><?php echo $filename; ?></li>
                            <li><b>Date Uploaded: </b><?php echo $date_uploaded; ?></li>
                        </ul>
                    </div>
                    <div class="col-lg-6 text-right">
                        <button class="btn btn-success btn-xs cust-btn-filename"><a href="<?php echo $path; ?>" target="_blank" ><i class="mdi mdi-fullscreen"></i> Fullscreen</a></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <embed src="<?php echo $path; ?>" width="100%" height="700px"/>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endif;

    if ($action == 'open-rev-attachment') : 

        $document_data = $revision->getRevByID($rid);

        $id = $document_data[0]['id'];
        $title = $document_data[0]['title'];
        $type = $document_data[0]['type'];
        $description = $document_data[0]['description'];
        $tags = $document_data[0]['tags'];
        $date_uploaded = $document_data[0]['date_uploaded'];

        $status = $document_data[0]['status'];
        $status = decode($status);
        $filename = (!empty($status)) ? $status['attach'] : '';

        $path = 'uploads/attachments/'.$filename;
    ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <h6>Attachment Preview</h6>
                        <ul>
                            <li><b>Attachment for: </b><?php echo $title; ?></li>
                            <li><b>Filename: </b><?php echo $filename; ?></li>
                            <li><b>Date Uploaded: </b><?php echo $date_uploaded; ?></li>
                        </ul>
                    </div>
                    <div class="col-lg-6 text-right">
                        <button class="btn btn-success btn-xs cust-btn-filename"><a href="<?php echo $path; ?>" target="_blank" ><i class="mdi mdi-fullscreen"></i> Fullscreen</a></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <embed src="<?php echo $path; ?>" width="100%" height="700px"/>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endif;

    if ($action == 'rev-details') : 

        $revision_data = $revision->getRevByID($rid);
        $author_data = $user->getUserByID($revision_data[0]['author']);

        $author_name = (!empty($author_data)) ? $author_data[0]['fullname'] : '';
        $author_pos = (!empty($author_data)) ? $author_data[0]['position'] : '';

        $id = $revision_data[0]['id'];
        $title = $revision_data[0]['title'];
        $type = $revision_data[0]['type'];
        $description = $revision_data[0]['description'];
        $tags = $revision_data[0]['tags'];
        $doc_id = $revision_data[0]['doc_id'];
        $filename = $revision_data[0]['filename'];
        $date_uploaded = $revision_data[0]['date_uploaded'];
        $version = $revision_data[0]['version'];
        $author = $author_name;
        $size = $revision_data[0]['size'];
        $remarks = $revision_data[0]['remarks'];
        $level = $revision_data[0]['level'];
        $status = $revision_data[0]['status'];
        $state = $revision_data[0]['state'];

        $status = decode($status);
        $state = decode($state);

        $user_data_stat = $user->getUserByID($status['user_id']);
        $approver = $status['user_id'];
        $fullname_stat = (!empty($user_data_stat)) ? $user_data_stat[0]['fullname'] : '';

        $posid_stat = (!empty($user_data_stat)) ? $user_data_stat[0]['position'] : '';
        $position_data_stat = $position->getPosByID($posid_stat);
        $position_name_stat = (!empty($position_data_stat)) ? $position_data_stat[0]['name'] : '';

        $user_data_ste = $user->getUserByID($state['user_id']);
        $publisher = $state['user_id'];
        $fullname_ste = (!empty($user_data_ste)) ? $user_data_ste[0]['fullname'] : '';

        $posid_ste = (!empty($user_data_ste)) ? $user_data_ste[0]['position'] : '';
        $position_data_ste = $position->getPosByID($posid_ste);
        $position_name_ste = (!empty($position_data_ste)) ? $position_data_ste[0]['name'] : '';
    ?>
        <!-- html code -->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h6>Revision Info</h6>
                <ul>
                    <li><b>Title: </b><?php echo $title; ?></li>
                    <li><b>Type: </b><?php echo $type; ?></li>
                    <li><b>Description: </b><?php echo $description; ?></li>
                    <li><b>Tags: </b><?php echo $tags; ?></li>
                    <li><b>Original File ID: </b><?php echo $doc_id; ?></li>
                    <li><b>Filename: </b><?php echo $filename; ?></li>
                    <li><b>Date Uploaded: </b><?php echo $date_uploaded; ?></li>
                    <li><b>Version: </b><?php echo $version; ?></li>
                    <li><b>Author: </b><?php echo $author; ?></li>
                    <li><b>Size: </b><?php echo $size; ?></li>
                    <li><b>Level: </b><?php echo $level; ?></li>
                    <li><b>Remarks: </b><?php echo $remarks; ?></li>
                </ul>
                <div class="row">
                    <div class="col-lg-6">
                        <h6>Status</h6>
                        <ul>
                            <li><b>Type: </b><?php echo ($status['type'] == 'approval') ? 'for approval' : $status['type']; ?></li>
                            <li><b>Date: </b><?php echo $status['date']; ?></li>
                            <li><b>User: </b><?php echo $fullname_stat.', '.$position_name_stat; ?></li>
                            <li><b>Remarks: </b><?php echo $status['rmk']; ?></li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <h6>State</h6>
                        <ul>
                            <li><b>Type: </b><?php echo $state['type']; ?></li>
                            <li><b>Date: </b><?php echo $state['date']; ?></li>
                            <li><b>User: </b><?php echo $fullname_ste.', '.$position_name_ste; ?></li>
                            <li><b>Remarks: </b><?php echo $state['rmk']; ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endif;

endif;
?>
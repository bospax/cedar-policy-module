<?php

require_once '../core/init_ajax.php';

$action = '';
$did = '';
$title = '';
$tags = '';
$description = '';
$remarks = '';
$rmk = '';
$attach = '';
$search = '';
$filter = '';
$announce = 0;
$filename_attachment = '';

$type = '';
$filename = '';
$date_uploaded = '';
$revision = '';
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
$rev = new Revision();
$parser = new \Smalot\PdfParser\Parser();
$pdfparser = new PDF2Text();

if (isset($_POST['action'])) :

    $action = sanitize($_POST['action']);
    $did = (isset($_POST['did'])) ? sanitize($_POST['did']) : '';
    $title = (isset($_POST['title'])) ? sanitize($_POST['title']) : '';
    $tags = (isset($_POST['tags'])) ? sanitize($_POST['tags']) : '';
    $description = (isset($_POST['description'])) ? sanitize($_POST['description']) : '';
    $remarks = (isset($_POST['remarks'])) ? sanitize($_POST['remarks']) : '';
    $rmk = (isset($_POST['rmk'])) ? sanitize($_POST['rmk']) : '';
    $attach = (isset($_POST['attach'])) ? sanitize($_POST['attach']) : '';
    $search = (isset($_POST['search'])) ? sanitize($_POST['search']) : '';
    $filter = (isset($_POST['filter'])) ? sanitize($_POST['filter']) : '';
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

            if ($action == 'add' || $action == 'edit') {
    
                $documents = $document->getAllDocument();
                $upload = (!empty($_FILES['file'])) ? $_FILES['file']['name'] : '';

                if (!empty($documents)) {
                    
                    foreach ($documents as $k => $v) {
                        $fetched_title = $documents[$k]['title'];
                        $filename = $documents[$k]['filename'];
                        $filename = explode('-', $filename);
                        $doc_id = $documents[$k]['id'];

                        if (($upload == $filename[1] && $action == 'add') || ($fetched_title == $title && $action == 'add')) {
                            $duplicate = 1;
                        }

                        if (($upload == $filename[1] && $doc_id != $did && $action == 'edit') || ($fetched_title == $title && $doc_id != $did && $action == 'edit')) {
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
        if (preg_match('/[^a-zA-Z0-9,.!?()_ -]/', $rmk)) {
            $errors[] = 'Special characters are not allowed.';
        }
    }

    if ($action == 'approve') {
        if (preg_match('/[^a-zA-Z0-9,.!?()_ -]/', $rmk)) {
            $errors[] = 'Special characters are not allowed.';
        }
    }

    if ($action == 'reject') {
        if (preg_match('/[^a-zA-Z0-9,.!?()_ -]/', $rmk)) {
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

            // add code
            $max_level = $position->getMaxLevel();
            $max_level = $max_level[0]['max_level'];

            // $type = ($level == $max_level) ? 'approved' : 'approval';

            // all file uploaded will be in max level and approved
            $level = $max_level;
            $type = 'approved';

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

            $addDoc = $document->addDoc($title, $extension, $description, $tags, $filename, $cdate, $revision, $author, $size, $remarks, $level, $status, $state, $content);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully uploaded.';

            echo encode($response);

        } elseif ($action == 'edit') {

            // edit code
            if (!empty($_FILES['file'])) {

                $document_data = $document->getDocByID($did);
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

                $updateDoc = $document->updateDocAndFile($title, $extension, $description, $tags, $filename, $size, $remarks, $did, $content);
            } 

            if (!empty($_FILES['file_attachment'])) {

                $document_data = $document->getDocByID($did);

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

                $updateDoc = $document->updateDocAndFileAttachment($title, $description, $tags, $remarks, $status, $did);
            }
            
            if (empty($_FILES)) {

                $extension = $announce;

                $updateDoc = $document->updateDoc($title, $extension, $description, $tags, $remarks, $did);
            }

            $response['type'] = 'success';
            $response['msg'] = 'Data successfully updated.';

            echo encode($response);

        } elseif ($action == 'delete') {

            // delete code
            $document_data = $document->getDocByID($did);
            
            if (!empty($document_data)) {

                $state = $document_data[0]['state'];
                $state = decode($state);

                $state['type'] = 'archived';
                $state['user_id'] = (int)$user_id;
                $state['date'] = $cdate;
                $state['rmk'] = $rmk;

                $state = encode($state);
            }

            $deleteDoc = $document->updateState($state, $did);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully archived.';

            echo encode($response);

        } elseif ($action == 'approve') {

            // approve code
            $document_data = $document->getDocByID($did);
            
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

            $approveDoc = $document->updateStatus($status, $did);
            $levelUpDoc = $document->updateLevel($level, $did);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully approved.';

            echo encode($response);

        } elseif ($action == 'reject') { 

            // approve code
            $document_data = $document->getDocByID($did);
            
            if (!empty($document_data)) {

                $file = $_FILES['file']['tmp_name'];
                $filename = time().'-'.$_FILES['file']['name'];
                $size = $_FILES['file']['size'];
                $destination = '../uploads/attachments/'.$filename;
                $extension = pathinfo($filename, PATHINFO_EXTENSION);

                $status = $document_data[0]['status'];
                $status = decode($status);

                $status['type'] = 'rejected';
                $status['user_id'] = (int)$user_id;
                $status['date'] = $cdate;
                $status['rmk'] = $rmk;
                $status['attach'] = $filename;

                move_uploaded_file($file, $destination);

                $status = encode($status);
            }

            $rejectDoc = $document->updateStatus($status, $did);

            $response['type'] = 'success';
            $response['msg'] = 'File has been rejected.';

            echo encode($response);

        } elseif ($action == 'publish') {

            // delete code
            $document_data = $document->getDocByID($did);
            $revisions = $rev->getRevByDocID($did);
            
            if (!empty($document_data)) {

                $state = $document_data[0]['state'];
                $state = decode($state);

                $state['type'] = 'published';
                $state['user_id'] = (int)$user_id;
                $state['date'] = $cdate;
                $state['rmk'] = $rmk;

                $state = encode($state);
            }

            $publishDoc = $document->updateState($state, $did);

            if (!empty($revisions)) {

                foreach ($revisions as $k => $v) {

                    $state = decode($revisions[$k]['state']);

                    if ($state['type'] == 'published') {

                        $rid = $revisions[$k]['id'];

                        $state['type'] = 'unpublished';
                        $state['user_id'] = (int)$user_id;
                        $state['date'] = $cdate;
                        $state['rmk'] = $rmk;

                        $state = encode($state);

                        $updateState = $rev->updateState($state, $rid);
                    }
                }
            }

            $response['type'] = 'success';
            $response['msg'] = 'File successfully published.';

            echo encode($response);

        } elseif ($action == 'unpublish') {

            // delete code
            $document_data = $document->getDocByID($did);
            
            if (!empty($document_data)) {

                $state = $document_data[0]['state'];
                $state = decode($state);

                $state['type'] = 'unpublished';
                $state['user_id'] = (int)$user_id;
                $state['date'] = $cdate;
                $state['rmk'] = $rmk;

                $state = encode($state);
            }

            $publishDoc = $document->updateState($state, $did);

            $response['type'] = 'success';
            $response['msg'] = 'File successfully unpublished.';

            echo encode($response);
        }
    }

    if ($action == 'doccount') {

        $total = 0;

        $documents = $document->getAllDocument();
        $revisions = $rev->getAllRevision();

        if (!empty($documents)) {
            $doccount = count($documents);
            $revcount = count($revisions);

            $total = (int)$doccount + (int)$revcount;
        }

        echo $total;
    }

    if ($action == 'pubcount') {

        $total = 0;
        $doccount = 0;
        $revcount = 0;

        $documents = $document->getAllDocument();
        $revisions = $rev->getAllRevision();

        foreach ($documents as $k => $v) {
            $state = decode($documents[$k]['state']);

            if ($state['type'] == 'published') {
                $doccount += 1;
            }
        }

        foreach ($revisions as $k => $v) {
            $state = decode($revisions[$k]['state']);

            if ($state['type'] == 'published') {
                $revcount += 1;
            }
        }

        $total = (int)$doccount + (int)$revcount;

        echo $total;
    }

    if ($action == 'download') {
        
    }

    if ($action == 'read') :

        if ($logged_userusertype == 'admin') {
            $documents = $document->getAllDocument();
        } 

        if ($logged_userusertype == 'user') {
            $documents = $document->getDocumentByAuthor($user_id);
        }

        $document_entries = [];
        $document_entry = [];

        if (!empty($documents)) {
            foreach ($documents as $k => $v) {
                
                $max_level = $position->getMaxLevel();
                $max_level = $max_level[0]['max_level'];

                $document_entry['id'] = $documents[$k]['id'];
                $document_entry['title'] = $documents[$k]['title'];
                $document_entry['type'] = $documents[$k]['type'];
                $document_entry['description'] = $documents[$k]['description'];
                $document_entry['tags'] = $documents[$k]['tags'];
                $document_entry['filename'] = $documents[$k]['filename'];
                $document_entry['date_uploaded'] = $documents[$k]['date_uploaded'];
                $document_entry['revision'] = $documents[$k]['revision'];
                $document_entry['author'] = $documents[$k]['author'];
                $document_entry['size'] = $documents[$k]['size'];
                $document_entry['remarks'] = $documents[$k]['remarks'];
                $document_entry['level'] = $documents[$k]['level'];
                $document_entry['status'] = $documents[$k]['status'];
                $document_entry['state'] = $documents[$k]['state'];

                $state = decode($documents[$k]['state']);
                $status = decode($documents[$k]['status']);
                $state_type = $state['type'];
                $status_type = $status['type'];

                $document_entry['state_type'] = $state_type;
                $document_entry['status_type'] = $status_type;

                $revs = $rev->getRevByDocID($documents[$k]['id']);

                $published_revision = false;
                
                foreach ($revs as $k => $v) {
                    $state = $revs[$k]['state'];
                    $state = decode($state);

                    if ($state['type'] == 'published') {
                        $published_revision = true;
                    }
                }

                $document_entry['published_revision'] = $published_revision;

                $document_entries[] = $document_entry;
            }

            // var_dump($document_entries);
        }

    ?>
    <table id="table-document" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>File</th>
                <th>Status</th>
                <th>State</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($document_entries)) : ?>
            <?php foreach ($document_entries as $k => $v) : ?>
            <tr>
                <td><?php echo $document_entries[$k]['id']; ?></td>
                <td data-toggle="tooltip" data-placement="top" title="<?php echo $document_entries[$k]['title']; ?>"><?php echo cutTitle($document_entries[$k]['title']); ?></td>
                <td>
                    <?php if (in_array('policy-read', $logged_userpermissions)) : ?>
                        <button class="btn btn-success btn-xs cust-btn-filename" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo cutFilename($document_entries[$k]['filename']); ?></button>
                        <?php echo ($document_entries[$k]['published_revision']) ? '<i class="mdi mdi-information pub-rev-indicator" data-toggle="tooltip" data-placement="top" title="1 revision is currently published"></i>' : ''; ?>
                    <?php else: ?>
                        <?php echo cutFilename($document_entries[$k]['filename']); ?>
                    <?php endif; ?>
                </td>
                <td><?php 

                    $indicator = '';
                
                    if ($document_entries[$k]['status_type'] == 'approved' && $document_entries[$k]['level'] != $max_level) {
                        $indicator = ' <i class="mdi mdi-information partially-appr-indicator" data-toggle="tooltip" data-placement="top" title="still needs verification by the highest approver."></i>';
                    } elseif ($document_entries[$k]['status_type'] == 'approved' && $document_entries[$k]['level'] == $max_level) {
                        $indicator = ' <i class="mdi mdi-check-circle fully-appr-indicator" data-toggle="tooltip" data-placement="top" title="file is now ready to be published."></i>';
                    }

                    echo ($document_entries[$k]['status_type'] == 'approval') ? 'for approval' : $document_entries[$k]['status_type'].$indicator; 
                    
                    ?>
                </td>
                <td><?php 

                    $indicator_ann = '';

                    if ($document_entries[$k]['state_type'] == 'published' && $document_entries[$k]['type'] == 1) {
                        $indicator_ann = ' <i class="mdi mdi-pin pinned-indicator" data-toggle="tooltip" data-placement="top" title="currently pinned on the Bulletin Board."></i>';
                    }

                    echo $document_entries[$k]['state_type'].$indicator_ann; ?></td>
                <td>
                    <div class="btn-group btn-action-doc">
                        <button type="button" class="btn btn-success" data-toggle="dropdown">Action</button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item cust-btn-view" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-details">Info</a>
                            <a class="dropdown-item cust-btn-rev" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>">View Revisions</a>
                            <?php if ($document_entries[$k]['state_type'] != 'deleted') : ?>
                                
                                <?php if ((in_array('policy-edit', $logged_userpermissions) && $logged_userusertype == 'admin') || ((in_array('policy-edit', $logged_userpermissions) && $logged_userusertype == 'user' && $document_entries[$k]['author'] == $user_id))) : ?>
                                    <a class="dropdown-item cust-btn-edit" href="javascript:void(0)"
                                    data-id="<?php echo $document_entries[$k]['id']; ?>"
                                    data-title="<?php echo $document_entries[$k]['title']; ?>"
                                    data-type="<?php echo $document_entries[$k]['type']; ?>"
                                    data-tags="<?php echo $document_entries[$k]['tags']; ?>"
                                    data-descr="<?php echo $document_entries[$k]['description']; ?>"
                                    data-rmk="<?php echo $document_entries[$k]['remarks']; ?>"
                                    >Edit</a>
                                
                                <?php else: ?>

                                    <?php if (in_array('policy-upload', $logged_userpermissions)) : ?>
                                        <a class="dropdown-item cust-btn-upd" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-revision">Revise</a>
                                    <?php endif; ?>

                                <?php endif; ?>

                                <?php if (in_array('policy-upload', $logged_userpermissions) && $logged_userusertype == 'admin') : ?>
                                    <a class="dropdown-item cust-btn-upd" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-revision">Revise</a>
                                <?php endif; ?>
                                
                                <?php if (in_array('policy-dld', $logged_userpermissions)) : ?>
                                    <!-- <a class="dropdown-item cust-btn-dld" href="javascript:void(0)" data-id="<?php //echo $document_entries[$k]['id']; ?>">Download</a> -->
                                    <?php if ($document_entries[$k]['revision'] != '' || $document_entries[$k]['revision'] != 0) : ?>
                                        <a class="dropdown-item cust-btn-dldall" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>">Download Revisions</a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($document_entries[$k]['level'] == $max_level) : ?>
                                    <?php if (in_array('policy-pub', $logged_userpermissions) && $document_entries[$k]['state_type'] != 'published') : ?>
                                        <a class="dropdown-item cust-btn-pub" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>">Publish</a>
                                    <?php endif; ?>

                                    <?php if (in_array('policy-unpub', $logged_userpermissions) && $document_entries[$k]['state_type'] == 'published') : ?>
                                        <a class="dropdown-item cust-btn-unpub" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>">Unpublish</a>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (in_array('policy-delete', $logged_userpermissions)) : ?>
                                    <a class="dropdown-item cust-btn-del" href="javascript:void(0)" data-id="<?php echo $document_entries[$k]['id']; ?>">Archive</a>
                                <?php endif; ?>

                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

    <script>
        $('#table-document').DataTable({
            dom: 'Bfrtip',
            buttons: []
        });
        $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
    </script>
    <?php
    endif;

    if ($action == 'approval') :  

        if ($logged_userusertype == 'admin') {
            $documents = $document->getAllDocument();
            $revisions = $rev->getAllRevision();
        } 

        if ($logged_userusertype == 'user') {

            $level = ((int)$logged_userposlevel != 1) ? (int)$logged_userposlevel - 1 : 0;

            // if ($logged_userposlevel == '2') {
            //     $level = 1;
            // } elseif ($logged_userposlevel == '3') {
            //     $level = 2;
            // }

            $documents = $document->getDocumentByLevel($level);
            $revisions = $rev->getRevByLevel($level);
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
                $document_entry['revision'] = $documents[$k]['revision'];
                $document_entry['author'] = $documents[$k]['author'];
                $document_entry['size'] = $documents[$k]['size'];
                $document_entry['remarks'] = $documents[$k]['remarks'];
                $document_entry['level'] = $documents[$k]['level'];
                $document_entry['status'] = $documents[$k]['status'];
                $document_entry['state'] = $documents[$k]['state'];

                $document_entry['uploadtype'] = 'doc';

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

        if (!empty($revisions)) {
            foreach ($revisions as $k => $v) {     
                
                $user_data = $user->getUserByID($revisions[$k]['author']);
                $term_data = ($user_data) ? $terminal->getTerminalByID($user_data[0]['term_id']) : '';
                $termcode = ($term_data) ? $term_data[0]['termcode'] : '';

                $max_level = $position->getMaxLevel();
                $max_level = $max_level[0]['max_level'];

                $document_entry['id'] = $revisions[$k]['id'];
                $document_entry['doc_id'] = $revisions[$k]['doc_id'];
                $document_entry['title'] = $revisions[$k]['title'];
                $document_entry['type'] = $revisions[$k]['type'];
                $document_entry['description'] = $revisions[$k]['description'];
                $document_entry['tags'] = $revisions[$k]['tags'];
                $document_entry['filename'] = $revisions[$k]['filename'];
                $document_entry['date_uploaded'] = $revisions[$k]['date_uploaded'];
                $document_entry['version'] = $revisions[$k]['version'];
                $document_entry['author'] = $revisions[$k]['author'];
                $document_entry['size'] = $revisions[$k]['size'];
                $document_entry['remarks'] = $revisions[$k]['remarks'];
                $document_entry['level'] = $revisions[$k]['level'];
                $document_entry['status'] = $revisions[$k]['status'];
                $document_entry['state'] = $revisions[$k]['state'];

                $document_entry['uploadtype'] = 'rev';

                $document_entry['termcode'] = $termcode;

                $status = decode($revisions[$k]['status']);
                $state = decode($revisions[$k]['state']);
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
        <h6>For Approval:</h6>

        <table id="table-approval" class="display responsive nowrap table table-striped table-bordered" style="width: 100%;">
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
                            <td data-toggle="tooltip" data-placement="top" title="<?php echo $document_entries[$k]['title']; ?>"><?php echo cutTitle($document_entries[$k]['title']); ?></td>
                            <td>
                                <?php if (in_array('policy-read', $logged_userpermissions)) : ?>
                                    <button class="btn btn-success btn-xs cust-btn-filename" data-uploadtype="<?php echo $document_entries[$k]['uploadtype']; ?>" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo cutFilename($document_entries[$k]['filename']); ?></button>
                                <?php else: ?>
                                    <?php echo cutFilename($document_entries[$k]['filename']); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $document_entries[$k]['date_uploaded']; ?></td>
                            <td><?php echo ($document_entries[$k]['status_type'] == 'approval') ? 'for approval' : $document_entries[$k]['status_type']; ?></td>
                            <td>
                                <button class="btn btn-success btn-xs cust-btn-view" data-uploadtype="<?php echo $document_entries[$k]['uploadtype']; ?>" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-details" data-toggle="tooltip" data-placement="top" title="info"><i class="mdi mdi-information-outline"></i></button>
                                <?php if (in_array('policy-dld', $logged_userpermissions)) : ?>
                                    <!-- <button class="btn btn-success btn-xs cust-btn-dld" data-uploadtype="<?php //echo $document_entries[$k]['uploadtype']; ?>" data-id="<?php //echo $document_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="download"><i class="mdi mdi-arrow-down-bold"></i></button> -->
                                <?php endif; ?>
                                <button class="btn btn-success btn-xs cust-btn-approve" data-uploadtype="<?php echo $document_entries[$k]['uploadtype']; ?>" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="approve"><i class="mdi mdi-check-circle"></i></button>
                                <button class="btn btn-success btn-xs cust-btn-reject" data-uploadtype="<?php echo $document_entries[$k]['uploadtype']; ?>" data-id="<?php echo $document_entries[$k]['id']; ?>" data-toggle="tooltip" data-placement="top" title="reject"><i class="mdi mdi-close-circle"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>

        <script>
            $('#table-approval').DataTable({
                dom: 'Bfrtip',
                buttons: []
            });
            $('.buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel').addClass('cust-btn-dt');
        </script>
    <?php endif; ?>
    <?php
    endif;

    if ($action == 'doc-details') : 

        $document_data = $document->getDocByID($did);
        $author_data = $user->getUserByID($document_data[0]['author']);

        $author_name = (!empty($author_data)) ? $author_data[0]['fullname'] : '';
        $author_pos = (!empty($author_data)) ? $author_data[0]['position'] : '';

        $id = $document_data[0]['id'];
        $title = $document_data[0]['title'];
        $type = $document_data[0]['type'];
        $description = $document_data[0]['description'];
        $tags = $document_data[0]['tags'];
        $filename = $document_data[0]['filename'];
        $date_uploaded = $document_data[0]['date_uploaded'];
        $revision = $document_data[0]['revision'];
        $author = $author_name;
        $size = $document_data[0]['size'];
        $remarks = $document_data[0]['remarks'];
        $level = $document_data[0]['level'];
        $status = $document_data[0]['status'];
        $state = $document_data[0]['state'];

        $status = decode($status);
        $state = decode($state);

        if (isset($status['attach'])) {
            $attachment = $status['attach'];
        }

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
        <!-- modal code here -->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h6>Document Info</h6>
                <ul>
                    <li><b>Title: </b><?php echo $title; ?></li>
                    <li><b>Type: </b><?php echo $type; ?></li>
                    <li><b>Description: </b><?php echo $description; ?></li>
                    <li><b>Tags: </b><?php echo $tags; ?></li>
                    <li><b>Filename: </b><?php echo $filename; ?></li>
                    <li><b>Date Uploaded: </b><?php echo $date_uploaded; ?></li>
                    <li><b>Revision: </b><?php echo $revision; ?></li>
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

                            <?php if (isset($status['attach'])) : ?>
                                <li><b>Attachment: </b><button class="btn btn-success btn-xs cust-btn-attach" data-attach="<?php echo '../uploads/attachments/'.$attachment; ?>" data-toggle="tooltip" data-placement="top" title="download attachment"><i class="mdi mdi-clipboard-text"></i></button></li>
                            <?php endif; ?>
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

    if ($action == 'announcement') : 

        $search_results = [];
        $handle = [];

        $documents = $document->getAllDocument();
        $revisions = $rev->getAllRevision();

        if (!empty($documents)) {
            foreach ($documents as $k => $v) {

                $type = $documents[$k]['type'];
                $state = decode($documents[$k]['state']);

                if ($type == 1 && $state['type'] == 'published') {
        
                    $doc_id = $documents[$k]['id'];
                    $title = $documents[$k]['title'];
                    $filename = $documents[$k]['filename'];
                    $date_uploaded = $documents[$k]['date_uploaded'];
                    $author = $documents[$k]['author'];
                    $content = $documents[$k]['content'];

                    $excerpt = substr($content, 0, 300).'..';

                    $author_data = $user->getUserByID($author);
                    $author_name = (!empty($author_data)) ? $author_data[0]['fullname'] : '';

                    $handle['id'] = $doc_id;
                    $handle['title'] = $title;
                    $handle['filename'] = $filename;
                    $handle['date_uploaded'] = $date_uploaded;
                    $handle['author_name'] = $author_name;
                    $handle['excerpt'] = $excerpt;
                    $handle['uploadtype'] = 'doc';

                    $search_results[] = $handle;
                }
            }
        }

        if (!empty($revisions)) {
            foreach ($revisions as $k => $v) {

                $type = $revisions[$k]['type'];
                $state = decode($revisions[$k]['state']);

                if ($type == 1 && $state['type'] == 'published') {
        
                    $rev_id = $revisions[$k]['id'];
                    $title = $revisions[$k]['title'];
                    $filename = $revisions[$k]['filename'];
                    $date_uploaded = $revisions[$k]['date_uploaded'];
                    $author = $revisions[$k]['author'];
                    $content = $revisions[$k]['content'];

                    $excerpt = substr($content, 0, 300).'..';

                    $author_data = $user->getUserByID($author);
                    $author_name = (!empty($author_data)) ? $author_data[0]['fullname'] : '';

                    $handle['id'] = $rev_id;
                    $handle['title'] = $title;
                    $handle['filename'] = $filename;
                    $handle['date_uploaded'] = $date_uploaded;
                    $handle['author_name'] = $author_name;
                    $handle['excerpt'] = $excerpt;
                    $handle['uploadtype'] = 'rev';

                    $search_results[] = $handle;
                }
            }
        }
    ?>
    
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="announcement-indicator"><i class="mdi mdi-pin"></i> Bulletin Board</h5>
                    <ul id="cust-announcement" class="list-unstyled">
                        <?php if (!empty($search_results)) : ?>
                            <?php foreach($search_results as $k => $v) : ?>
                                <li class="media cust-announce-item">
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1"><?php echo $search_results[$k]['title']; ?></h5> 
                                        <p><?php echo $search_results[$k]['excerpt']; ?></p>
                                        <span><i class="mdi mdi-book-open-page-variant"></i> Read: <button class="btn btn-success btn-xs cust-btn-filename" data-uploadtype="<?php echo $search_results[$k]['uploadtype']; ?>" data-id="<?php echo $search_results[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo $search_results[$k]['filename']; ?></button></span>&nbsp;
                                        <span><i class="mdi mdi-calendar-check"></i> <?php echo $search_results[$k]['date_uploaded']; ?></span>&nbsp;
                                        <span><i class="mdi mdi-account"></i> Uploader: <?php echo $search_results[$k]['author_name']; ?></span>&nbsp;
                                    </div>
                                </li>
                            <?php endforeach; ?>

                        <?php else : ?>
                            <p>No posts yet.</p>
                        <?php endif; ?>
                    </ul>

                    <?php if (!empty($search_results) && count($search_results) > 3) : ?>
                        <div class="text-center">
                            <button id="loadmore-announce" class="btn btn-xs btn-primary">Load More</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function(){
                $(".cust-announce-item").slice(0, 3).show();
            });
        </script>
    
    <?php
    endif;

    if ($action == 'open-doc') : 

        $document_data = $document->getDocByID($did);

        $id = $document_data[0]['id'];
        $title = $document_data[0]['title'];
        $type = $document_data[0]['type'];
        $description = $document_data[0]['description'];
        $tags = $document_data[0]['tags'];
        $filename = $document_data[0]['filename'];
        $date_uploaded = $document_data[0]['date_uploaded'];

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

    if ($action == 'open-doc-attachment') : 

        $document_data = $document->getDocByID($did);

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

    if ($action == 'upload-rev') : 

        $document_data = $document->getDocByID($did);

        $id = $document_data[0]['id'];
        $title = $document_data[0]['title'];
        $type = $document_data[0]['type'];
        $description = $document_data[0]['description'];
        $tags = $document_data[0]['tags'];
        $filename = $document_data[0]['filename'];
        $date_uploaded = $document_data[0]['date_uploaded'];
        $revision = $document_data[0]['revision'];
        $author = $document_data[0]['author'];
        $size = $document_data[0]['size'];
        $remarks = $document_data[0]['remarks'];
        $level = $document_data[0]['level'];
        $status = $document_data[0]['status'];
        $state = $document_data[0]['state'];

        $status = decode($status);
        $state = decode($state);

    ?>
    <!-- modal code here -->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        </div>
        <div class="modal-body">
            <h5>Upload Revision</h5>
            <h6>Original File Info:</h6>
            <ul>
                <li><b>ID: </b><?php echo $id; ?></li>
                <li><b>Title: </b><?php echo $title; ?></li>
                <li><b>Filename: </b><?php echo $filename; ?></li>
            </ul>
            <form>
                <div class="row">
                    <input type="hidden" class="form-control cust-input-field" id="input-rid" value="<?php echo $id; ?>">

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
                    <div class="form-group col-lg-11 import-rev-wrapper">
                        <div class="custom-file">
                            <input type="file" name="input-import-rev" class="form-control-file" id="input-import-rev" accept=".pdf">
                            <label class="custom-file-label" for="input-import-rev">Choose file</label>
                        </div>
                    </div>
                    <div class="form-group col-lg-1">
                        <input type="file" name="input-import-attachment-rev" id="input-import-attachment-rev" accept=".pdf">
                        <label for="input-import-attachment-rev"><i class="mdi mdi-clipboard-text attach-icon"></i></label>
                    </div>
                    <div class="form-group col-lg-12">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" value="announce" class="custom-control-input" id="chk-announce-rev">
                            <label class="custom-control-label" for="chk-announce-rev">Pin on the Bulletin Board.</label>
                        </div>
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

    if ($action == 'search-doc') : 

        $search_keyword = $search;
        $discard = ['is', 'the', 'with', 'in', 'of'];
        $search_results = [];
        $handle = [];
        $filters = [];

        $documents = $document->getAllDocument();
        $revisions = $rev->getAllRevision();

        if (!empty($documents)) {
            foreach ($documents as $k => $v) {
                
                $r = [];
                $state = decode($documents[$k]['state']);

                if ($state['type'] == 'published') {
        
                    $doc_id = $documents[$k]['id'];
                    $title = $documents[$k]['title'];
                    $filename = $documents[$k]['filename'];
                    $date_uploaded = $documents[$k]['date_uploaded'];
                    $author = $documents[$k]['author'];
                    $content = $documents[$k]['content'];

                    $status = $documents[$k]['status'];
                    $status = decode($status);
                    $attachment = $status['attach'];

                    $tags = $documents[$k]['tags'];
                    $tags = explode(',', $tags);

                    foreach ($tags as $k => $v) {
                        $tags[$k] = trim($tags[$k]);
                    }

                    $author_data = $user->getUserByID($author);
                    $author_name = (!empty($author_data)) ? $author_data[0]['fullname'] : '';
                    
                    $path = '../uploads/documents/'.$filename;

                    // $pdfparser->setFilename($path); 
                    // $pdfparser->decodePDF();

                    // $content = $pdfparser->output();

                    // $pdf = $parser->parseFile($path);

                    // $content = $pdf->getText();

                    $search_keyword = preg_replace("/[^a-zA-Z0-9\s]/", "", $search_keyword);

                    $keywords = explode(' ', $search_keyword);

                    for ($i=0; $i < count($keywords); $i++) {

                        if (in_array($keywords[$i], $discard)) {
                            $keywords[$i] = '';
                        }

                        if ($keywords[$i]) {
                            $match = stripos($content, $keywords[$i]);
                            $match_title = stripos($title, $keywords[$i]);
                            
                            if ($match) {
                                $r[] = $match;
                            }

                            if ($match_title) {
                                $r[] = $match;
                            }
                            
                            if (in_array($keywords[$i], $tags)) {
                                $r[] = $match;
                            }
                        }
                    }

                    $start = (!empty($r)) ? $r[0] : 0;
                    $excerpt = substr($content, $start, 400).'..';
                    $matches = count($r);

                    if ($r) {

                        $handle['id'] = $doc_id;
                        $handle['title'] = $title;
                        $handle['filename'] = $filename;
                        $handle['date_uploaded'] = $date_uploaded;
                        $handle['author_name'] = $author_name;
                        $handle['excerpt'] = $excerpt;
                        $handle['uploadtype'] = 'doc';
                        $handle['matches'] = $matches;
                        $handle['attachment'] = $attachment;

                        foreach ($tags as $value) {
                            if (!in_array($value, $filters)) {
                                $filters[] = $value;
                            }
                        }

                        if (!empty($filter) && in_array($filter, $tags)) {

                            $search_results[] = $handle;

                        } elseif (empty($filter)) {
                            
                            $search_results[] = $handle;
                        }
                    }
                }
            }
        }

        if (!empty($revisions)) {
            foreach ($revisions as $k => $v) {
                
                $r = [];
                $state = decode($revisions[$k]['state']);

                if ($state['type'] == 'published') {
        
                    $rev_id = $revisions[$k]['id'];
                    $title = $revisions[$k]['title'];
                    $filename = $revisions[$k]['filename'];
                    $date_uploaded = $revisions[$k]['date_uploaded'];
                    $author = $revisions[$k]['author'];
                    $content = $revisions[$k]['content'];

                    $status = $revisions[$k]['status'];
                    $status = decode($status);
                    $attachment = $status['attach'];

                    $author_data = $user->getUserByID($author);
                    $author_name = (!empty($author_data)) ? $author_data[0]['fullname'] : '';

                    $tags = $revisions[$k]['tags'];
                    $tags = explode(',', $tags);

                    foreach ($tags as $k => $v) {
                        $tags[$k] = trim($tags[$k]);
                    }
                    
                    $path = '../uploads/documents/'.$filename;

                    // $pdfparser->setFilename($path); 
                    // $pdfparser->decodePDF();

                    // $content = $pdfparser->output();

                    // $pdf = $parser->parseFile($path);

                    // $content = $pdf->getText();

                    $search_keyword = preg_replace("/[^a-zA-Z0-9\s]/", "", $search_keyword);

                    $keywords = explode(' ', $search_keyword);

                    for ($i=0; $i < count($keywords); $i++) {

                        if (in_array($keywords[$i], $discard)) {
                            $keywords[$i] = '';
                        }

                        if ($keywords[$i]) {
                            $match = stripos($content, $keywords[$i]);
                            $match_title = stripos($title, $keywords[$i]);
                            
                            if ($match) {
                                $r[] = $match;
                            }

                            if ($match_title) {
                                $r[] = $match;
                            }
                            
                            if (in_array($keywords[$i], $tags)) {
                                $r[] = $match;
                            }
                        }
                    }

                    $start = (!empty($r)) ? $r[0] : 0;
                    $excerpt = substr($content, $start, 400).'..';
                    $matches = count($r);

                    if ($r) {

                        $handle['id'] = $rev_id;
                        $handle['title'] = $title;
                        $handle['filename'] = $filename;
                        $handle['date_uploaded'] = $date_uploaded;
                        $handle['author_name'] = $author_name;
                        $handle['excerpt'] = $excerpt;
                        $handle['uploadtype'] = 'rev';
                        $handle['matches'] = $matches;
                        $handle['attachment'] = $attachment;

                        foreach ($tags as $value) {
                            if (!in_array($value, $filters)) {
                                $filters[] = $value;
                            }
                        }

                        if (!empty($filter) && in_array($filter, $tags)) {

                            $search_results[] = $handle;

                        } elseif (empty($filter)) {
                            
                            $search_results[] = $handle;
                        }
                    }
                }
            }
        }

        $matches = array_column($search_results, 'matches');

        array_multisort($matches, SORT_DESC, $search_results); 

        // var_dump($filters);
    ?>
    <div class="filtersearch form-group col-lg-4">
        <select name="combo-filtersearch" id="combo-filtersearch" class="select2 form-control custom-select col-12 cust-input-field">
            <option value="null">Filter</option>
            <?php if (!empty($filters)) : ?>
                <?php foreach($filters as $v) : ?>
                    <option value="<?php echo $v; ?>" <?php echo (!empty($filter) && $filter == $v) ? 'selected' : ''; ?>><?php echo (!empty($filter) && $filter == $v) ? 'Filtered by: '.$v : $v; ?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <ul id="cust-results" class="list-unstyled">
        <?php if (!empty($search_results)) : ?>
            <?php foreach($search_results as $k => $v) : ?>
                <li class="media cust-result-item">
                    <div class="media-body">
                        <h5 class="mt-0 mb-1"><?php echo $search_results[$k]['title']; ?></h5> 
                        <p><?php echo $search_results[$k]['excerpt']; ?></p>
                        <span><i class="mdi mdi-book-open-page-variant"></i> Read: <button class="btn btn-success btn-xs cust-btn-filename" data-uploadtype="<?php echo $search_results[$k]['uploadtype']; ?>" data-id="<?php echo $search_results[$k]['id']; ?>" data-toggle="modal" data-target="#modal-doc-preview"><?php echo $search_results[$k]['filename']; ?></button></span>&nbsp;
                        
                        <?php if (!empty($search_results[$k]['attachment'])) : ?>
                            <span class="cust-btn-attachment" data-uploadtype="<?php echo $search_results[$k]['uploadtype']; ?>" data-id="<?php echo $search_results[$k]['id']; ?>" data-toggle="modal" data-target="#modal-attach-preview"><i class="mdi mdi-clipboard-text"></i>Attachment </span> &nbsp;
                        <?php endif; ?>

                        <span><i class="mdi mdi-calendar-check"></i> <?php echo $search_results[$k]['date_uploaded']; ?></span>&nbsp;
                        <span><i class="mdi mdi-account"></i><?php echo $search_results[$k]['author_name']; ?></span>&nbsp;
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <?php if (!empty($search_results) && count($search_results) > 4) : ?>
        <div class="text-center">
            <button id="loadmore" class="btn btn-xs btn-primary">Load More</button>
        </div>
    <?php endif; ?>

    <?php if (empty($search_results)) : ?>
        <p>Nothing found. Try searching again.</p>
    <?php endif; ?>

    <script>
        $(document).ready(function(){
            $(".cust-result-item").slice(0, 4).show();
        });
    </script>

    <?php
    endif;

endif;
?>
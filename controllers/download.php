<?php
require_once '../core/init_ajax.php';

$did = '';
$filename = '';
$files = [];
$document = new Document();
$revision = new Revision();

if (isset($_GET['did'])) {

    $did = (isset($_GET['did'])) ? sanitize($_GET['did']) : '';
    
    if ($_GET['type'] == 'all') {

        $revisions = $revision->getRevByDocID($did);
        $document_data = $document->getDocByID($did);

        $doc_title = $document_data[0]['title'];

        foreach ($revisions as $k => $v) {
            $files[] = '../uploads/documents/'.$revisions[$k]['filename'];
        }

        $zipname = time().'-'.$doc_title.'_revisions.zip';
        $zip = new ZipArchive;

        $zip->open($zipname, ZipArchive::CREATE);

        foreach ($files as $file) {
            $zip->addFile($file);
        }

        $zip->close();

        header("Content-type: application/zip"); 
        header("Content-Disposition: attachment; filename=$zipname");
        header("Content-length: ".filesize($zipname));
        header("Pragma: no-cache"); 
        header("Expires: 0");

        readfile($zipname);
        unlink($zipname);

        // var_dump($files);

    } else {

        if ($_GET['type'] == 'doc') {

            $document_data = $document->getDocByID($did);
            
        } elseif ($_GET['type'] == 'rev') {
    
            $document_data = $revision->getRevByID($did);
        }
    
        $filename = (!empty($document_data)) ? $document_data[0]['filename'] : '';
        $filepath = '../uploads/documents/'.$filename;
    
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($filepath));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($filepath));
            readfile($filepath);
            exit;
        }
    }

} elseif (isset($_GET['attach'])) {

    $filepath = $_GET['attach'];

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: '.filesize($filepath));

        readfile($filepath);
        exit;
    }

} else {

    exit;
}
?>
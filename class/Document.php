<?php 
class Document {
    private $db_handle;
    
    function __construct() {
        $this->db_handle = new DBController();
    }

    function getAllDocument() {
        $qAllDoc = "SELECT * FROM `documents` ORDER BY `date_uploaded` DESC";
        $rAllDoc = $this->db_handle->runBaseQuery($qAllDoc);

        return $rAllDoc;
    }

    function getDocumentByLevel($level) {
        $qDocLevel = "SELECT * FROM `documents` WHERE `level` = :lvl";

        $pDocLevel = [
            'lvl' => $level
        ];

        $rDocLevel = $this->db_handle->runBaseQuery($qDocLevel, $pDocLevel);

        return $rDocLevel;
    }

    function getDocumentByAuthor($author) {
        $qDocAuthor = "SELECT * FROM `documents` WHERE `author` = :author";

        $pDocAuthor = [
            'author' => $author
        ];

        $rDocAuthor = $this->db_handle->runBaseQuery($qDocAuthor, $pDocAuthor);

        return $rDocAuthor;
    }

    function addDoc($title, $type, $description, $tags, $filename, $date_uploaded, $revision, $author, $size, $remarks, $level, $status, $state, $content) {
        $qAddDoc = "INSERT INTO `documents` 
        (`title`,`type`,`description`,`tags`,`filename`,`date_uploaded`,`revision`,`author`,`size`,`remarks`,`level`,`status`,`state`,`content`) 
        VALUES (:title, :typ, :descr, :tags, :filen, :date_uploaded, :rev, :author, :size, :remarks, :lvl, :stat, :ste, :content)";
        
        $pAddDoc = [
            'title' => $title,
            'typ' => $type,
            'descr' => $description,
            'tags' => $tags,
            'filen' => $filename,
            'date_uploaded' => $date_uploaded,
            'rev' => $revision,
            'author' => $author,
            'size' => $size,
            'remarks' => $remarks,
            'lvl' => $level,
            'stat' => $status,
            'ste' => $state,
            'content' => $content
        ];

        $rAddDoc = $this->db_handle->insert($qAddDoc, $pAddDoc);
        return $rAddDoc;
    }

    function updateDoc($title, $type, $description, $tags, $remarks, $did) {
        $qUpdDoc = "UPDATE `documents` SET 
        `title` = :title,
        `type` = :typ,
        `description` = :descr,
        `tags` = :tags,
        `remarks` = :rmk
        WHERE `id` = :did";

        $pUpdDoc = [
            'title' => $title,
            'typ' => $type,
            'descr' => $description,
            'tags' => $tags,
            'rmk' => $remarks,
            'did' => $did
        ];

        $rUpdDoc = $this->db_handle->update($qUpdDoc, $pUpdDoc);
        return $rUpdDoc;
    }

    function updateDocAndFile($title, $type, $description, $tags, $filename, $size, $remarks, $did, $content) {
        $qUpdDoc = "UPDATE `documents` SET 
        `title` = :title,
        `type` = :typ,
        `description` = :descr,
        `tags` = :tags,
        `filename` = :filen,
        `size` = :size,
        `remarks` = :rmk,
        `content` = :content
        WHERE `id` = :did";

        $pUpdDoc = [
            'title' => $title,
            'typ' => $type,
            'descr' => $description,
            'tags' => $tags,
            'filen' => $filename,
            'size' => $size,
            'rmk' => $remarks,
            'did' => $did,
            'content' => $content
        ];

        $rUpdDoc = $this->db_handle->update($qUpdDoc, $pUpdDoc);
        return $rUpdDoc;
    }

    function updateDocAndFileAttachment($title, $description, $tags, $remarks, $status, $did) {
        $qUpdDocAttach = "UPDATE `documents` SET 
        `title` = :title,
        `description` = :descr,
        `tags` = :tags,
        `remarks` = :rmk,
        `status` = :stat
        WHERE `id` = :did";

        $pUpdDocAttach = [
            'title' => $title,
            'descr' => $description,
            'tags' => $tags,
            'rmk' => $remarks,
            'stat' => $status,
            'did' => $did
        ];

        $rUpdDocAttach = $this->db_handle->update($qUpdDocAttach, $pUpdDocAttach);
        return $rUpdDocAttach;
    }

    function getDocByID($did) {
        $qDocID = "SELECT * FROM `documents` WHERE `id` = :did";

        $pDocID = [
            'did' => $did
        ];

        $rDocID = $this->db_handle->runBaseQuery($qDocID, $pDocID);

        return $rDocID;
    }

    function updateRev($doc_rev, $did) {
        $qRevDoc = "UPDATE `documents` SET 
        `revision` = :rev
        WHERE `id` = :did";

        $pRevDoc = [
            'did' => $did,
            'rev' => $doc_rev
        ];

        $rRevDoc = $this->db_handle->update($qRevDoc, $pRevDoc);
        return $rRevDoc;
    }

    function updateState($state, $did) {
        $qSteDoc = "UPDATE `documents` SET 
        `state` = :ste
        WHERE `id` = :did";

        $pSteDoc = [
            'did' => $did,
            'ste' => $state
        ];

        $rSteDoc = $this->db_handle->update($qSteDoc, $pSteDoc);
        return $rSteDoc;
    }

    function updateStatus($status, $did) {
        $qStatDoc = "UPDATE `documents` SET 
        `status` = :stat
        WHERE `id` = :did";

        $pStatDoc = [
            'did' => $did,
            'stat' => $status
        ];

        $rStatDoc = $this->db_handle->update($qStatDoc, $pStatDoc);
        return $rStatDoc;
    }

    function updateLevel($level, $did) {
        $qLvlDoc = "UPDATE `documents` SET 
        `level` = :lvl
        WHERE `id` = :did";

        $pLvlDoc = [
            'did' => $did,
            'lvl' => $level
        ];

        $rLvlDoc = $this->db_handle->update($qLvlDoc, $pLvlDoc);
        return $rLvlDoc;
    }
}
?>
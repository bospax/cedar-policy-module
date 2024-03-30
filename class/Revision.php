<?php 
class Revision {
    private $db_handle;
    
    function __construct() {
        $this->db_handle = new DBController();
    }

    function getAllRevision() {
        $qAllRev = "SELECT * FROM `revisions` ORDER BY `date_uploaded` DESC";
        $rAllRev = $this->db_handle->runBaseQuery($qAllRev);

        return $rAllRev;
    }

    function getRevByID($rid) {
        $qRevID = "SELECT * FROM `revisions` WHERE `id` = :rid";

        $pRevID = [
            'rid' => $rid
        ];

        $rRevID = $this->db_handle->runBaseQuery($qRevID, $pRevID);

        return $rRevID;
    }

    function getRevByDocID($doc_id) {
        $qRevDocID = "SELECT * FROM `revisions` WHERE `doc_id` = :docid";

        $pRevDocID = [
            'docid' => $doc_id
        ];

        $rRevDocID = $this->db_handle->runBaseQuery($qRevDocID, $pRevDocID);

        return $rRevDocID;
    }

    function getRevByLevel($level) {
        $qDocLevel = "SELECT * FROM `revisions` WHERE `level` = :lvl";

        $pDocLevel = [
            'lvl' => $level
        ];

        $rDocLevel = $this->db_handle->runBaseQuery($qDocLevel, $pDocLevel);

        return $rDocLevel;
    }

    function addRev($doc_id, $title, $type, $description, $tags, $filename, $date_uploaded, $version, $author, $size, $remarks, $level, $status, $state, $content) {
        $qAddRev = "INSERT INTO `revisions` 
        (`doc_id`,`title`,`type`,`description`,`tags`,`filename`,`date_uploaded`,`version`,`author`,`size`,`remarks`,`level`,`status`,`state`,`content`) 
        VALUES (:doc_id, :title, :typ, :descr, :tags, :filen, :date_uploaded, :ver, :author, :size, :remarks, :lvl, :stat, :ste, :content)";
        
        $pAddRev = [
            'doc_id' => $doc_id,
            'title' => $title,
            'typ' => $type,
            'descr' => $description,
            'tags' => $tags,
            'filen' => $filename,
            'date_uploaded' => $date_uploaded,
            'ver' => $version,
            'author' => $author,
            'size' => $size,
            'remarks' => $remarks,
            'lvl' => $level,
            'stat' => $status,
            'ste' => $state,
            'content' => $content
        ];

        $rAddRev = $this->db_handle->insert($qAddRev, $pAddRev);
        return $rAddRev;
    }

    function updateRev($title, $type, $description, $tags, $remarks, $rid) {
        $qUpdRev = "UPDATE `revisions` SET 
        `title` = :title,
        `type` = :typ,
        `description` = :descr,
        `tags` = :tags,
        `remarks` = :rmk
        WHERE `id` = :rid";

        $pUpdRev = [
            'title' => $title,
            'typ' => $type,
            'descr' => $description,
            'tags' => $tags,
            'rmk' => $remarks,
            'rid' => $rid
        ];

        $rUpdRev = $this->db_handle->update($qUpdRev, $pUpdRev);
        return $rUpdRev;
    }

    function updateRevAndFile($title, $type, $description, $tags, $filename, $size, $remarks, $rid, $content) {
        $qUpdDoc = "UPDATE `revisions` SET 
        `title` = :title,
        `type` = :typ,
        `description` = :descr,
        `tags` = :tags,
        `filename` = :filen,
        `size` = :size,
        `remarks` = :rmk,
        `content` = :content
        WHERE `id` = :rid";

        $pUpdDoc = [
            'title' => $title,
            'typ' => $type,
            'descr' => $description,
            'tags' => $tags,
            'filen' => $filename,
            'size' => $size,
            'rmk' => $remarks,
            'rid' => $rid,
            'content' => $content
        ];

        $rUpdDoc = $this->db_handle->update($qUpdDoc, $pUpdDoc);
        return $rUpdDoc;
    }

    function updateDocAndFileAttachment($title, $description, $tags, $remarks, $status, $rid) {
        $qUpdDocAttach = "UPDATE `revisions` SET 
        `title` = :title,
        `description` = :descr,
        `tags` = :tags,
        `remarks` = :rmk,
        `status` = :stat
        WHERE `id` = :rid";

        $pUpdDocAttach = [
            'title' => $title,
            'descr' => $description,
            'tags' => $tags,
            'rmk' => $remarks,
            'stat' => $status,
            'rid' => $rid
        ];

        $rUpdDocAttach = $this->db_handle->update($qUpdDocAttach, $pUpdDocAttach);
        return $rUpdDocAttach;
    }

    function updateState($state, $rid) {
        $qSteRev = "UPDATE `revisions` SET 
        `state` = :ste
        WHERE `id` = :rid";

        $pSteRev = [
            'rid' => $rid,
            'ste' => $state
        ];

        $rSteRev = $this->db_handle->update($qSteRev, $pSteRev);
        return $rSteRev;
    }

    function updateStatus($status, $rid) {
        $qStatDoc = "UPDATE `revisions` SET 
        `status` = :stat
        WHERE `id` = :rid";

        $pStatDoc = [
            'rid' => $rid,
            'stat' => $status
        ];

        $rStatDoc = $this->db_handle->update($qStatDoc, $pStatDoc);
        return $rStatDoc;
    }

    function updateLevel($level, $rid) {
        $qLvlDoc = "UPDATE `revisions` SET 
        `level` = :lvl
        WHERE `id` = :rid";

        $pLvlDoc = [
            'rid' => $rid,
            'lvl' => $level
        ];

        $rLvlDoc = $this->db_handle->update($qLvlDoc, $pLvlDoc);
        return $rLvlDoc;
    }
}
?>
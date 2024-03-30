<?php 
class Subunit {
    private $db_handle;
    
    function __construct() {
        $this->db_handle = new DBController();
    }

    function getAllSubunit() {
        $qAllSub = "SELECT * FROM `subunits`";
        $rAllSub = $this->db_handle->runBaseQuery($qAllSub);

        return $rAllSub;
    }

    function addSubunit($term_id, $subname) {

        $term_id = (int)$term_id;

        $qAddSub = "INSERT INTO `subunits` 
        (`term_id`, `subname`) 
        VALUES (:term_id, :subname)";
        
        $pAddSub = [
            'term_id' => $term_id,
            'subname' => $subname
        ];

        $rAddSub = $this->db_handle->insert($qAddSub, $pAddSub);
        return $rAddSub;
    }

    function updateSubunit($term_id, $subname, $suid) {
        
        $term_id = (int)$term_id;

        $qUpdSub = "UPDATE `subunits` SET 
        `term_id` = :term_id,
        `subname` = :subname
        WHERE `id` = :suid";

        $pUpdSub = [
            'term_id' => $term_id,
            'subname' => $subname,
            'suid' => $suid
        ];

        $rUpdSub = $this->db_handle->update($qUpdSub, $pUpdSub);
        return $rUpdSub;
    }

    function deleteSubunit($suid) {
        $qDelSub = "DELETE FROM `subunits` WHERE `id` = :suid";
        $pDelSub = [
            'suid' => $suid
        ];

        $rDelSub = $this->db_handle->delete($qDelSub, $pDelSub);
        return $rDelSub;
    }

    function checkDuplicateSubunit($term_id, $subname, $suid = '') {
        
        if (!empty($suid)) {
            
            $qTIDSubDup = "SELECT * FROM `subunits` WHERE `id` != :suid AND `term_id` = :term_id AND `subname` = :subname";

            $pTIDSubDup = [
                'suid' => $suid,
                'term_id' => $term_id,
                'subname' => $subname
            ];

        } else {

            $qTIDSubDup = "SELECT * FROM `subunits` WHERE `term_id` = :term_id AND `subname` = :subname";

            $pTIDSubDup = [
                'term_id' => $term_id,
                'subname' => $subname
            ];
        }

        $rTIDSubDup = $this->db_handle->runBaseQuery($qTIDSubDup, $pTIDSubDup);
        return $rTIDSubDup;
    }

    function getSubunitByTermID($term_id) {
        $qTIDSub = "SELECT * FROM `subunits` WHERE `term_id` = :term_id";

        $pTIDSub = [
            'term_id' => $term_id
        ];
        
        $rTIDSub = $this->db_handle->runBaseQuery($qTIDSub, $pTIDSub);
        return $rTIDSub;
    }

    function getSubunitByID($suid) {
        $qIDSub = "SELECT * FROM `subunits` WHERE `id` = :suid";

        $pIDSub = [
            'suid' => $suid
        ];
        
        $rIDSub = $this->db_handle->runBaseQuery($qIDSub, $pIDSub);
        return $rIDSub;
    }
}
?>
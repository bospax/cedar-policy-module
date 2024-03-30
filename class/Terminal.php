<?php 
class Terminal {
    private $db_handle;
    
    function __construct() {
        $this->db_handle = new DBController();
    }

    function getAllTerminal() {
        $qAllTerm = "SELECT * FROM `terminals`";
        $rAllterm = $this->db_handle->runBaseQuery($qAllTerm);

        return $rAllterm;
    }

    function getTerminalByID($tid) {
        $qTID = "SELECT * FROM `terminals` WHERE `id` = :tid";

        $pTID = [
            'tid' => $tid
        ];

        $rTID = $this->db_handle->runBaseQuery($qTID, $pTID);
        return $rTID;
    }

    function getTerminalByCode($termcode) {
        $qCode = "SELECT * FROM `terminals` WHERE `termcode` = :termcode";

        $pCode = [
            'termcode' => $termcode
        ];

        $rCode = $this->db_handle->runBaseQuery($qCode, $pCode);
        return $rCode;
    }

    function addTerminal($termcode, $termname) {
        $qAddTerm = "INSERT INTO `terminals` 
        (`termcode`, `termname`) 
        VALUES (:termcode, :termname)";
        
        $pAddTerm = [
            'termcode' => $termcode,
            'termname' => $termname
        ];

        $rAddTerm = $this->db_handle->insert($qAddTerm, $pAddTerm);
        return $rAddTerm;
    }

    function updateTerminal($termcode, $termname, $tid) {
        $qUpdTerm = "UPDATE `terminals` SET 
        `termcode` = :termcode,
        `termname` = :termname
        WHERE `id` = :tid";

        $pUpdTerm = [
            'termcode' => $termcode,
            'termname' => $termname,
            'tid' => $tid
        ];

        $rUpdTerm = $this->db_handle->update($qUpdTerm, $pUpdTerm);
        return $rUpdTerm;
    }

    function deleteTerminal($tid) {
        $qDelTerm = "DELETE FROM `terminals` WHERE `id` = :tid";
        $pDelTerm = [
            'tid' => $tid
        ];

        $rDelTerm = $this->db_handle->delete($qDelTerm, $pDelTerm);
        return $rDelTerm;
    }

    function checkDuplicateTermcode($termcode, $tid = '') {
        
        if (!empty($tid)) {
            
            $qTIDCode = "SELECT * FROM `terminals` WHERE `id` != :tid AND `termcode` = :termcode";

            $pTIDCode = [
                'tid' => $tid,
                'termcode' => $termcode
            ];

        } else {

            $qTIDCode = "SELECT * FROM `terminals` WHERE `termcode` = :termcode";

            $pTIDCode = [
                'termcode' => $termcode
            ];
        }

        $rTIDCode = $this->db_handle->runBaseQuery($qTIDCode, $pTIDCode);
        return $rTIDCode;
    }
}
?>
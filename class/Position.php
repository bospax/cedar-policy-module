<?php 
class Position {
    private $db_handle;
    
    function __construct() {
        $this->db_handle = new DBController();
    }

    function getAllPosition() {
        $qAllPos = "SELECT * FROM `positions`";
        $rAllPos = $this->db_handle->runBaseQuery($qAllPos);

        return $rAllPos;
    }

    function getPosByID($posid) {
        $qPosID = "SELECT * FROM `positions` WHERE `id` = :posid";

        $pPosID = [
            'posid' => $posid
        ];

        $rPosID = $this->db_handle->runBaseQuery($qPosID, $pPosID);

        return $rPosID;
    }

    function getMaxLevel() {
        $qMaxLvl = "SELECT max(`level`) AS `max_level` FROM `positions`";
        $rMaxLvl = $this->db_handle->runBaseQuery($qMaxLvl);

        return $rMaxLvl;
    }
}
?>
<?php 
class User {
    private $db_handle;
    
    function __construct() {
        $this->db_handle = new DBController();
    }

    function getAllUser() {
        $qAllUser = "SELECT * FROM `users`";
        $rAllUser = $this->db_handle->runBaseQuery($qAllUser);

        return $rAllUser;
    }

    function getUserByID($usid) {
        $qUserID = "SELECT * FROM `users` WHERE `id` = :usid";

        $pUserID = [
            'usid' => $usid
        ];

        $rUserID = $this->db_handle->runBaseQuery($qUserID, $pUserID);

        return $rUserID;
    }

    function addUser($fullname, $position, $email, $username, $password, $usertype, $permission, $sub_id, $term_id) {
        $qAddUser = "INSERT INTO `users` 
        (`fullname`, `position`, `email`, `username`, `password`, `usertype`, `permission`, `sub_id`, `term_id`) 
        VALUES (:fullname, :position, :email, :username, :pwd, :usertype, :permission, :sub_id, :term_id)";
        
        $pAddUser = [
            'fullname' => $fullname,
            'position' => $position,
            'email' => $email,
            'username' => $username,
            'pwd' => $password,
            'usertype' => $usertype,
            'permission' => $permission,
            'sub_id' => $sub_id,
            'term_id' => $term_id
        ];

        $rAddUser = $this->db_handle->insert($qAddUser, $pAddUser);
        return $rAddUser;
    }

    function updateUser($fullname, $position, $email, $username, $usertype, $permission, $sub_id, $term_id, $usid) {
        $qUpdUser = "UPDATE `users` SET 
        `fullname` = :fullname, 
        `position` = :position,
        `email` = :email,
        `username` = :username,
        `usertype` = :usertype,
        `permission` = :permission,
        `sub_id` = :sub_id,
        `term_id` = :term_id 
        WHERE `id` = :usid";

        $pUpdUser = [
            'fullname' => $fullname,
            'position' => $position,
            'email' => $email,
            'username' => $username,
            'usertype' => $usertype,
            'permission' => $permission,
            'sub_id' => $sub_id,
            'term_id' => $term_id,
            'usid' => $usid
        ];

        $rUpdUser = $this->db_handle->update($qUpdUser, $pUpdUser);
        return $rUpdUser;
    }

    function changePassword($password, $usid) {
        $qChgPass = "UPDATE `users` SET 
        `password` = :pwd
        WHERE `id` = :usid";

        $pChgPass = [
            'pwd' => $password,
            'usid' => $usid
        ];

        $rChgPass = $this->db_handle->update($qChgPass, $pChgPass);
        return $rChgPass;
    }

    function checkDuplicateUsername($username, $usid = '') {
        
        if (!empty($usid)) {
            
            $qUIDUname = "SELECT * FROM `users` WHERE `id` != :usid AND `username` = :username";

            $pUIDUname = [
                'usid' => $usid,
                'username' => $username
            ];

        } else {

            $qUIDUname = "SELECT * FROM `users` WHERE `username` = :username";

            $pUIDUname = [
                'username' => $username
            ];
        }

        $rUIDUname = $this->db_handle->runBaseQuery($qUIDUname, $pUIDUname);
        return $rUIDUname;
    }

    function checkDuplicateEmail($email, $usid = '') {
        
        if (!empty($usid)) {
            
            $qUIDEmail = "SELECT * FROM `users` WHERE `id` != :usid AND `email` = :email";

            $pUIDEmail = [
                'usid' => $usid,
                'email' => $email
            ];

        } else {

            $qUIDEmail = "SELECT * FROM `users` WHERE `email` = :email";

            $pUIDEmail = [
                'email' => $email
            ];
        }

        $rUIDEmail = $this->db_handle->runBaseQuery($qUIDEmail, $pUIDEmail);
        return $rUIDEmail;
    }

    function authenticatePassword($password, $usid) {

        $valid = false;

        $qUserPass = "SELECT * FROM `users` WHERE `id` = :usid";

        $pUserPass = [
            'usid' => $usid
        ];

        $rUserPass = $this->db_handle->runBaseQuery($qUserPass, $pUserPass);

        $hashed = $rUserPass[0]['password'];

        if (password_verify($password, $hashed)) {
            $valid =  true;
        }

        return $valid;
    }

    function authenticateUsername($username) {

        $qUserAuth = "SELECT * FROM `users` WHERE `username` = :username";

        $pUserAuth = [
            'username' => $username
        ];

        $rUserAuth = $this->db_handle->runBaseQuery($qUserAuth, $pUserAuth);

        return $rUserAuth;
    }

    function deleteUser($usid) {
        $qDelUser = "DELETE FROM `users` WHERE `id` = :usid";
        $pDelUser = [
            'usid' => $usid
        ];

        $rDelUser = $this->db_handle->delete($qDelUser, $pDelUser);
        return $rDelUser;
    }
}
?>
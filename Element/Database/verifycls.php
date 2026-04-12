<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    $s = '../../Element/Database/database.php';
    require_once $s;

    class verify_logic extends database{
        public function get_verifylock($idfile){
            $get_lock=$this->connect->prepare("SELECT start_time FROM verification_locks WHERE verify_id=?");
            $get_lock->execute(array($idfile));
            return $get_lock->fetch(PDO::FETCH_OBJ);
        }
        public function del_lock($idfile){
            $del_lock=$this->connect->prepare("DELETE FROM verification_locks WHERE verify_id=?");
            $del_lock->execute(array($idfile));
        }
        public function add_lock($idfile){
            $add_lock=$this->connect->prepare("INSERT INTO verification_locks (verify_id, start_time) VALUES (?,NOW())");
            $add_lock->execute(array($idfile));
        }
    }
?>
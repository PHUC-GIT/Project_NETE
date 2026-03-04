<?php
    $s = '../../Element/Database/database.php';
    require_once $s;

    class admin_user extends database{
        public function admin_ID($uploadedfile){
            $get_value=$this->connect->prepare("SELECT id_admin FROM admin WHERE key_value=?");
            $get_value->execute(array($uploadedfile));
            return $get_value->fetch(PDO::FETCH_OBJ);
        }
        public function AdminCheckKey($uploadedfile) {
            $select = $this->connect->prepare("SELECT key_value FROM admin WHERE key_value=? LIMIT 1");
            $select->execute(array($uploadedfile));
            if ($select->fetchAll(PDO::FETCH_ASSOC)){
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }
?>
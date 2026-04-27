<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    $s = '../../Element/Database/database.php';
    require_once $s;

    class admin_user extends database{
        public function AdminCheckKey($uploadedfile) {
            $select = $this->connect->prepare(
                "SELECT id_admin FROM admin WHERE key_value=? LIMIT 1"
            );
            $select->execute(array($uploadedfile));
            $result = $select->fetch(PDO::FETCH_OBJ);
            return $result ?: false;
        }
    }
?>
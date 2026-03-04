<?php
    $s = '../../Element/Database/database.php';
    if (file_exists($s)){
        $f=$s;
    } else {
        $f = './Element/Database/database.php';
    }
    require_once $f;
    class preference extends database{
        public function updateprefer($backgroundvalue){
            // Main operation
            $update=$this->connect->prepare("UPDATE preference SET background=? WHERE userid=?");
            $update->execute(array($backgroundvalue, $_SESSION['AUTHENTICATE_USER']));
            return $update->rowCount() !== false;
        }

        public function preferenceinfo() {
            $getprefer=$this->connect->prepare("SELECT preference.background FROM preference WHERE userid = ?");
            $getprefer->execute([$_SESSION['AUTHENTICATE_USER']]);
            return $getprefer->fetchColumn();
        }
    }
?>
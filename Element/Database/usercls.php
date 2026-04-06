<?php
    $s = '../../Element/Database/database.php';
    if (file_exists($s)){
        $f=$s;
    } else {
        $f = './Element/Database/database.php';
    }
    require_once $f;
    class user extends database{
        public function user_list(){
            $get_value=$this->connect->prepare("SELECT * FROM user");
            $get_value->execute();
            return $get_value->fetchAll(PDO::FETCH_OBJ);
        }
        public function get_user_info($Get_ID){
            $get_value=$this->connect->prepare("SELECT * FROM user WHERE iduser=?");
            $get_value->execute(array($Get_ID));
            return $get_value->fetch(PDO::FETCH_OBJ);
        }
        // Master Storage Awarness
        public function get_totalallocated(){
            $get_size = $this->connect->prepare("SELECT SUM(storage_allocated) as total_size FROM user");
            $get_size->execute();
            $result = $get_size->fetch(PDO::FETCH_OBJ);
            return $result->total_size ?? 0;
        }
        public function update_user_withpassword($username, $password, $comment, $storage, $Get_ID){
            $update_value=$this->connect->prepare("UPDATE user SET username=?, password=?, comment=?, storage_allocated=? WHERE iduser=?");
            $update_value->execute(array($username, $password, $comment, $storage, $Get_ID));
            return $update_value->rowCount() > 0;
        }
        public function update_user_notpassword($username, $comment, $storage, $Get_ID){
            $update_value=$this->connect->prepare("UPDATE user SET username=?, comment=?, storage_allocated=? WHERE iduser=?");
            $update_value->execute(array($username, $comment, $storage, $Get_ID));
            return $update_value->rowCount() > 0;
        }
        public function add_user($username, $password, $salt, $comment, $storage){
            $add_value=$this->connect->prepare("INSERT INTO user (username, password, salt, comment, storage_allocated) VALUES (?,?,?,?,?)");
            $add_value->execute(array($username, $password, $salt, $comment, $storage));
            // Retrive the ID from new user by the name
            $getprefer=$this->connect->prepare("SELECT iduser FROM user WHERE username=?");
            $getprefer->execute(array($username));
            $getuser=$getprefer->fetchColumn();
            // Add the preference for user.
            $add_value=$this->connect->prepare("INSERT INTO preference (userid) VALUES (?)");
            $add_value->execute(array($getuser));
            return $add_value->rowCount() !== false;
        }
        public function user_ID($Get_NAME){
            $get_value=$this->connect->prepare("SELECT iduser, salt FROM user WHERE username COLLATE utf8_bin = ?");
            $get_value->execute(array($Get_NAME));
            return $get_value->fetch(PDO::FETCH_OBJ);
        }
        public function user_Name($Get_ID){
            $get_value=$this->connect->prepare("SELECT username FROM user WHERE iduser = ?");
            $get_value->execute(array($Get_ID));
            return $get_value->fetch(PDO::FETCH_OBJ);
        }
        public function user_salt($Get_ID){
            $get_value=$this->connect->prepare("SELECT salt FROM user WHERE iduser = ?");
            $get_value->execute(array($Get_ID));
            return $get_value->fetch(PDO::FETCH_OBJ);
        }
        public function user_file($Get_ID, $mime_type="nete/folder"){
            $get_value=$this->connect->prepare("SELECT * FROM files WHERE Uploader = ? AND mime_type != ?");
            $get_value->execute(array($Get_ID, $mime_type));
            return $get_value->fetchAll(PDO::FETCH_OBJ);
        }
        public function user_NOTE($Get_ID){
            $get_value=$this->connect->prepare("SELECT COUNT(*) FROM note WHERE Whois = ?");
            $get_value->execute(array($Get_ID));
            return $get_value->fetchColumn();
        }
        public function UserCheckLogin($username, $password) {
            $select = $this->connect->prepare("SELECT password FROM user WHERE username COLLATE utf8_bin = ?");
            $select->execute(array($username));
            // Fetch the result
            if (!$user_data = $select->fetch(PDO::FETCH_OBJ)) {
                return FALSE; 
            }
            $stored_hash = $user_data->password;
            if (password_verify($password, $stored_hash)) {
                return TRUE;
            } else {
                return FALSE;
            }
        }

        // Queue to delete user 
        private function userdelqueue($Get_ID) {
            // Delete queue
            $SQL_QUEUE = array("DELETE FROM files WHERE Uploader = ?", "DELETE FROM note WHERE Whois = ?", "DELETE FROM preference WHERE userid = ?", "DELETE FROM user WHERE iduser = ?");
            foreach ($SQL_QUEUE as $SQL) {
                $deletequeue = $this->connect->prepare($SQL);
                if (!$deletequeue->execute(array($Get_ID))) {
                    return false;
                }
            }
            return true;
        }

        public function deleteuser($Get_ID) {
            $this->connect->beginTransaction();
            try {
                // Start delete user
                $result = $this->userdelqueue($Get_ID);
                if ($result) {
                    $this->connect->commit();
                    return true;
                } else {
                    $this->connect->rollBack();
                    return false;
                }
            } catch (PDOException $e) {
                $this->connect->rollBack();
                return false;
            }
        }
    }
?>
<?php
    $s = '../../Element/Database/database.php';
    if (file_exists($s)){
        $f=$s;
    } else {
        $f = './Element/Database/database.php';
    }
    require_once $f;
    class track extends database{
        public function getinfo_spy($getidfile){
            $info=$this->connect->prepare("SELECT files.* FROM files JOIN user_report ON user_report.ITEM_ID = files.id WHERE files.id = ? AND user_report.ITEM_TYPE = 'file'");
            $info->execute(array($getidfile));
            return $info->fetch(PDO::FETCH_OBJ);
        }
        // New Feature and Redemtion
        public function getreportlist(){
            $getlist=$this->connect->prepare("SELECT user_report.*, user.username FROM user_report JOIN user ON user_report.USER_ID = user.iduser");
            $getlist->execute();
            return $getlist->fetchAll(PDO::FETCH_OBJ);
        }
        public function reportin_file($reason, $fileid){
            // Main operation
            $add=$this->connect->prepare("INSERT INTO user_report (USER_ID, ITEM_TYPE, ITEM_ID, REASON) VALUE (?,?,?,?)");
            $add->execute(array($_SESSION['AUTHENTICATE_USER'], 'file', $fileid, $reason));
            return $add->rowCount() !== false;
        }
        public function reportin_note($reason, $noteid){
            // Main operation
            $add=$this->connect->prepare("INSERT INTO user_report (USER_ID, ITEM_TYPE, ITEM_ID, REASON) VALUE (?,?,?,?)");
            $add->execute(array($_SESSION['AUTHENTICATE_USER'], 'note', $noteid, $reason));
            return $add->rowCount() !== false;
        }
        public function getreported_file(){
            $getList=$this->connect->prepare("SELECT files.*, user.username, user.iduser FROM files JOIN user_report ON files.id = user_report.ITEM_ID JOIN user ON files.Uploader = user.iduser WHERE user_report.ITEM_TYPE = 'file' GROUP BY files.id ORDER BY MAX(user_report.DATE) DESC");
            $getList->execute();
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function reportfile_search($search_query){
            $search_query = trim($search_query);
            $search_query = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search_query);
            $final_query = '%' . $search_query . '%';
            $getList=$this->connect->prepare("SELECT files.*, user.username FROM files JOIN user_report ON files.id = user_report.ITEM_ID JOIN user ON files.Uploader = user.iduser WHERE user_report.ITEM_TYPE = 'file' AND LOWER(files.file_name) LIKE LOWER(?) GROUP BY files.id ORDER BY MAX(user_report.DATE) DESC");
            $getList->execute((array($final_query)));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function getreported_note(){
            $getList=$this->connect->prepare("SELECT note.*, user.username, user.iduser FROM note JOIN user_report ON note.id = user_report.ITEM_ID JOIN user ON note.Whois = user.iduser WHERE user_report.ITEM_TYPE = 'note' GROUP BY note.id ORDER BY MAX(user_report.DATE) DESC");
            $getList->execute();
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function reportnote_search($search_query){
            $search_query = trim($search_query);
            $search_query = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search_query);
            $final_query = '%' . $search_query . '%';
            $getList=$this->connect->prepare("SELECT note.*, user.username FROM note JOIN user_report ON note.id = user_report.ITEM_ID JOIN user ON note.Whois = user.iduser WHERE user_report.ITEM_TYPE = 'note' AND LOWER(user.username) LIKE LOWER(?) GROUP BY note.id ORDER BY MAX(user_report.DATE) DESC");
            $getList->execute(array($final_query));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function delete_report($item_type, $item_id){
            $del=$this->connect->prepare("DELETE FROM user_report WHERE ITEM_TYPE=? AND ITEM_ID=?");
            $del->execute(array($item_type, $item_id));
            return $del->rowCount() !== false;
        }
        public function delete_report_gulity($item_type, $item_id){
            // Recheck if the file or note actually in report?
            if ($item_type == 'file') {
                $check=$this->connect->prepare("SELECT EXISTS (SELECT 1 FROM user_report INNER JOIN files ON user_report.ITEM_ID = files.id WHERE files.id = ?) AS item_exists");
                $check->execute(array($item_id));
                $getcheck = $check->fetch(PDO::FETCH_ASSOC);
                if (($getcheck['item_exists']) == 1) {
                } else {
                    return false;
                    die;
                }
            } else {
                $check=$this->connect->prepare("SELECT EXISTS (SELECT 1 FROM user_report INNER JOIN note ON user_report.ITEM_ID = note.id WHERE note.id = ?) AS item_exists");
                $check->execute(array($item_id));
                $getcheck = $check->fetch(PDO::FETCH_ASSOC);
                if (($getcheck['item_exists']) == 1) {
                } else {
                    return false;
                    die;
                }
            }
            // Delete The Report Entry
            $del=$this->connect->prepare("DELETE FROM user_report WHERE ITEM_TYPE=? AND ITEM_ID=?");
            $del->execute(array($item_type, $item_id));
            // Delete File and Notes overide!
            if ($item_type == 'file') {
                // Delete exelusive file
                $delfile=$this->connect->prepare("DELETE FROM files WHERE id=?");
                $delfile->execute(array($item_id));
                return $delfile->rowCount() !== false;
            } else {
                // Delete exelusive note if it's not file.
                $delfile=$this->connect->prepare("DELETE FROM note WHERE id=?");
                $delfile->execute(array($item_id));
                return $delfile->rowCount() !== false;
            }

        }
        public function get_file_link($file_id){
            $checkdir=$this->connect->prepare("SELECT file_link FROM files WHERE id=?");
            $checkdir->execute(array($file_id));
            return $checkdir->fetch(PDO::FETCH_OBJ);
        }
        
        public function redtrigger($get_id) {
            $update=$this->connect->prepare("UPDATE files SET file_status=1, last_check=NOW() WHERE id=?");
            $update->execute(array($get_id));
        }
    }
?>
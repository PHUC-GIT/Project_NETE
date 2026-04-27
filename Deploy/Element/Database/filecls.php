<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    $s = '../../Element/Database/database.php';
    if (file_exists($s)){
        $f=$s;
    } else {
        $f = './Element/Database/database.php';
    }
    require_once $f;
    class index extends database{

        // List Files or Files In Folder
        public function listfile($get_id, $parent_folder_id, $mime_type="nete/folder"){
            $getList=$this->connect->prepare("SELECT * FROM files WHERE Uploader = ? AND parent_folder_id=? ORDER BY CASE WHEN mime_type = ? THEN 0 ELSE 1 END");
            $getList->execute(array($get_id, $parent_folder_id, $mime_type));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function listfile_search($get_id, $search_query, $mime_type="nete/folder"){
            $search_query = trim($search_query);
            $search_query = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search_query);
            $final_query = '%' . $search_query . '%';
            $getList=$this->connect->prepare("SELECT * FROM files WHERE Uploader = ? AND LOWER(file_name) LIKE LOWER(?) AND mime_type != ?");
            $getList->execute(array($get_id, $final_query, $mime_type));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function get_folder_by_id($id) {
            $stmt = $this->connect->prepare("SELECT file_name FROM files WHERE id=? AND type='folder'");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        }

        // Check for real dir
        public function checkiffolderexist($id, $mime_type="nete/folder") {
            $getList=$this->connect->prepare("SELECT id FROM files WHERE Uploader = ? AND id=? AND mime_type=?");
            $getList->execute(array($_SESSION['AUTHENTICATE_USER'], $id, $mime_type));
            return $getList->fetch(PDO::FETCH_OBJ) !== false;
        }
        // Check for Illegal move
        public function CheckMoveConflict($get_id, $parent_folder_id) {
            // Handle unexpect
            if ($parent_folder_id === null || $parent_folder_id === "NULL"){
                return false;
            }
            try {
                $checkfolder = $this->connect->prepare("WITH RECURSIVE FolderHierarchy AS (SELECT id FROM files WHERE id = ? UNION ALL SELECT f.id FROM files f INNER JOIN FolderHierarchy fh ON f.parent_folder_id = fh.id WHERE f.mime_type = 'nete/folder') SELECT COUNT(id) AS conflict_count FROM FolderHierarchy WHERE id = ?");
                $checkfolder->execute(array($get_id, $parent_folder_id));
                $result = $checkfolder->fetch(PDO::FETCH_ASSOC);
                return $result['conflict_count'] > 0;
            } catch (PDOException $e){
                error_log("NETE REPORT -> DB error in check_move_conflict: " . $e->getMessage());
                return true;
            }
        }

        // Storage Awareness System
        public function get_totalsize(){
            $get_size = $this->connect->prepare("SELECT SUM(size) as total_size FROM files WHERE Uploader = ?");
            $get_size->execute(array($_SESSION['AUTHENTICATE_USER'] ?? ''));
            $result = $get_size->fetch(PDO::FETCH_OBJ);
            return $result->total_size ?? 0;
        }
        public function get_userlocated(){
            $get_size = $this->connect->prepare("SELECT storage_allocated FROM user WHERE iduser=?");
            $get_size->execute(array($_SESSION['AUTHENTICATE_USER']));
            return $get_size->fetch(PDO::FETCH_OBJ);
        }

        // List Public Files
        public function sharefile(){
            $getShare=$this->connect->prepare("SELECT files.*, user.username, user.iduser FROM files JOIN user ON files.Uploader = user.iduser WHERE files.share=1");
            $getShare->execute();
            return $getShare->fetchAll(PDO::FETCH_OBJ);
        }
        public function sharefile_search($search_query){
            $search_query = trim($search_query);
            $search_query = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search_query);
            $final_query = '%' . $search_query . '%';
            $getList=$this->connect->prepare("SELECT files.*, user.username, user.iduser FROM files JOIN user ON files.Uploader = user.iduser WHERE files.share=1 AND LOWER(files.file_name) LIKE LOWER(?)");
            $getList->execute(array($final_query));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }

        // Get files info.
        public function getinfo($getidfile){
            $info=$this->connect->prepare("SELECT * FROM files WHERE id=? AND (Uploader=? OR share=1)");
            $info->execute(array($getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $info->fetch(PDO::FETCH_OBJ);
        }
        public function getinfo_edit($getidfile){
            $info=$this->connect->prepare("SELECT * FROM files WHERE id=? AND Uploader=?");
            $info->execute(array($getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $info->fetch(PDO::FETCH_OBJ);
        }
        public function getfileassoc($getidfile){
            $info=$this->connect->prepare("SELECT file_link FROM files WHERE parent_folder_id=? AND Uploader=?");
            $info->execute(array($getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $info->fetchAll(PDO::FETCH_OBJ);
        }
        public function getnumfileinfolder($getidfile){
            $info=$this->connect->prepare("SELECT COUNT(*) FROM files WHERE parent_folder_id=? AND Uploader=?");
            $info->execute(array($getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $info->fetchColumn();
        }
        public function getfolderavailable($mime_type="nete/folder"){
            $info=$this->connect->prepare("SELECT id, file_name FROM files WHERE mime_type=? AND Uploader=?");
            $info->execute(array($mime_type, $_SESSION['AUTHENTICATE_USER']));
            return $info->fetchAll(PDO::FETCH_OBJ);
        }
        public function countfile($mime_type="nete/folder"){
            $get_value=$this->connect->prepare("SELECT COUNT(id) FROM files WHERE Uploader = ? AND mime_type != ?");
            $get_value->execute(array($_SESSION['AUTHENTICATE_USER'], $mime_type));
            return $get_value->fetchColumn();
        }
        public function countfolder($mime_type="nete/folder"){
            $get_value=$this->connect->prepare("SELECT COUNT(id) FROM files WHERE Uploader = ? AND mime_type = ?");
            $get_value->execute(array($_SESSION['AUTHENTICATE_USER'], $mime_type));
            return $get_value->fetchColumn();
        }
        public function checkfilehash($gethashfile){
            $get_hash=$this->connect->prepare("SELECT sha256_hash FROM files WHERE sha256_hash=? and Uploader=? LIMIT 1");
            $get_hash->execute(array($gethashfile, $_SESSION['AUTHENTICATE_USER']));
            if ($get_hash->fetch(PDO::FETCH_ASSOC)){
                return TRUE;
            } else {
                return FALSE;
            }
        }
        // System that management the files logic to database.
        public function addfile($file_name, $file_link, $file_size, $filetype, $sha256_hash, $parent_folder_id, $filemime, $is_share, $retry = 3): bool{
            // Gen Unique ID
            $uniid = $this->get_custom_id();
            // Main operation
            try {
                $add=$this->connect->prepare("INSERT INTO files (id, Uploader, file_name, file_link, size, type, sha256_hash, parent_folder_id, mime_type, share) VALUES (?,?,?,?,?,?,?,?,?,?)");
                $add->execute(array($uniid, $_SESSION['AUTHENTICATE_USER'], $file_name, $file_link, $file_size, $filetype, $sha256_hash, $parent_folder_id, $filemime, $is_share));
                return $add->rowCount() > 0;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000' && $retry > 0) {
                    return $this->addfile($file_name, $file_link, $file_size, $filetype, $sha256_hash, $parent_folder_id, $filemime, $is_share, $retry - 1);
                }
                return false;
            }
        }
        public function deletefile($getidfile){
            
            // Main operation
            $del=$this->connect->prepare("DELETE FROM files WHERE id=? AND Uploader=?");
            $del->execute(array($getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $del->rowCount() > 0;
        }

        // New function to handle both physical and database deletion recursively.
        private function recursiveDelete($folderId, $userId) {
            // Get all files and folders inside the current folder
            $getContents = $this->connect->prepare("SELECT id, file_link, mime_type FROM files WHERE parent_folder_id = ? AND Uploader = ?");
            $getContents->execute(array($folderId, $userId));
            $contents = $getContents->fetchAll(PDO::FETCH_OBJ);
            foreach ($contents as $item) {
                // Check if the item is a folder using your unique mime_type
                if ($item->mime_type === 'nete/folder') {
                    // Recursively delete the sub-folder and its contents
                    if (!$this->recursiveDelete($item->id, $userId)) {
                        return false; // Stop if a nested deletion fails
                    }
                } else {
                    // Delete the physical file first
                    $physicalPath = "../." . $item->file_link;
                    if (file_exists($physicalPath)) {
                        if (!unlink($physicalPath)) {
                            // Log error and stop
                            return false;
                        }
                    }
                    // Delete the database record for the file
                    // Delete quick access entry from the sidebar if it exist.
                    if (isset($_SESSION['QUICK_ACCESS_FILE'][$item->id])) {
                        unset($_SESSION['QUICK_ACCESS_FILE'][$item->id]);
                    }
                    $delFile = $this->connect->prepare("DELETE FROM files WHERE id = ? AND Uploader = ?");
                    if (!$delFile->execute(array($item->id, $userId))) {
                        return false;
                    }
                }
            }
            // Now delete the database record for the parent folder itself
            $delFolder = $this->connect->prepare("DELETE FROM files WHERE id = ? AND Uploader = ?");
            if (!$delFolder->execute(array($folderId, $userId))) {
                return false;
            }
            return true;
        }

        // Delete Folder
        public function deletefolder($getidfile){
            $this->connect->beginTransaction();
            try {
                // Start the recursive deletion process
                $result = $this->recursiveDelete($getidfile, $_SESSION['AUTHENTICATE_USER']);
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
        
        public function updatefile($file_name, $is_share, $moving_file, $getidfile){
            // Main operation
            $update=$this->connect->prepare("UPDATE files SET file_name=?, share=?, parent_folder_id=? WHERE id=? AND Uploader=?");
            $update->execute(array($file_name, $is_share,$moving_file, $getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $update->rowCount() > 0;
        }

        public function updatefilecontent($get_size, $get_hash, $getidfile){
            // Main operation
            $update=$this->connect->prepare("UPDATE files SET size=?, sha256_hash=?, date=NOW(), last_check=NOW() WHERE id=? AND Uploader=?");
            $update->execute(array($get_size, $get_hash, $getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $update->rowCount() > 0;
        }

        public function updatefolder($folder_name, $file_link, $moving_file, $getidfile){
            // Main operation
            $update=$this->connect->prepare("UPDATE files SET file_name=?, file_link=?, parent_folder_id=? WHERE id=? AND Uploader=? AND mime_type='nete/folder'");
            $update->execute(array($folder_name, $file_link, $moving_file, $getidfile, $_SESSION['AUTHENTICATE_USER']));
            return $update->rowCount() > 0;
        }
        
        public function updatestatus($status, $get_id) {
            $update=$this->connect->prepare("UPDATE files SET file_status=?, last_check=NOW() WHERE id=?");
            $update->execute(array($status, $get_id));
        }

        public function redtrigger($get_id) {
            $update=$this->connect->prepare("UPDATE files SET file_status=1, last_check=NOW() WHERE id=?");
            $update->execute(array($get_id));
        }
    }
?>
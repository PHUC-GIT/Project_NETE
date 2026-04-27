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
        public function listnote($whois){
            $getList=$this->connect->prepare("SELECT * FROM note WHERE Whois = ? ORDER BY date DESC");
            $getList->execute((array($whois)));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function listpublic(){
            $getList=$this->connect->prepare("SELECT note.*, user.username, user.iduser FROM note JOIN user ON note.Whois = user.iduser WHERE note.public_value=1 ORDER BY date DESC");
            $getList->execute();
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function listpublic_search($search_query){
            $search_query = trim($search_query);
            $search_query = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search_query);
            $final_query = '%' . $search_query . '%';
            $getList=$this->connect->prepare("SELECT note.*, user.username, user.iduser FROM note JOIN user ON note.Whois = user.iduser WHERE note.public_value=1 AND LOWER(user.username) LIKE LOWER(?)");
            $getList->execute(array($final_query));
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }
        public function countnote(){
            $get_value=$this->connect->prepare("SELECT COUNT(id) FROM note WHERE Whois = ?");
            $get_value->execute(array($_SESSION['AUTHENTICATE_USER']));
            return $get_value->fetchColumn();
        }
        public function addnote($text_content, $default_value, $tag, $retry = 3): bool {
            // Gen Unique ID
            $uniid = $this->get_custom_id();
            // Main operation
            try {
                $add=$this->connect->prepare("INSERT INTO note (id, Whois, text, public_value, tag) VALUES (?,?,?,?,?)");
                $add->execute(array($uniid, $_SESSION['AUTHENTICATE_USER'], $text_content, $default_value, $tag));
                return $add->rowCount() > 0;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000' && $retry > 0) {
                    return $this->addnote($text_content, $default_value, $tag, $retry - 1);
                }
                return false;
            }
        }
        public function deletenote($getidnote){
            // Main operation
            $del=$this->connect->prepare("DELETE FROM note WHERE id=? AND Whois=?");
            $del->execute(array($getidnote, $_SESSION['AUTHENTICATE_USER']));
            return $del->rowCount() > 0;
        }
        public function updatenote($text_content, $tag_public,$tag, $getidnote){
            // Main operation
            $update=$this->connect->prepare("UPDATE note SET text=?, public_value=?, tag=? WHERE id=? AND Whois=?");
            $update->execute(array($text_content, $tag_public,$tag, $getidnote, $_SESSION['AUTHENTICATE_USER']));
            return $update->rowCount() > 0;
        }
    }
?>
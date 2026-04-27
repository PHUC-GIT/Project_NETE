<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    $s = '../../Element/Database/database.php';
    if (file_exists($s)){
        $f=$s;
    } else {
        $f = './Element/Database/database.php';
    }
    require_once $f;

    class report extends database{
        private function getUserIP(): string {
            // Only trust X_FORWARDED_FOR if behind a trusted proxy
            if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $ip = 'Unknown';
                foreach ($ipList as $candidateIp) {
                    $candidateIp = trim($candidateIp);
                    // Use the first valid, public IP found (or just the first valid one)
                    if (filter_var($candidateIp, FILTER_VALIDATE_IP)) {
                        $ip = $candidateIp;
                        break; 
                    }
                }
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
            }
            // Final sanity check and sanitization before returning
            return filter_var($ip, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        }

        public function report_in($cause, $user, $retry = 3): bool {
            // Gen Unique ID
            $uniid = $this->get_custom_id();
            // Use the new private method
            try {
                $ip = $this->getUserIP();
                $report = $this->connect->prepare("INSERT INTO report (id, cause, user, IP) VALUES (?,?,?,?)");
                $report->execute(array($uniid, $cause, $user, $ip));
                return $report->rowCount() > 0;
            } catch (PDOException $e) {
                if ($e->getCode() === '23000' && $retry > 0) {
                    return $this->report_in($cause, $user, $retry -1);
                }
                return false;
            }
        }

        public function listreport(){
            $getList=$this->connect->prepare("SELECT * FROM report");
            $getList->execute();
            return $getList->fetchAll(PDO::FETCH_OBJ);
        }

        public function listallfile(){
            $allPDF=$this->connect->prepare("SELECT files.*, user.username FROM files JOIN user ON files.Uploader = user.iduser");
            $allPDF->execute();
            return $allPDF->fetchAll(PDO::FETCH_OBJ);
        }
    }
?>
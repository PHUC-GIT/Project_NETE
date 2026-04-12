<?php

// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

class database {
    
    public $connect;

    private function failSafe($msg) {
      error_log("NETE_REPORT -> $msg");
      if (session_status() === PHP_SESSION_ACTIVE) {
          session_unset();
          session_destroy();
      }
      session_start();
      session_regenerate_id(true);
      if (!headers_sent()) {
          $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "System unable to processe your info.");
          header('Location: ../../index.php');
      }
      exit;
  }

    
    public function __construct() {
        try {
          $init = parse_ini_file("Database_Config/config.ini");
          $servername = $init["servername"] ?? '';
          $dbname = $init["dbname"] ?? '';
          $username = $init["username"] ?? '';
          $password = $init["pass"] ?? '';
          
          $opt = array(
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
          );

          $this->connect=new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password, $opt);
        } catch(PDOException $e) {
          $this->failSafe("Database connection failed: " . $e->getMessage());
        } catch (Exception $e) {
          $this->failSafe("Database connection failed: " . $e->getMessage());
        }
    }
}
?>
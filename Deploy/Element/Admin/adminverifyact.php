<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    ini_set('session.cookie_httponly', '1'); 
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', '1'); // Change it to '0' if you're run it on http localhost or without secured HTTPS but only if something unexpect happened. 
    session_start();
    header("X-Frame-Options: DENY");
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("X-Content-Type-Options: nosniff");
    // Check for POST method
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Head
        require '../../Element/Database/admincls.php';
        if(isset($_POST['reqact'])) {
            $requestaction = $_POST['reqact'];
            switch ($requestaction) {
                case 'checkkey':
                    $get_pre_ip = isset($_POST['show_ip']) ? $_POST['show_ip'] : 'null';
                    if ($_FILES['idkey']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['idkey']['tmp_name'])) { //checks if file is uploaded
                        $getkeysize = $_FILES['idkey']['size'];
                        if ($getkeysize > 5242880){
                            // Exacly 5MB allowed uploaded key before get hash.
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Key too big to scan.");
                            header('location:../../login_admin.php');
                            die;
                        }
                        $uploadedfile = hash_file('sha256', $_FILES['idkey']['tmp_name']);
                        $admin = new admin_user();
                        $result = $admin->AdminCheckKey($uploadedfile);
                        if ($result){
                            // Flush old session for new veried session.
                            session_unset();
                            session_destroy();
                            session_start();
                            session_regenerate_id(true);
                            // Cooking CSRF Token
                            if (empty($_SESSION['CSRF_TOKEN'])) {
                                $_SESSION['CSRF_TOKEN'] = bin2hex(random_bytes(64 / 2));
                            }
                            // Check IP Preference.
                            if (($get_pre_ip) == 'Hide_IP') {
                                $ip = '***.***.***.***';
                            } else {
                                function getUserIP() {
                                    // Only trust X_FORWARDED_FOR if behind a trusted proxy
                                    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
                                        return $_SERVER['HTTP_CLIENT_IP'];
                                    }
                                
                                    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                                        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                                        foreach ($ipList as $ip) {
                                            $ip = trim($ip);
                                            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                                                return $ip;
                                            }
                                        }
                                    }
                                    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
                                }       
                                $ip = getUserIP();
                            }
                            $_SESSION['AUTHENTICATE_ADMIN'] = $result->id_admin;
                            $_SESSION['USER_ADDR_IP'] = $ip;
                            $_SESSION['WELCOME_POPUP'] = true;
                            header('location:../../index.php');
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Wrong key.");
                            header('location:../../login_admin.php');
                            die;
                        }
                    }else{
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Cant read the files");
                        header('location:../../login_admin.php');
                        die;
                    }
                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Upload key please?");
                    header('location:../../login_admin.php');
                    die;    
            }
        }
        // Tail

    } else {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
        header('location:../../login.php');
        die;
    }
?>
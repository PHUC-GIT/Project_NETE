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
        require '../../Element/Database/usercls.php';
        if (isset($_POST['reqact'])) {
            $requestaction = $_POST['reqact'];
            switch ($requestaction) {
                case 'checklogin':
                $username = isset($_POST['username']) ? $_POST['username'] : '';
                $password = isset($_POST['password']) ? $_POST['password'] : '';
                $get_pre_ip = isset($_POST['show_ip']) ? $_POST['show_ip'] : '';
                $user = new user();
                $rs = $user->UserCheckLogin($username, $password);
                if ($rs) {
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

                        $_SESSION['AUTHENTICATE_USER'] = $user->user_ID($username)->iduser;
                        $_SESSION['USER_ADDR_IP'] = $ip;
                        $_SESSION['SALT_VALUE'] = $user->user_ID($username)->salt;
                        $_SESSION['WELCOME_POPUP'] = true;
                        $_SESSION['QUICK_ACCESS_FILE'] = array();
                        header('location:../../index.php');
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Your login info is incorrect.");
                        echo "<script>window.location.href='../../login.php';</script>";
                        die;
                    }
                    break;
                
                case 'logout':
                    // Flush old session for new veried session.
                    session_unset();
                    session_destroy();
                    session_start();
                    session_regenerate_id(true);
                    header('location:../../index.php');
                    break;
            }
        }
        // Tail
    } else {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
        header('location:../../login.php');
        die;
    }
?>
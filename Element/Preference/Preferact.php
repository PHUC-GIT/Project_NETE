<?php
ini_set('session.cookie_httponly', '1'); 
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', '1'); // Change it to '0' if you're run it on http localhost or without secured HTTPS but only if something unexpect happened. 
session_start();
header("X-Frame-Options: DENY");
header('Referrer-Policy: strict-origin-when-cross-origin');
header("X-Content-Type-Options: nosniff");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $crsf_get = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    if ($crsf_get === $_SESSION['CSRF_TOKEN']) {
        // CSRF Matched and reload anew.
        unset($_SESSION['CSRF_TOKEN']);
        // Recheck if the session already empty to insert new token.
        $regen_csrf = bin2hex(random_bytes(64 / 2));
        $_SESSION['CSRF_TOKEN'] = $regen_csrf;

        // Head
        require "../../Element/Database/prefercls.php";
        $requestaction = $_POST['reqact'] ?? null;
        if($requestaction) { 
            switch ($requestaction) {
                case 'updatesetting':
                    $systembackgroundlist = array("B1", "B2", "B3", "B4", "B5", "B6", "B7");
                    $backgroundvalue = isset($_POST['is_background']) ? $_POST['is_background'] : 'B1'; // B1 is system default.
                    if (!in_array($backgroundvalue, $systembackgroundlist)) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Unknow background value. Please set again.");
                        header('location:../../index.php?req=preference');
                        die;
                    }
                    $requestupdate = new preference();
                    $result = $requestupdate->updateprefer($backgroundvalue);
                    if ($result) {
                        header('location:../../index.php?req=preference');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "There is something wrong! Please try again.");
                        header('location:../../index.php?req=preference');
                        die;
                    }
                    die;

                case 'updatepasswordonly':
                    $oldpassword = isset($_POST['old_password']) ? $_POST['old_password'] : '';
                    $password = isset($_POST['new_password']) ? $_POST['new_password'] : 'ilovenete';
                    if (empty($password)) {
                        $password = 'ilovenete';
                    }
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $requestupdate = new preference();
                    $check = $requestupdate->recheckpassword($oldpassword);
                    if ($check) {
                        $result = $requestupdate->update_user_passwordonly($hashed_password);
                        if ($result) {
                            header('location:../../index.php?req=preference');
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Can't update password! Please try again.");
                            header('location:../../index.php?req=preference');
                            die;
                        }
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Your old password is not correct. Please try again.");
                        header('location:../../index.php?req=preference');
                        die;
                    }
                    die;
            }
        }
        // Tail
    } else {
        // Regen the CSRF even on fail.
        unset($_SESSION['CSRF_TOKEN']);
        // Recheck if the session already empty to insert new token.
        $regen_csrf = bin2hex(random_bytes(64 / 2));
        $_SESSION['CSRF_TOKEN'] = $regen_csrf;
        
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Something when wrong. Please try again.");
        header('location:../../index.php?req=preference');
        die;
    }
} else {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
    header('location:../../index.php?req=preference');
    die;
}
?>
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

        // Function For Reuse
        function deleteDirectory($targetdir) {
            // If the folder dont exist.
            if (!is_dir($targetdir)) {
                return true;
            }
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $targetdir,
                    RecursiveDirectoryIterator::SKIP_DOTS
                ),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
            return rmdir($targetdir);
        }
        require '../../Element/Database/usercls.php';
        // Get SESSION user, And verify one also. (I know that in this space is only admin access only but anyway)
        if (isset($_SESSION['AUTHENTICATE_USER'])) {
            $getuserinfo = new user();
            $resultget = $getuserinfo->user_Name($_SESSION['AUTHENTICATE_USER']);
            $session_user = $resultget->username ?? null;
            if (!$session_user) {
                // Destroy the session if user don't found
                if (session_status() === PHP_SESSION_ACTIVE) {
                  session_unset();
                  session_destroy();
                }
                session_start();
                session_regenerate_id(true);
                if (!headers_sent()) {
                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "System unable to processe your info.");
                    header('location:../../login.php');
                }
                die;
            }
        }
        // Get Master Drive value information
        $requestalluserstorage = new user();
        $Alluserstorage = $requestalluserstorage->get_totalallocated(); // This will get all user current allocated space by SUM all binary bytes.
        $redzone_diskspace = 10737418240; // Set as Free Space banned zone to keep safety free space
        $path = __DIR__;
        $free_bytes = @disk_free_space($path);
        if ($free_bytes === false) {
            $session_master_left = 0;
        } else {
            $final_bytes = $free_bytes - $redzone_diskspace - $Alluserstorage;
            if ($final_bytes < 0) $final_bytes = 0; // If thing goes minus, make it zero.
            $session_master_left = $final_bytes;
        }
        if (isset($_POST['reqact'])) {
            $requestaction = $_POST['reqact'];
            switch ($requestaction){
                case 'userupdate':
                    $ID_USER = isset($_POST['user_id']) ? $_POST['user_id'] : '';
                    $password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
                    $comment = isset($_POST['row_comment']) ? $_POST['row_comment'] : '';
                    $storage_allocated = isset($_POST['storage']) ? $_POST['storage'] : '';
                    $editusername = isset($_POST['edit_username']) ? $_POST['edit_username'] : '';
                    $requestupdate = new user();
                    $currentuserquota = $requestupdate->get_user_info($ID_USER)->storage_allocated;
                    $userusedquota = $requestupdate->get_useralreadyusedrefund($ID_USER);
                    $realavailiable = $session_master_left + $currentuserquota; // Refund value
                    if (empty($editusername)) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "You can not leave username empty.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    if (is_numeric($storage_allocated)) {
                        if ($storage_allocated < 0) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Storage allocated cant be negative.");
                            header('location:../../index.php?req=user');
                            die;
                        }
                        if ($storage_allocated > $realavailiable) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "You dont have enough space in master space.");
                            header('location:../../index.php?req=user');
                            die;
                        }
                        // Prevent taked user current used byte.
                        if ($storage_allocated < $userusedquota) {
                            $storage_allocated = $userusedquota;
                        }
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Storage allocated must be number value.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    if (empty($password)) {
                        $result = $requestupdate->update_user_notpassword($editusername, $comment, $storage_allocated, $ID_USER);
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $result = $requestupdate->update_user_withpassword($editusername, $hashed_password, $comment, $storage_allocated, $ID_USER);
                    }
                    if ($result) {
                        header('location:../../index.php?req=user');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Could not update user.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    die;
                
                case 'adduser':
                    $hashed_password = password_hash('ilovenete', PASSWORD_DEFAULT);
                    $salt = bin2hex(random_bytes(10 / 2));
                    $username = isset($_POST['new_username']) ? $_POST['new_username'] : '';
                    $comment = isset($_POST['new_comment']) ? $_POST['new_comment'] : '';
                    $storage_allocated = isset($_POST['new_storage']) ? $_POST['new_storage'] : '';
                    if (empty($username)) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "You can not leave username empty.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    if (is_numeric($storage_allocated)) {
                        if ($storage_allocated < 0) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Storage allocated cant be negative.");
                            header('location:../../index.php?req=user');
                            die;
                        }
                        if ($storage_allocated > $session_master_left) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "You dont have enough space in master space.");
                            header('location:../../index.php?req=user');
                            die;
                        }
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Storage allocated must be number value.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    $requestadd = new user();
                    $result = $requestadd->add_user($username, $hashed_password, $salt, $comment, $storage_allocated);
                    if ($result) {
                        header('location:../../index.php?req=user');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Could not make new user.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    die;

                case 'userdel':
                    $ID_USER = isset($_POST['user_id']) ? $_POST['user_id'] : '';
                    $requestinfo = new user();
                    $userinfo = $requestinfo->user_Name($ID_USER)->username;
                    $getsalt = $requestinfo->user_salt($ID_USER)->salt;
                    $getuserdir = $userinfo . $getsalt;
                    $hashfinder = hash('sha256', $getuserdir);
                    $targetdir = "../../User_Data/" . $hashfinder . "/";
                    if (!$userinfo || !$getsalt) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "User dont exist.");
                        header('location:../../index.php?req=user');
                        die;
                    }
                    $result = $requestinfo->deleteuser($ID_USER);
                    if ($result) {
                        deleteDirectory($targetdir);
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "User deleted success!"); // Exception of using modal for success info :)
                        header('location:../../index.php?req=user');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Could not delete user."); // Exception of using modal for success info :)
                        header('location:../../index.php?req=user');
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
        header('location:../../index.php?req=user');
        die;
    }
} else {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
    header('location:../../index.php?req=user');
    die;
}
?>
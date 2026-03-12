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
                    if (empty($editusername)) {
                        echo "<script>alert('You can not leave username empty.');
                        window.location.href='../../index.php?req=user';
                        </script>";
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
                        echo "<script>alert('Could not update user');
                        window.location.href='../../index.php?req=user';
                        </script>";
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
                        echo "<script>alert('You can not leave username empty.');
                        window.location.href='../../index.php?req=user';
                        </script>";
                        die;
                    }
                    if (is_numeric($storage_allocated)) {
                        if ($storage_allocated < 0) {
                            echo "<script>alert('Storage allocated cant be negative.');
                            window.location.href='../../index.php?req=user';
                            </script>";
                            die;
                        }
                    } else {
                        echo "<script>alert('Storage allocated must be number value.');
                        window.location.href='../../index.php?req=user';
                        </script>";
                        die;
                    }
                    $requestadd = new user();
                    $result = $requestadd->add_user($username, $hashed_password, $salt, $comment, $storage_allocated);
                    if ($result) {
                        header('location:../../index.php?req=user');
                        die;
                    } else {
                        echo "<script>alert('Could not make new user');
                        window.location.href='../../index.php?req=user';
                        </script>";
                        die;
                    }
                    die;

                case 'userdel':
                    $ID_USER = isset($_POST['user_id']) ? $_POST['user_id'] : '';
                    $requestinfo = new user();
                    $userinfo = $requestinfo->user_Name($ID_USER)->username;
                    $getsalt = $requestinfo->user_salt($ID_USER)->salt;
                    $getuserdir = $userinfo . $getsalt;
                    $hashfinder = sha1($getuserdir);
                    $targetdir = "../../User_Data/" . $hashfinder . "/";
                    if (!$userinfo || !$getsalt) {
                        echo "<script>alert('User do not exist.');
                        window.location.href='../../index.php?req=user';
                        </script>";
                        die;
                    }
                    $result = $requestinfo->deleteuser($ID_USER);
                    if ($result) {
                        deleteDirectory($targetdir);
                        echo "<script>alert('User deleted success!');
                        window.location.href='../../index.php?req=user';
                        </script>";
                        die;
                    } else {
                        echo "<script>alert('Could not delete user');
                        window.location.href='../../index.php?req=user';
                        </script>";
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
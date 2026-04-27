<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

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
        require '../../Element/Database/trackcls.php';
        require '../../Element/Database/usercls.php';
        // Get SESSION user, And verify one also.
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
        if(isset($_POST['reqact'])) {
            $requestaction = $_POST['reqact'];
            switch ($requestaction) {
                case 'report_file':
                    $get_reason = isset($_POST['report_reason']) ? $_POST['report_reason'] : '';
                    $get_id = isset($_POST['report_reason']) ? $_POST['id_get'] : '';
                    // get_id should presenece. If it not. It's not send from the form
                    if (empty($get_id)) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Report Ejected. No ID found");
                        header('location:../../index.php?req=note');
                        die;
                    }
                    $reportin = new track();
                    $result = $reportin->reportin_file($get_reason, $get_id);
                    if ($result) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File repored!");
                        header('location:../../index.php?req=doc');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Cant report this file!");
                        header('location:../../index.php?req=doc');
                        die;
                    }
                
                case 'report_note':
                    $get_reason = isset($_POST['report_reason']) ? $_POST['report_reason'] : '';
                    $get_id = isset($_POST['id_get']) ? $_POST['id_get'] : '';
                    // get_id should presenece. If it not. It's not send from the form
                    if (empty($get_id)) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Report Ejected. No ID found");
                        header('location:../../index.php?req=note');
                        die;
                    }
                    $reportin = new track();
                    $result = $reportin->reportin_note($get_reason, $get_id);
                    if ($result) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Note repored!");
                        header('location:../../index.php?req=note');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Cant report this note!");
                        header('location:../../index.php?req=note');
                        die;
                    }
                // Delete Case
                case 'deletelistandfile':
                    $get_type_item = isset($_POST['item_type']) ? $_POST['item_type'] : '';
                    $get_item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
                    // Get the file_link
                    $locdir = new track();
                    // Delete the file then anything assoc
                    if ($get_type_item === 'file') {
                        $getidlink = $locdir->get_file_link($get_item_id);
                        if (!$getidlink) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File Not Found! Clean List Instead Please?");
                            header('location:../../index.php?req=track');
                            die;
                        }
                        $todir = $getidlink->file_link;
                        $delete_dir = "../." . $todir;
                        if (file_exists($delete_dir) && !unlink($delete_dir)) {
                            // Deletion failure
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Could not delete the file!");
                            header('location:../../index.php?req=track');
                            die;
                        } else {
                            // File delete or already deleted, Proceed to remove report list!
                            $del = new track();
                            $result = $del->delete_report_gulity($get_type_item, $get_item_id);
                            if ($result) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "[SUCCESS] Gulity file deleted and clean up duplicate report!");
                                header('location:../../index.php?req=track');
                                die;
                            } else {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Fail to delete list!");
                                header('location:../../index.php?req=track');
                                die;
                            }
                        }
                    } else {
                        // Note deletion!
                        $del = new track();
                        $result = $del->delete_report_gulity($get_type_item, $get_item_id);
                        if ($result) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "[SUCCESS] Gulity note deleted and clean up duplicate report!");
                            header('location:../../index.php?req=track');
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Fail to delete list!");
                            header('location:../../index.php?req=track');
                            die;
                        }
                    }
                    die;
                    
                case 'deletelist':
                    $get_type_item = isset($_POST['item_type']) ? $_POST['item_type'] : '';
                    $get_item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
                    $del = new track();
                    $result = $del->delete_report($get_type_item, $get_item_id);
                    if ($result) {
                        header('location:../../index.php?req=track');
                        break;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Cant delete the report list!");
                        header('location:../../index.php?req=track');
                        break;
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
        header('location:../../index.php');
        die;
    }
} else {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
    header('location:../../index.php');
    die;
}
?>
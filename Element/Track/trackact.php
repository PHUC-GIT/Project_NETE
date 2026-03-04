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
        require '../../Element/Database/trackcls.php';
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
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Can't report this file!");
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
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Can't report this note!");
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
                            echo "<script>alert('File Not Found! Clean List Instead Please?');
                                window.location.href='../../index.php?req=track';
                                </script>";
                            die;
                        }
                        $todir = $getidlink->file_link;
                        $delete_dir = "../." . $todir;
                        if (file_exists($delete_dir) && !unlink($delete_dir)) {
                            // Deletion failure
                            echo "<script>alert('!ERROR!: Could not delete the file!');
                                window.location.href='../../index.php?req=track';
                                </script>";
                                die;
                        } else {
                            // File delete or already deleted, Proceed to remove report list!
                            $del = new track();
                            $result = $del->delete_report_gulity($get_type_item, $get_item_id);
                            if ($result) {
                                echo "<script>alert('Gulity file deleted and clean up duplicate report!');
                                window.location.href='../../index.php?req=track';
                                </script>";
                                die;
                            } else {
                                echo "<script>alert('Fail to delete list!');
                                window.location.href='../../index.php?req=track';
                                </script>";
                                die;
                            }
                        }
                    } else {
                        // Note deletion!
                        $del = new track();
                        $result = $del->delete_report_gulity($get_type_item, $get_item_id);
                        if ($result) {
                            echo "<script>alert('Gulity note deleted and clean up duplicate report!');
                            window.location.href='../../index.php?req=track';
                            </script>";
                            die;
                        } else {
                            echo "<script>alert('Fail to delete list!');
                            window.location.href='../../index.php?req=track';
                            </script>";
                            die;
                        }
                    }
                    die;

                    // THIS ONE BELOW IS OLD MODEL
                //     $del = new track();
                //     $result = $del->delete_report_gulity($get_type_item, $get_item_id);
                //     if ($result) {
                //         if ($get_type_item == 'file') {
                //             $addstring = "../.";
                //             $delete_dir = "{$addstring}" . "{$todir}";
                //             if (!file_exists($delete_dir)) {
                //                 echo "<script>alert('!ERROR!: Ehhh... This file do not exist!');
                //                 window.location.href='../../index.php?req=track';
                //                 </script>";
                //                 break;
                //             }
                //             if(!unlink($delete_dir)) {
                //                 echo "<script>alert('!ERROR!: Entry deleted but file can not!');
                //                 window.location.href='../../index.php?req=track';
                //                 </script>";
                //                 break; 
                //             } else {
                //                 echo "<script>alert('Successfully delete file and clear up report!');
                //                 window.location.href='../../index.php?req=track';
                //                 </script>";
                //                 break; 
                //             }
                //         } else {
                //             echo "<script>alert('Successfully delete note and clear up report!');
                //                 window.location.href='../../index.php?req=track';
                //                 </script>";
                //                 break; 
                //         }
                //     } else {
                //         echo "<script>alert('!FATAL ERROR!: Can't delete the guilty files or it report!');
                //         window.location.href='../../index.php?req=track';
                //         </script>";
                //         break;
                //     }
                // die;
                    
                case 'deletelist':
                    $get_type_item = isset($_POST['item_type']) ? $_POST['item_type'] : '';
                    $get_item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
                    $del = new track();
                    $result = $del->delete_report($get_type_item, $get_item_id);
                    if ($result) {
                        header('location:../../index.php?req=track');
                        break;
                    } else {
                        echo "<script>alert('!FATAL ERROR!: Can't delete the report list!');
                        window.location.href='../../index.php?req=track';
                        </script>";
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
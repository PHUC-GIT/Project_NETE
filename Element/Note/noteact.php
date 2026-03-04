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
        require '../../Element/Database/notecls.php';
        require "../../Element/Database/reportcls.php";
        $requestaction = $_POST['reqact'] ?? null;
        if($requestaction) { 
            switch ($requestaction) {
                case 'addnote':
                    $text_content = "";
                    $tag = "card_default";
                    $requestadd = new index();
                    $Get_Note_Num = $requestadd->countnote();
                    if ($Get_Note_Num >= 100) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Maximum note capacity reached! Please remove some unimportant note.");
                        header('location:../../index.php?req=note');
                        die;
                    }

                    $result = $requestadd->addnote($text_content, $default_value=0, $tag);
                    if ($result) {
                        header('location:../../index.php?req=note');
                        die;
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Can't create new note!");
                        header('location:../../index.php?req=note');
                        die;
                    }
                    break;
                
                case 'ajax_updatenote':
                    $text_content = isset($_POST['text_content_edit']) ? $_POST['text_content_edit'] : '';
                    $tag = isset($_POST['tag']) ? $_POST['tag'] : '';
                    $tag_public = isset($_POST['tag_public']) ? $_POST['tag_public'] : 0;
                    $getidnote = isset($_POST['id_get']) ? $_POST['id_get'] : '';
                    // Tag checker
                    $taglist = array('card_default', 'card_red', 'card_blue', 'card_green', 'card_yellow', 'card_purple');
                    if (!in_array($tag, $taglist)) {
                        echo json_encode(['success' => false, 'message' => 'Invalid color.', 'new_csrf_token' => $regen_csrf]);
                        exit;
                    }
                    $requestupdate = new index();
                    $result = $requestupdate->updatenote($text_content, $tag_public, $tag, $getidnote);
                    if ($result) {
                        echo json_encode(['success' => true, 'new_csrf_token' => $regen_csrf]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Save failed.', 'new_csrf_token' => $regen_csrf]);
                    }
                    die;
                
                case 'deletenote':
                    $getidnote = isset($_POST['id_get']) ? $_POST['id_get'] : '';
                    $requestdelete = new index();
                    $result = $requestdelete->deletenote($getidnote);
                    if ($result) {
                        header('location:../../index.php?req=note');
                    } else {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Can't delete your note!");
                        header('location:../../index.php?req=note');
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
        header('location:../../index.php?req=note');
        die;
    }
} else {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
    header('location:../../index.php?req=note');
    die;
}
?>
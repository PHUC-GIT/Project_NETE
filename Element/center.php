<?php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }

    if(isset($_GET['req'])){
        $request=$_GET['req'];
        switch ($request) {
            // Main Page
            case 'home':
                require "./Element/home.php";
                break;
            case 'utility':
                require "./Element/utility.php";
                break;
            case 'help':
                require "./Element/help.php";
                break;
            case 'preference':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/preference.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'doc':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/doc.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'note':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/note.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'track':
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/track.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'user':
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/user.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            
            // Utility_Path
            case 'net':
                require "./Element/Utility_path/net.php";
                break;
            case 'browser':
                require "./Element/Utility_path/browser.php";
                break;
            case 'link':
                require "./Element/Utility_path/link.php";
                break;
            case 'indirect':
                require "./Element/Utility_path/indirect.php";
                break;
            case 'draw':
                require "./Element/Utility_path/draw.php";
                break;

            // file_fun_path
            case 'upload_file':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/uploader_file.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'create_folder':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/create_folder.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'create_file':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/create_file.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'edittext_file':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/edit_text.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'folder_update':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/folder_update.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'view':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/file_view.php";
                } else {
                    require "./Element/fool.php";
                }       
                break;
            case 'docshare':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/file_share.php";
                } else {
                    require "./Element/fool.php";
                }         
                break;
            case 'file_update':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/file_update.php";
                } else {
                    require "./Element/fool.php";
                }          
                break;
            case 'file_report':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/file_path/report_file.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            
            // help_path
            case 'privacy':
                require "./Element/help_path/privacy_info.php";
                break;
            case 'about':
                require "./Element/help_path/about_info.php";
                break;
            
            // note_path
            case 'note_public':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/note_path/public_note.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'note_report':
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])){
                   require "./Element/note_path/report_note.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            
            // User_path
            case 'useredit':
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/user_path/user_update.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            
            // track_path
            case 'report_eye_note':
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/track_path/reviewreportnote.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'report_eye_file':
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/track_path/reviewreportfile.php";
                } else {
                    require "./Element/fool.php";
                }
                break;
            case 'report_view':
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])){
                    require "./Element/track_path/reportview.php";
                } else {
                    require "./Element/fool.php";
                }
                break;

            // Go to fool
            default:
                require "./Element/fool.php";
                break;
        }
    } else {
        require "./Element/default.php";
    }
    exit;
?>
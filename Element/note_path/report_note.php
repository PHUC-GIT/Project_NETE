<!DOCTYPE html>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../../Index.php');
        die;
    }

    require "./Element/Database/trackcls.php";
    if (!isset($_GET['reportid'])) {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No context have been given.");
        echo "<script>window.location.href='index.php?req=note';</script>";
        die;
    }
    $getidfile = urldecode($_GET['reportid'] ?? '');
?>
<html lang="en">
    <style>
        h1, p, a {
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        
        #notehere {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
        }
        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .outline {
            border: 1px solid #ff9900; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(255, 153, 0, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
            z-index: 100;
        }

        .btn_card {
            padding: 7px;
            border: 3px solid #ff9900;
            background-color: #ff9900;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff9900;
        }

        .btn_card.pointer {
            cursor: pointer;
        }
    </style>
    <div>
        <div class="outline">
            <h1>REPORT DATA</h1>
            <button class="btn_card pointer" onclick="history.back()">Return</button>
        </div>
    </div>
        <div align="center" class="text_container">
            <form name="update_files" id="actionform_PDF" method="post" action="Element/Track/trackact.php?reqact=report_note">
                <input type="hidden" name="reqact" value="report_note"/>
                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>    
                <h1>SUSPECTION REPORT</h1>
                <p></p>
                <input type="hidden" name="id_get" value="<?php echo $getidfile ?? '';?>">
                <p>Reason</p>
                <textarea name="report_reason" id="text_content" placeholder="Write your reason here..." rows="10" cols="33"></textarea>
                <p></p>
                <input id="btn_upload" type="submit" value="REPORT"/>
                <p></p>
            </form>
        </div>
</html>
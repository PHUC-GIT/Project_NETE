<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}

require "./Element/Database/filecls.php";
require "./Element/Database/reportcls.php";
if (!isset($_GET['idview'])) {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No context have been given.");
    echo "<script>window.location.href='index.php?req=doc';</script>";
    die;
}
$getidfile = urldecode($_GET['idview'] ?? '');
$obj=new index();
$report=new report();
$get_info = $obj->getinfo($getidfile);
$get_type = $get_info->mime_type ?? '';
$img_type = array("image/jpeg", "image/vnd.microsoft.icon", "image/png", "image/x-icon");
$video_type = array("video/mp4");
$audio_type = array("audio/mpeg", "audio/ogg");
$PDF_type = array("application/pdf");
$text_type = array("text/plain");
$actual_path = $get_info->file_link ?? '';

// Add File to quick access
// If the list already have one? Delete that right there and readd later
if (isset($_SESSION['QUICK_ACCESS_FILE'][$getidfile])) {
    unset($_SESSION['QUICK_ACCESS_FILE'][$getidfile]);
}
$_SESSION['QUICK_ACCESS_FILE'][$getidfile] = [
    "File_Name" => $get_info->file_name,
    "ID" => $getidfile,
    "Mime_Type" => $get_type,
    "Type" => $get_info->type,
    "Size" => $get_info->size ?? 0,
];
// If the list over 10 then remove the oldest one.
if (count($_SESSION['QUICK_ACCESS_FILE']) > 10) {
    array_shift($_SESSION['QUICK_ACCESS_FILE']);
}

// Check if it exist
if (!file_exists($actual_path)) {
    // Delete quick access entry from the sidebar
    if (isset($_SESSION['QUICK_ACCESS_FILE'][$getidfile])) {
        unset($_SESSION['QUICK_ACCESS_FILE'][$getidfile]);
    }
    $obj->redtrigger($getidfile);
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Error! Can not access file!");
    echo "<script>window.location.href='index.php?req=doc';</script>";
    die;
}
// If not public or owned files
if (!$get_info) {
    // Delete quick access entry from the sidebar
    if (isset($_SESSION['QUICK_ACCESS_FILE'][$getidfile])) {
        unset($_SESSION['QUICK_ACCESS_FILE'][$getidfile]);
    }
    $user = $name_login ?? '';
    $cause = "Unauthorized access ID files!";
    $report->report_in($cause, $user);
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Unable to fetch this file. Context Invalid.");
    echo "<script>window.location.href='index.php?req=doc';</script>";
    die;
}
function streamTextToOutput($filepath) {
    $fp = @fopen($filepath, 'r');
    if (!$fp) {
        echo "ERROR: Could not access file due to system permissions.";
        return;
    }

    // Start streaming directly to the output buffer
   while (($line = fgets($fp)) !== false) {
        echo XSS($line); 
        flush();
    }
    fclose($fp);
    return; 
}
?>
<!DOCTYPE html>
<html lang="en">
    <!-- Local CSS -->
    <style>
        h1, h4, p, a {
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .headertext {
            color: white;
            font-weight: bold;
            font-family: 'Roboto', sans-serif;
            white-space: nowrap;
            margin: 0;
            font-size: 1.3rem;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: default;
        }

        .smalltext {
            color: white;
            font-family: 'Roboto', sans-serif;
            white-space: nowrap;
            margin: 0;
            font-size: 1.0rem;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: default;
        }

        .outline {
            border: 1px solid #0081d6; 
            padding: 4px; 
            border-radius: 15px 15px 0px 0px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(0, 128, 214, 0.3);
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
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            transition: 0.1s;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #0081d6;
        }

        .view_value {
            border: none;
            border-radius: 7px;
            width: 99%;
            height: 74vh;
            background: white;
        }

        .view_value_text {
            border: none;
            border-radius: 7px;
            width: 97%;
            height: 73vh;
            max-height: 73vh;
            padding: 10px;
            overflow-y: auto;
            overflow-x: hidden;
            text-wrap: auto;
            word-wrap: break-word;
            background-color: rgba(0, 0, 0, 0.5);
            scrollbar-color: #0081d6 transparent;
            border: 1px solid gray;
            text-align: left;
            font-family: 'Roboto', sans-serif;
            color: white;
            font-size: 15px;
            outline: none;
            resize: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        .file_view {
            margin-top: 15px;
        }

        .Info {
            fill: white;
            cursor: pointer;
        }

        .Info:hover {
            fill: #7fcfff;
        }
        
        /* Below Frame */
        .Screen {
            margin-top: 97px;
            margin-bottom: 0px;
            /* Special for anything that have more than 1 header frame */

        }

        .subframe {
            border: 1px solid #0081d6; 
            padding: 4px; 
            border-radius: 0px 0px 15px 15px;
            position: fixed;
            top: 45px;
            left: 103px;
            right: 4px;
            background-color:rgba(0, 128, 214, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
            z-index: 100;
        }

        .cross_button_header {
            display: flex;
            border: none;
            background: transparent;
            fill: white;
            border-radius: 5px;
            transition: 0.1s; 
            cursor: pointer;
        }

        .cross_button_header:hover {
            background: white;
            fill: #0081d6;
        }

    </style>
    <div>
        <div class="outline">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <svg style="margin-left: 5px;" width="32" height="32" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" stroke-linecap="round" stroke-linejoin="round"><path d="m4.4023 1.4492c-.83832 0-1.5234.68512-1.5234 1.5234v26.381c-1e-7.83832.68442 1.5254 1.5234 1.5254h23.113c.83902 0 1.5234-.68707 1.5234-1.5254v-20.695a.35003.35003 0 00-.125-.26953l-8.209-6.8594a.35003.35003 0 00-.22461-.080078zm0 .69922h15.949l7.9883 6.6738v20.531c0 .46325-.36166.82617-.82422.82617h-23.113c-.46255 0-.82422-.36292-.82422-.82617v-26.381c0-.46325.36096-.82422.82422-.82422z" stop-color="#000000"/><g><path d="m28.69 8.6582-8.209-6.8594v5.6855c0 .65079.52304 1.1738 1.1738 1.1738z" stop-color="#000000"/><path d="m20.705 1.5293a.35003.35003 0 00-.57422.26953v5.6855c0 .83832.68512 1.5234 1.5234 1.5234h7.0352a.35003.35003 0 00.22461-.61914zm.125 1.0176 6.8965 5.7617h-6.0723c-.46325 0-.82422-.36096-.82422-.82422z" stop-color="#000000"/></g><g transform="matrix(.65322 0 0 .65322 7.5736 9.6007)" stroke-width="2"><path d="m12 3c-7.7619 0-11.895 8.5527-11.895 8.5527a1.0001 1.0001 0 000 .89453s4.1326 8.5527 11.895 8.5527c7.7619 0 11.895-8.5527 11.895-8.5527a1.0001 1.0001 0 000-.89453s-4.1326-8.5527-11.895-8.5527zm0 2c5.8634 0 9.3477 6.2096 9.7793 7-.43163.79042-3.9159 7-9.7793 7-5.8634 0-9.3477-6.2096-9.7793-7 .43163-.79042 3.9159-7 9.7793-7z" stop-color="#000000"/><path d="m12 8c-2.1973 0-4 1.8027-4 4 0 2.1973 1.8027 4 4 4 2.1973 0 4-1.8027 4-4 0-2.1973-1.8027-4-4-4zm0 2c1.1164 0 2 .88359 2 2s-.88359 2-2 2-2-.88359-2-2 .88359-2 2-2z" stop-color="#000000"/><path d="m11.952 9.6238c-.96218-.00277-1.8652.65296-2.1905 1.5533-.18103.4852-.18351 1.037-.040709 1.5322.31175.97881 1.2967 1.7382 2.342 1.6601 1.0372.0097 1.9939-.77334 2.2406-1.7717.32032-1.109-.32724-2.3901-1.404-2.8012-.29983-.12092-.62436-.17814-.94737-.17271z"/></g></g></svg>
            </div>
            <div class="headertext" style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <a><?php echo XSS($get_info->file_name);?></a>
            </div>
            <div style="display: flex; justify-content: right; align-items: center;">
                <button class="cross_button_header" onclick="window.location.href='index.php?req=doc';"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
            </div>
       </div>
       <div class="subframe">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px; fill: white; margin-left: 5px;">
                <button class="cross_button_header" onclick="window.location.href='Element/view/viewware.php?secure=download_file&fileid=<?php echo XSS($getidfile);?>';"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 8zM4 19h16v2H4z"/></svg></button>
            </div>
            <div class="smalltext">
                <a>Read Only</a>
            </div>
        </div>
    </div>
        <div align="center" class="file_view">
            <?php
            if (in_array($get_type, $video_type)) {
                ?>
                <video class="view_value" controls>
                    <source src="Element/view/viewware.php?secure=view_media&fileid=<?php echo XSS($getidfile);?>" type="<?php echo XSS($get_type);?>">
                    Your browser does not support the video player element.
                </video>
                <?php
            }

            elseif (in_array($get_type, $audio_type)) {
                ?>
                <audio class="view_value" controls>
                    <source src="Element/view/viewware.php?secure=view_media&fileid=<?php echo XSS($getidfile);?>" type="<?php echo XSS($get_type);?>">
                    Your browser does not support the audio element.
                </audio>
                <?php
            }
            elseif (in_array($get_type, $img_type)) {
                ?>
                    <img class="view_value" src="Element/view/viewware.php?secure=view_file&fileid=<?php echo XSS($getidfile);?>">
                <?php
            }
            elseif (in_array($get_type, $PDF_type)) {
                ?>
                    <object class="view_value" data="Element/view/viewware.php?secure=view_file&fileid=<?php echo XSS($getidfile);?>"></object>
                <?php
            }
            elseif (in_array($get_type, $text_type)) {
                ?>
                    <pre class="view_value_text"><?php streamTextToOutput($actual_path);?></pre>
                <?php
            }
            else {
                ?>
                <a>Sorry. The given file don't supported.</a>
                <?php
            }
            ?>
        </div>
</html>
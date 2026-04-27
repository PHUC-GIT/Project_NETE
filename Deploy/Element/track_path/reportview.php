<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}

require "./Element/Database/trackcls.php";
if (!isset($_GET['idview'])) {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No context have been given.");
    header('location:./index.php?req=track');
    die;
}
$getidfile = urldecode($_GET['idview'] ?? '');
$obj=new track();
$get_info = $obj->getinfo_spy($getidfile);
$get_type = $get_info->mime_type;
$img_type = array("image/jpeg", "image/vnd.microsoft.icon", "image/png", "image/x-icon");
$video_type = array("video/mp4");
$audio_type = array("audio/mpeg", "audio/ogg");
$PDF_type = array("application/pdf");
$text_type = array("text/plain");
$actual_path = $get_info->file_link ?? '';
// Check if it exist
if (!file_exists($actual_path)) {
    $obj->redtrigger($getidfile);
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Error! Can not access file!");
    echo "<script>window.location.href='index.php?req=report_eye_file';</script>";
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
            border: 1px solid #ff0062; 
            padding: 4px; 
            border-radius: 15px 15px 0px 0px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(255, 0, 98, 0.3);
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
            border: 3px solid #ff0062;
            background-color: #ff0062;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff0062;
        }

        .view_value {
            border: none;
            border-radius: 7px;
            width: 99%;
            height: 74vh;
            background: transparent;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        .view_value_image {
            border: none;
            border-radius: 7px;
            max-width: 99%;
            max-height: 74vh;
            overflow-x: scroll;
            overflow-y: scroll;
            scrollbar-color: #ff0062 transparent;
            background: transparent;
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
            background-color: rgba(0, 0, 0, 0.5);
            scrollbar-color: #ff0062 transparent;
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

        /* Below Frame */
        .Screen {
            margin-top: 97px;
            margin-bottom: 0px;
            /* Special for anything that have more than 1 header frame */

        }

        .subframe {
            border: 1px solid #ff0062; 
            padding: 4px; 
            border-radius: 0px 0px 15px 15px;
            position: fixed;
            top: 45px;
            left: 103px;
            right: 4px;
            background-color:rgba(255, 0, 98, 0.3);
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
            cursor: pointer;
        }

        .cross_button_header:hover {
            background: rgba(255, 0, 98, 0.5);
        }

        .icons_button_style {
            display: flex;
            border: none;
            background: transparent;
            fill: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .icons_button_style:hover {
            background: #ff006280;
        }

        .icons_button_style_disable {
            display: flex;
            border: none;
            background: transparent;
            fill: #ffffff80;
            border-radius: 5px;
            cursor: default;
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
                <button class="cross_button_header" onclick="window.location.href='index.php?req=report_eye_file';"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
            </div>
       </div>
       <div class="subframe">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px; fill: white; margin-left: 5px;">
                <button class="icons_button_style" onclick="window.location.href='Element/view/viewinsigh.php?secure=download_file&fileid=<?php echo XSS($getidfile);?>';"><svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24"><path d="M19 9h-4V3H9v6H5l7 8zM4 19h16v2H4z"/></svg></button>
                <?php
                    if (in_array($get_type, $text_type)) {
                        ?>
                            <button title="Make this text bigger" class="icons_button_style" onclick="changeSize(5)"><svg width="25" height="25" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g stroke-width=".03125"><path d="m2.1385 7.9531c-.54969 1.4263-1.1223 2.8498-1.6558 4.2785.35598.06897.7363.01661 1.1011.03339.22727-.000123.45455-.000246.68182-.00037.2314-.64031.46282-1.2806.69427-1.9209h3.46c.24671.64082.49371 1.2815.74164 1.9219.61279-.002571 1.2259.006177 1.8385-.007935-1.1295-2.8374-2.2666-5.6718-3.4-8.5077h-1.8235c-.54598 1.401-1.092 2.8021-1.6379 4.2031zm2.5181-2.2274c.42342 1.0408.81992 2.0951 1.1872 3.1571-.38844.069931-.80454.023531-1.2032.039006-.38001-.0041237-.76178.0050238-1.1406-.019597.3735-1.0628.75811-2.1236 1.1566-3.1765z" style="paint-order:stroke fill markers"/><path d="m11.844 6.1875v1.1094h-2.25v1.4992c.74479.0055 1.4896.010977 2.2344.016418.0054.74479.01091 1.4896.01642 2.2344h1.468v-2.25h2.25v-1.5h-2.25v-2.2188h-1.4688v1.1094z" style="paint-order:stroke fill markers"/></g></svg></button>
                            <button title="Make this text smaller" class="icons_button_style" onclick="changeSize(-5)"><svg width="25" height="25" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g stroke-width=".03125"><path d="m2.1385 7.9531c-.54969 1.4263-1.1223 2.8498-1.6558 4.2785.35598.06897.7363.01661 1.1011.03339.22727-.000123.45455-.000246.68182-.00037.2314-.64031.46282-1.2806.69427-1.9209h3.46c.24671.64082.49371 1.2815.74164 1.9219.61279-.002571 1.2259.006177 1.8385-.007935-1.1295-2.8374-2.2666-5.6718-3.4-8.5077h-1.8235c-.54598 1.401-1.092 2.8021-1.6379 4.2031zm2.5181-2.2274c.42342 1.0408.81992 2.0951 1.1872 3.1571-.38844.069931-.80454.023531-1.2032.039006-.38001-.0041237-.76178.0050238-1.1406-.019597.3735-1.0628.75811-2.1236 1.1566-3.1765z" style="paint-order:stroke fill markers"/><path d="m10.5 8v.98438h3.9062v-1.9688h-3.9062v.98438z" style="paint-order:stroke fill markers"/></g></svg></button>
                        <?php
                    } else {
                        ?>
                            <button title="Disabled. Incompatible content" class="icons_button_style_disable"><svg width="25" height="25" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g stroke-width=".03125"><path d="m2.1385 7.9531c-.54969 1.4263-1.1223 2.8498-1.6558 4.2785.35598.06897.7363.01661 1.1011.03339.22727-.000123.45455-.000246.68182-.00037.2314-.64031.46282-1.2806.69427-1.9209h3.46c.24671.64082.49371 1.2815.74164 1.9219.61279-.002571 1.2259.006177 1.8385-.007935-1.1295-2.8374-2.2666-5.6718-3.4-8.5077h-1.8235c-.54598 1.401-1.092 2.8021-1.6379 4.2031zm2.5181-2.2274c.42342 1.0408.81992 2.0951 1.1872 3.1571-.38844.069931-.80454.023531-1.2032.039006-.38001-.0041237-.76178.0050238-1.1406-.019597.3735-1.0628.75811-2.1236 1.1566-3.1765z" style="paint-order:stroke fill markers"/><path d="m11.844 6.1875v1.1094h-2.25v1.4992c.74479.0055 1.4896.010977 2.2344.016418.0054.74479.01091 1.4896.01642 2.2344h1.468v-2.25h2.25v-1.5h-2.25v-2.2188h-1.4688v1.1094z" style="paint-order:stroke fill markers"/></g></svg></button>
                            <button title="Disabled. Incompatible content" class="icons_button_style_disable"><svg width="25" height="25" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g stroke-width=".03125"><path d="m2.1385 7.9531c-.54969 1.4263-1.1223 2.8498-1.6558 4.2785.35598.06897.7363.01661 1.1011.03339.22727-.000123.45455-.000246.68182-.00037.2314-.64031.46282-1.2806.69427-1.9209h3.46c.24671.64082.49371 1.2815.74164 1.9219.61279-.002571 1.2259.006177 1.8385-.007935-1.1295-2.8374-2.2666-5.6718-3.4-8.5077h-1.8235c-.54598 1.401-1.092 2.8021-1.6379 4.2031zm2.5181-2.2274c.42342 1.0408.81992 2.0951 1.1872 3.1571-.38844.069931-.80454.023531-1.2032.039006-.38001-.0041237-.76178.0050238-1.1406-.019597.3735-1.0628.75811-2.1236 1.1566-3.1765z" style="paint-order:stroke fill markers"/><path d="m10.5 8v.98438h3.9062v-1.9688h-3.9062v.98438z" style="paint-order:stroke fill markers"/></g></svg></button>
                        <?php
                    }
                ?>
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
                    <source src="Element/view/viewinsigh.php?secure=view_media&fileid=<?php echo XSS($getidfile);?>" type="<?php echo XSS($get_type);?>">
                    Your browser does not support the video player element.
                </video>
                <?php
            }

            elseif (in_array($get_type, $audio_type)) {
                ?>
                <audio class="view_value" controls>
                    <source src="Element/view/viewinsigh.php?secure=view_media&fileid=<?php echo XSS($getidfile);?>" type="<?php echo XSS($get_type);?>">
                    Your browser does not support the audio element.
                </audio>
                <?php
            } 
            elseif (in_array($get_type, $img_type)) {
                ?>
                    <img class="view_value_image" src="Element/view/viewinsigh.php?secure=view_file&fileid=<?php echo XSS($getidfile);?>">
                <?php
            }
            elseif (in_array($get_type, $PDF_type)) {
                ?>
                    <object class="view_value" data="Element/view/viewinsigh.php?secure=view_file&fileid=<?php echo XSS($getidfile);?>"></object>
                <?php
            }
            elseif (in_array($get_type, $text_type)) {
                ?>
                    <pre id="text_view" class="view_value_text"><?php streamTextToOutput($actual_path)?></pre>
                <?php
            }
            else {
                ?>
                <a>Sorry. The given file don't supported.</a>
                <?php
            }
            ?>
        </div>
        <?php
        if (in_array($get_type, $text_type)) {
            ?>
                <script src="JS/TextControl.js" type="text/javascript"></script>
            <?php
        }
        ?>
</html>
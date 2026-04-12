<?php
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
$get_info = $obj->getinfo_edit($getidfile);
$get_type = $get_info->mime_type ?? '';
$ediable_content_type = array("text/plain");
$actual_path = $get_info->file_link ?? '';
$file_size = $get_info->size ?? 0;

// Add File to quick access
if (isset($_SESSION['QUICK_ACCESS_FILE'][$getidfile])) {
    unset($_SESSION['QUICK_ACCESS_FILE'][$getidfile]);
}
$_SESSION['QUICK_ACCESS_FILE'][$getidfile] = [
    "File_Name" => $get_info->file_name,
    "ID" => $getidfile,
    "Mime_Type" => $get_type,
    "Type" => $get_info->type,
    "Size" => $file_size,
];
// If the list over 10 then remove the oldest one.
if (count($_SESSION['QUICK_ACCESS_FILE']) > 10) {
    array_shift($_SESSION['QUICK_ACCESS_FILE']);
}

// Check if file is editable.
if (!in_array($get_type, $ediable_content_type)) {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Error! Given content can't be editable!");
    echo "<script>window.location.href='index.php?req=doc';</script>";
    die;
}

// Check if file is too big to edit.
if ($file_size >= 52428800) {
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File too big to edit.");
    echo "<script>window.location.href='index.php?req=doc';</script>";
    die;
}

// Check if file exist?
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
            padding: 3px;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 5px;
            transition: 0.1s;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #0081d6;
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
    <form name="update_file_edit" id="actionform_EDITTXT" method="post" action="Element/File/fileact.php">
        <input type="hidden" name="reqact" value="update_editfile"/>
        <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
        <input type="hidden" name="id_get" value="<?php echo XSS($getidfile);?>">
    <div>
       <div class="outline">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <svg style="margin-left: 5px;" width="32" height="32" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" stroke-linejoin="round"><path d="m4.4023 1.4492c-.83832 0-1.5234.68512-1.5234 1.5234v26.381c-1e-7.83832.68442 1.5254 1.5234 1.5254h23.113c.83902 0 1.5234-.68707 1.5234-1.5254v-20.695a.35003.35003 0 00-.125-.26953l-8.209-6.8594a.35003.35003 0 00-.22461-.080078zm0 .69922h15.949l7.9883 6.6738v20.531c0 .46325-.36166.82617-.82422.82617h-23.113c-.46255 0-.82422-.36292-.82422-.82617v-26.381c0-.46325.36096-.82422.82422-.82422z" stop-color="#000000" stroke-linecap="round"/><g><path d="m28.69 8.6582-8.209-6.8594v5.6855c0 .65079.52304 1.1738 1.1738 1.1738z" stop-color="#000000" stroke-linecap="round"/><path d="m20.705 1.5293a.35003.35003 0 00-.57422.26953v5.6855c0 .83832.68512 1.5234 1.5234 1.5234h7.0352a.35003.35003 0 00.22461-.61914zm.125 1.0176 6.8965 5.7617h-6.0723c-.46325 0-.82422-.36096-.82422-.82422z" stop-color="#000000" stroke-linecap="round"/><path d="m11.279 12.504a.17766.17766 0 00-.17773.16992l-.13477 2.6914a.17766.17766 0 00.17578.18555h.32227a.17766.17766 0 00.17773-.16406c.0379-.49811.15666-.87824.3418-1.1484.19233-.28069.41276-.45753.66797-.55078.18498-.06413.55466-.10742 1.0801-.10742h1.2031v8.5898c0 .6318-.08791 1.0354-.15625 1.1348-.16553.23809-.43617.36328-.88477.36328h-.40625a.17766.17766 0 00-.17773.17773v.3125a.17766.17766 0 00.17773.17773h4.8652a.17766.17766 0 00.17773-.17773v-.3125a.17766.17766 0 00-.17773-.17773h-.39648c-.41313 0-.68404-.10435-.85742-.29492l-.002-.002c-.03736-.04366-.09181-.16999-.12695-.37305-.03537-.20434-.05469-.48187-.05469-.82812v-8.5898h1.4375c.45422 0 .8069.07666 1.0586.21484a.17766.17766 0 00.002.002c.26005.13502.46806.34358.62891.64453.08719.1652.17359.49094.23438.95508a.17766.17766 0 00.17578.1543h.32227a.17766.17766 0 00.17773-.18555l-.12695-2.6914a.17766.17766 0 00-.17774-.16992zm.16797.35547h9.0312l.10938 2.334c-.059279-.37958-.12393-.7124-.23242-.91797-.18851-.35269-.44977-.62323-.77539-.79297-.3236-.17722-.73362-.25781-1.2266-.25781h-1.6152a.17766.17766 0 00-.17773.17774v8.7676c0 .35904.01779.65291.05859.88867.04081.23576.09789.41679.21289.54883a.17766.17766 0 00.002.002c.22886.25294.59528.34633 1.0059.37109h-3.8477c.46148-.02576.85926-.15369 1.0801-.47266a.17766.17766 0 000-.002c.17992-.2617.21875-.68128.21875-1.3359v-8.7676a.17766.17766 0 00-.17774-.17774h-1.3809c-.54475 0-.93409.03543-1.2012.12891a.17766.17766 0 00-.002 0c-.3316.12116-.61268.35491-.83789.68359-.20326.29665-.29799.70716-.35352 1.1582h-.0078z" stop-color="#000000" style="paint-order:stroke fill markers"/><path d="m20.648 12.682.12695 2.6914h-.32161q-.0931-.71094-.25391-1.0156-.26237-.49088-.70247-.7194-.43164-.23698-1.1426-.23698h-1.6165v8.7682q0 1.0579.22852 1.3203.32161.35547.99023.35547h.39779v.31315h-4.8665v-.31315h.40625q.72786 0 1.0326-.4401.1862-.27083.1862-1.2357v-8.7682h-1.3796q-.80404 0-1.1426.11849-.4401.16081-.75325.61784-.31315.45703-.3724 1.2357h-.32161l.13542-2.6914z" stop-color="#000000" style="paint-order:stroke fill markers"/></g></g></svg></a>
            </div>
            <div class="headertext" style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <a><?php echo XSS($get_info->file_name);?></a>
            </div>
            <div style="display: flex; justify-content: right; align-items: center;">
                <button class="cross_button_header" type="button" onclick="window.location.href='index.php?req=doc';"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
            </div>
       </div>
       <div class="subframe">
            <div>
                <button class="btn_card" type="submit" id="save_btn">Save</button>
            </div>
            <div class="smalltext">
                <a>This text file only save if file size is lower than 50MB</a>
            </div>
       </div>
    </div>
    <div align="center" class="file_view">
        <textarea id="text_form" class="view_value_text" name="file_content"><?php echo streamTextToOutput($actual_path);?></textarea>
    </div>
    </form>
    <script>
        const form = document.getElementById('actionform_EDITTXT');
        const saveBtn = document.getElementById('save_btn');
        form.addEventListener('submit', function(){
            saveBtn.innerHTML = 'Saving...';
            saveBtn.disabled = true;
        });
    </script>
    <?php
    // Temporary solution. This will get one time use $_Session value from the save and will put current user scroll to the bottom.
    if (isset($_SESSION['SCROLLTOBOTTOM'])) {
        ?>
        <script>
            const textform = document.getElementById('text_form');
            textform.scrollTop = textform.scrollHeight;
        </script>
        <?php
        unset($_SESSION['SCROLLTOBOTTOM']);
    }
    ?>
</html>
<!DOCTYPE html>
<html lang="en">
    <?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    define('RECHECK_FILE_STATUS_TIMER', 30 * 24 * 60 * 60);
    $get_current_time = time();
    require "./Element/Database/filecls.php";
    // Predefine value
    $obj = new index();
    $get_ID = $_SESSION['AUTHENTICATE_USER'];
    $search_value = '';
    // The array with short tag assign.
    $file_icons_mime = array(
        'application/pdf' => './Resource/FormatIcons/PDF.png', // .pdf
        'text/plain' => './Resource/FormatIcons/Txt.png', // .txt
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => './Resource/FormatIcons/Word.png', // .docx
        'application/msword' => './Resource/FormatIcons/Word.png', // .doc
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => './Resource/FormatIcons/Powerpoint.png', // .pptx
        'application/vnd.ms-powerpoint' => './Resource/FormatIcons/Powerpoint.png', // .pot, .ppa, .pps, .ppt, .pwz
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => './Resource/FormatIcons/Xls.png', // .xlsx
        'application/vnd.ms-excel' => './Resource/FormatIcons/Xls.png', // .xla, .xlb, .xlc, .xlm, .xls, .xlt, .xlw
        'application/x-zip-compressed' => './Resource/FormatIcons/Zip.png', // .zip
        'application/zip' => './Resource/FormatIcons/Zip.png', // .zip
        'application/x-7z-compressed' => './Resource/FormatIcons/Zip.png', // .7z
        'application/x-rar-compressed' => './Resource/FormatIcons/Zip.png', // .rar
        'application/x-rar' => './Resource/FormatIcons/Zip.png', // .rar rare?
        'image/png' => './Resource/FormatIcons/Image.png', // .png
        'image/svg+xml' => './Resource/FormatIcons/Image.png', // .svg, .svgz
        'image/jpeg' => './Resource/FormatIcons/Image.png', // jpe, .jpeg, .jpg, .pjpg, .jfif, .jfif-tbnl, .jif
        'image/x-icon' => './Resource/FormatIcons/Image.png', // .ico
        'image/vnd.microsoft.icon' => './Resource/FormatIcons/Image.png', // .ico
        'application/vnd.sqlite3' => './Resource/FormatIcons/Sql.png', // .db, .sqlite, .sqlite3, .db-wal, .sqlite-wal, .db-shm, .sqlite-shm
        'audio/mpeg' => './Resource/FormatIcons/Music.png', // .m2a, .m3a, .mp2, .mp2a, .mp3, .mpga
        'audio/ogg' => './Resource/FormatIcons/Music.png', // .oga, .ogg, .spx
        'video/mp4' => './Resource/FormatIcons/Video.png', // .mp4, .mp4v, .mpg4
        'application/vnd.android.package-archive' => './Resource/FormatIcons/Apk.png', // .apk, ".xapk"
        'nete/folder' => './Resource/FormatIcons/Folder_Files.png', //This one is system specialist mime
    );

    $view_format_type = array(
        "application/pdf",
        "text/plain", 
        "audio/mpeg",
        "audio/ogg", 
        "video/mp4", 
        "image/jpeg", 
        "image/png", 
        "image/x-icon",
        "image/vnd.microsoft.icon",
    );

    // Folder and direction awareness.
    if (isset($_GET['direct'])) {
        if ($_GET['direct'] == "Root") {
            unset($_SESSION['Current_Folder']);
            $_SESSION['Folder_Array'] = array();
        } else if ($_GET['direct'] == "Up") {
            array_pop($_SESSION['Folder_Array']);
            $_SESSION['Current_Folder'] = end($_SESSION['Folder_Array']) ?: "NULL";
        } else {
            // Prevent add breadcrum on same id and previous id.
            if (in_array($_SESSION['Current_Folder'], $_SESSION['Folder_Array'])) {
                if (in_array($_GET['direct'], $_SESSION['Folder_Array'])) {
                } else {
                    // Prevent nonsense string
                    if (!preg_match('/^[a-fA-F0-9]{64}$/', $_GET['direct'])) {
                        unset($_SESSION['Current_Folder']);
                        $_SESSION['Folder_Array'] = array();
                        echo "<script>window.location.href='index.php?req=doc';</script>";
                    } else {
                        // Check if folder existed
                        $checkfolder = $obj->checkiffolderexist($_GET['direct']);
                        if(!$checkfolder) {
                            unset($_SESSION['Current_Folder']);
                            $_SESSION['Folder_Array'] = array();
                            echo "<script>window.location.href='index.php?req=doc';</script>";
                        } else {
                            $_SESSION['Current_Folder'] = $_GET['direct'];
                            $_SESSION['Folder_Array'][] = $_GET['direct'];
                        }
                    }
                }
            } else {
                // Prevent nonsense string. Again.
                if (!preg_match('/^[a-fA-F0-9]{64}$/', $_GET['direct'])) {
                    unset($_SESSION['Current_Folder']);
                    $_SESSION['Folder_Array'] = array();
                    echo "<script>window.location.href='index.php?req=doc';</script>";
                } else {
                    // Check if folder existed. Again.
                    $checkfolder = $obj->checkiffolderexist($_GET['direct']);
                    if(!$checkfolder) {
                        unset($_SESSION['Current_Folder']);
                        $_SESSION['Folder_Array'] = array();
                        echo "<script>window.location.href='index.php?req=doc';</script>";
                    } else {
                        $_SESSION['Current_Folder'] = $_GET['direct'];
                        $_SESSION['Folder_Array'][] = $_GET['direct'];
                    }
                }
            }
        }
    }
    // Check if it exist.
    if (isset($_SESSION['Current_Folder'])) {
    } else {
        $_SESSION['Current_Folder'] = "NULL";
        $_SESSION['Folder_Array'] = array();
    }
    $parent_folder_id = $_SESSION['Current_Folder'];
    // Search Detection
    if (isset($_GET['files_search'])) {
        $search_value = $_GET['files_search'];
        if (empty($search_value)) {
            $doc_list = $obj->listfile($get_ID, $parent_folder_id);
        } else {
            $doc_list = $obj->listfile_search($get_ID, $search_value);
        }
    } else {
        $doc_list = $obj->listfile($get_ID, $parent_folder_id);
    }
    $doc_count = count($doc_list);
    $total_size = $obj->get_totalsize();
    $countup = count($doc_list);
    $share_status = "default";
    $sort_1 = 'selected';
    $sort_2 = '';
    $sort_3 = '';
    if (isset($_GET["sort_tag"])) {
        $sort_data = $_GET["sort_tag"];
        // date sorter
        if ($sort_data == "sort_date") {
            $sort_2 = 'selected';
            $sort_1 = '';
            usort($doc_list, function($a, $b) {
                $date_A = strtotime($a->date);
                $date_B = strtotime($b->date);
                return $date_B - $date_A;
            });
        }
        // doc name sorter
        else if ($sort_data == "sort_name") {
            $sort_3 = 'selected';
            $sort_1 = '';
            usort($doc_list, function($a, $b) {
                return strcmp($a->file_name, $b->file_name);
            });
        }
        else {
            $sort_1 = 'selected';
        }
    }

    # List layout and image fixed
    $layout1 = 'selected';
    $layout2 = '';
    $layout_val = 'list-div';
    $image_val = 'img_inside_file_list';
    if (isset($_GET["layout_tag"])) {
        $layout_data = $_GET["layout_tag"];
        if ($layout_data == "Grid") {
            $layout2 = 'selected';
            $layout1 = '';
            $layout_val = 'flex-div';
            $image_val = 'img_inside_file';
        }
        else {
            $layout1 = 'selected';
        }
    }

    // Process of true storage
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    $get_user_storage = $obj->get_userlocated();
    $maximum_allowed = $get_user_storage->storage_allocated;
    $size_show = formatBytes($total_size);

    // Percent calculator
    function getPercentUsed($used, $max) {
        if ($max == 0) return 0;
        return round(($used / $max) * 100, 2);
    }
    $percent_used = getPercentUsed($total_size, $maximum_allowed);
    ?>
    <style>
        h1, h2, h4, p {
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .Card_Text {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 15px;
            cursor: default;
        }

        .outline {
            border: 1px solid #0081d6; 
            padding: 10px; 
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
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .btn_card {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .btn_search {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 0px 5px 5px 0px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        #Unique_search {
            padding-left: 10px;
            padding-right: 10px;
            border: 3px solid rgba(0, 128, 214, 0);
            background-color: rgba(0, 128, 214, 0.5);
            border-radius: 5px 0px 0px 5px;
            font-family: 'Roboto', sans-serif;
            color: white;
            font-size: 15px;
            font-weight: bold;
            transition: 0.1s;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        #Unique_search::placeholder {
            color: #0081d6;
        }

        .btn_search:hover {
            background-color: white;
            border: 3px solid white;
            color: #0081d6;
            fill: #0081d6;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #0081d6;
            fill: #0081d6;
        }

        .css_sort {
            background: #0081d6;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            color: white;
            transition: 0.1s;
            margin-left: 10px;
            cursor: pointer;
        }

        option {
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
        }

        .css_sort:hover, .css_sort:focus{
            background-color: white;
            color: #0081d6;
        }

        .Info {
            fill: white;
            cursor: pointer;
        }

        .Info:hover {
            fill: #7fcfff;
        }

        .List_Btn {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px 0px 0px 5px;
            margin-left: 10px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .Grid_Btn {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 0px 5px 5px 0px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .List_Btn:hover, .Grid_Btn:hover {
            background-color: white;
            border: 3px solid white;
            fill: #0081d6;
        }

        .List_Btn.selected, .Grid_Btn.selected {
          transition: none;
          border: 3px solid white;
          background-color: #0081d6;
          fill: white;
          cursor: default;
        }

        /* For breadcrum frame */

        .Screen {
            margin-top: 135px;
            /* Special for anything that have more than 1 header frame */

        }

        .subframe {
            border: 1px solid #0081d6; 
            padding: 8px; 
            border-radius: 0px 0px 15px 15px;
            position: fixed;
            top: 65px;
            left: 103px;
            right: 4px;
            background-color:rgba(0, 128, 214, 0.3);
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .btn_card_sub {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            fill: white;
            border: none;
            background-color: #0081d6;
            border-radius: 5px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-right: 5px;
            cursor: pointer;
        }

        .btn_card_sub:hover {
            background-color: white;
            color: #0081d6;
            fill: #0081d6;
        }

        .btn_card_sub_disable {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            fill: gray;
            border: none;
            background-color: #484848ff;
            border-radius: 5px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-right: 5px;
            cursor: not-allowed;
        }        

        .btn_card_sub_text {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 5px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .btn_card_sub_text:hover {
            background-color: white;
            border: 3px solid white;
            color: #0081d6;
            fill: #0081d6;
        }

        .breadcrum_section {
            padding-left: 10px;
            padding-right: 10px;
            padding-top: 5px;
            padding-bottom: 5px;
            max-width: 500px;
            height: 18px;
            border: 3px solid rgba(0, 128, 214, 0);
            background-color: rgba(0, 128, 214, 0.5);
            border-radius: 5px;
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-decoration: none;
            font-family: 'Roboto', sans-serif;
            transition: 0.1s;
            outline: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: default;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            /* The key properties for this effect */
            direction: rtl; 
            text-align: left;
        }

        .breadcrum_section a {
            direction: ltr;
            unicode-bidi: bidi-override;
        }

        .Card_File_Add {
            width: 50%;
            height: 250px;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            fill: gray;
            border: 1px solid gray;
            margin-top: 10px;
            margin-left: auto;
            margin-right: auto;
            transition: 0.1s;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(7px);
            backdrop-filter: blur(7px);
        }

    </style>
    <!-- File Async -->
        <script>
            function checkFileAsync(fileId) {
                const statusDiv = document.getElementById(`verify-status-${fileId}`);
                if (!statusDiv) return;

                const verifyUrl = `Element/verify_engine/verifyware.php?secure=hash_check&fileid=${fileId}`;

                fetch(verifyUrl).then(response =>{
                    if(!response.ok) {
                        throw new Error (`HTTP error! Status: ${response.status}`);
                    }
                    return response.json()
                })
                .then(data => {
                    statusDiv.innerHTML = `<img title="${data.title_status}" src="${data.icon_url}">`;
                })
                .catch(error => {
                    console.error(`Hash check to file ${fileId} error:`, error);
                    statusDiv.innerHTML = `<img title="Verification Error" src="Resource/shield_panic.png">`;
                });
            }
        </script>
    <div>
        <div class="outline">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <h1>MY FILES</h1><a title="Your Storage: <?php echo XSS($size_show);?> / <?php echo formatBytes($maximum_allowed);?>" class="Info"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg></a>
            </div>
            <?php
            if ($percent_used > 70) {
                ?>
                    <a class="alert_ico" title="Your storage going short! Consider clean up some space?"><img src="Resource\alert.png"></a>
                <?php
            }
            ?>
            <div>
                <form method="get" action="index.php" style="display: flex;">
                    <input type="hidden" name="req" value="doc">
                    <input type="hidden" name="layout_tag" value="<?php echo XSS($_GET["layout_tag"] ?? '');?>">
                    <input type="text" id="Unique_search" name="files_search" placeholder="Search Your Files" value="<?php echo XSS($search_value);?>">
                    <button type="submit" class="btn_search"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"/></svg></button>
                    <select onchange="this.form.submit()" id="sort_tag_id" name="sort_tag" class="css_sort dropdown_control"> 
                        <option value="default" <?php echo XSS($sort_1)?>>Default Sort</option>
                        <option value="sort_date" <?php echo XSS($sort_2)?>>Date Sort</option>
                        <option value="sort_name" <?php echo XSS($sort_3)?>>Name Sort</option>
                    </select>
                    <?php
                    if ($layout1 == 'selected') {
                        ?>
                            <button name="layout_tag" class="List_Btn <?php echo XSS($layout1)?>" disabled><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M4 6h2v2H4zm0 5h2v2H4zm0 5h2v2H4zm16-8V6H8.023v2H18.8zM8 11h12v2H8zm0 5h12v2H8z"/></svg></button>
                            <button type="submit" name="layout_tag" value="Grid" class="Grid_Btn"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm5 2h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1zm1-6h4v4h-4V5zM3 20a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6zm2-5h4v4H5v-4zm8 5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v6zm2-5h4v4h-4v-4z"/></svg></button>
                        <?php
                    } else {
                        ?>
                            <button type="submit" name="layout_tag" value="List" class="List_Btn"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M4 6h2v2H4zm0 5h2v2H4zm0 5h2v2H4zm16-8V6H8.023v2H18.8zM8 11h12v2H8zm0 5h12v2H8z"/></svg></button>
                            <button name="layout_tag" class="Grid_Btn <?php echo XSS($layout2)?>" disabled><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M10 3H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM9 9H5V5h4v4zm5 2h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1zm1-6h4v4h-4V5zM3 20a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6zm2-5h4v4H5v-4zm8 5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v6zm2-5h4v4h-4v-4z"/></svg></button>
                        <?php
                    }
                    ?>
                    <button type="button" class="btn_card" onclick="window.location.href='index.php?req=docshare';"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m4 3c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2v-12c0-1.103-.897-2-2-2h-9.5859l-1.707-1.707a.997.997 0 00-.70703-.29297h-4zm10.268 8.1738c.98191 0 1.7754.79348 1.7754 1.7754 0 .98191-.79348 1.7773-1.7754 1.7773-.98191 0-1.7773-.79544-1.7773-1.7773 0-.98191.79544-1.7754 1.7773-1.7754zm4.0605.76172c.84073 0 1.5234.68075 1.5234 1.5215 0 .84073-.68271 1.5234-1.5234 1.5234-.84073 0-1.5234-.68271-1.5234-1.5234 0-.84073.68271-1.5215 1.5234-1.5215zm-4.0605 3.5527c1.5419 0 2.791 1.2492 2.791 2.791v.095703c0 .36802-.298.66602-.66602.66602h-4.252c-.36802 0-.66602-.298-.66602-.66602v-.095703c0-1.5419 1.2511-2.791 2.793-2.791zm4.0605.25391c1.2611 0 2.2832 1.0221 2.2832 2.2832v.35547c0 .36484-.29336.66016-.6582.66016h-2.2969c.10469-.19828.16406-.4249.16406-.66602v-.095703c0-.81693-.27667-1.5684-.73828-2.168.3585-.23318.78766-.36914 1.2461-.36914z"/></svg>Public Files</button>
                </form>
            </div>    
        </div>
        <div class="subframe">
            <div>
                <form method="get" action="index.php" style="display: flex;">
                    <input type="hidden" name="req" value="doc">
                    <input type="hidden" name="sort_tag" value="<?php echo XSS($_GET["sort_tag"] ?? '')?>"> 
                    <input type="hidden" name="layout_tag" value="<?php echo XSS($_GET["layout_tag"] ?? '')?>">  
                    <button type="submit" title="Go Up To Root" class="btn_card_sub" name="direct" value="Root"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="m21.743 12.331-9-10c-.379-.422-1.107-.422-1.486 0l-9 10a.998.998 0 0 0-.17 1.076c.16.361.518.593.913.593h2v7a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-4h4v4a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-7h2a.998.998 0 0 0 .743-1.669z"/></svg></button>
                    <?php
                        if($_SESSION['Current_Folder'] == "NULL"){
                            ?>
                                <button type="button" title="Go Up One Level" class="btn_card_sub_disable" name="direct"><svg width="24" height="24" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m4 3c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2v-12c0-1.103-.897-2-2-2h-9.5859l-1.707-1.707a.997.997 0 00-.70703-.29297h-4zm8 4.6426 5.9121 5.9121h-3.4336v4.2793l-4.873-.083984v-4.1953h-3.5176l5.9121-5.9121z"/></svg></button>
                            <?php
                        } else {
                            ?>
                                <button type="submit" title="Go Up One Level" class="btn_card_sub" name="direct" value="Up"><svg width="24" height="24" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m4 3c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2v-12c0-1.103-.897-2-2-2h-9.5859l-1.707-1.707a.997.997 0 00-.70703-.29297h-4zm8 4.6426 5.9121 5.9121h-3.4336v4.2793l-4.873-.083984v-4.1953h-3.5176l5.9121-5.9121z"/></svg></button>
                            <?php
                        }
                    ?>
                    <div class="breadcrum_section">
                    <?php
                        if (!empty($search_value)) {
                            echo "<a>Search Result: " . XSS($search_value)."</a>";
                        } else {
                            echo '<a> '.XSS($name_login ?? '').' Drive</a>';
                            // If there are folders in the stack, show them
                            if (!empty($_SESSION['Folder_Array'])) {
                                $obj = new index();
                                $sep = "<a> &gt; </a>"; // HTML arrow separator
                                $path = '';
                                foreach ($_SESSION['Folder_Array'] as $fid) {
                                    // Get folder info
                                    $folder = $obj->get_folder_by_id($fid);
                                    if ($folder) {
                                        $path .= $sep . '<a>' . XSS($folder->file_name) . '</a>';
                                    }
                                }
                                echo $path;
                            }
                        }
                    ?>
                    </div>
                    <div class="breadcrum_section" style="margin-left: 5px;">
                        <a><?php echo XSS(Dynamic_Items_Counter($doc_count));?></a>
                    </div>
                </form>
            </div>
            <div style="display: flex;">
                <button type="button" class="btn_card_sub_text" onclick="window.location.href='index.php?req=create_folder';"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m4 3c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2v-12c0-1.103-.897-2-2-2h-9.5859l-1.707-1.707a.997.997 0 00-.70703-.29297h-4zm7.1074 5.332h.89258.88672v3.8281c.24749.07721.55422.012227.82226.035156h2.7109v1.9473h-3.502c-.07055.27084-.006334.6013-.027344.89453v2.9668h-1.7832v-3.8301c-.24749-.0772-.55033-.00832-.81836-.03125h-2.709v-1.9473h3.4961c.07054-.27083.01023-.60325.03125-.89648v-2.9668z"/></svg>New Folder</button>
                <?php
                if ($total_size < $maximum_allowed) {
                    ?>
                        <button type="button" class="btn_card_sub_text" onclick="window.location.href='index.php?req=create_file';"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5zm-1.9629 6.9102h.83789.83594v3.4902c.2325.07035.51773.008362.76953.029297h2.5469v1.7754h-3.2891c-.06627.2468-.007618.54921-.027343.81641v2.7031h-1.6738v-3.4902c-.2325-.07034-.51578-.008364-.76758-.029297h-2.5469v-1.7754h3.2871c.06627-.2468.007616-.54921.027343-.81641v-2.7031z"/></svg>New File</button>
                        <button type="button" class="btn_card_sub_text" onclick="window.location.href='index.php?req=upload_file';"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h3.6055v-3.8359h-3.5176l5.9121-5.9121 5.9121 5.9121h-3.4336v3.8359h3.5215a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5z"/></svg>Upload File</button>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
        <div class="<?php echo $layout_val?>">
            <?php
            if ($countup>0){
                foreach ($doc_list as $show){
                    if ($show->share == 1) {
                        $share_status = "Yes";
                    } else {
                        $share_status = "No";
                    }
                    ?>
                    <div class="Card">
                        <div>
                            <?php
                            $getfiletype = $show->mime_type;
                            $image_src = $file_icons_mime[$show->mime_type] ?? './Resource/FormatIcons/Unknow.png';
                            ?>
                            <?php
                            $quickview_img = array("image/jpeg", "image/png");
                            if ((in_array($getfiletype, $quickview_img)) && ($image_val == "img_inside_file")) {
                                ?>
                                <div title="Type: <?php echo XSS($show->type);?>" class="<?php echo XSS($image_val);?>"><img style="width: 66.2px; height: 66.2px;" loading="lazy" src="Element/view/viewware.php?secure=view_file&fileid=<?php echo XSS($show->id);?>"></div>
                                <?php
                            } else {
                                ?>
                                <div title="Type: <?php echo XSS($show->type);?>" class="<?php echo XSS($image_val);?>"><img src="<?php echo XSS($image_src);?>"></div>
                                <?php
                            }
                            ?>
                        </div>
                        <h4 title="<?php echo XSS($show->file_name);?>"><b><?php echo XSS($show->file_name);?></b></h4>
                        <?php
                        if ($layout_val == 'list-div') {
                            ?>
                                <p><?php echo $show->date?></p>
                            <?php
                        }
                        ?>
                        <?php if ($show->mime_type != "nete/folder") {
                            if ($layout_val == 'list-div') {
                                ?>
                                    <p>Share: <?php echo XSS($share_status)?></p>
                                    <p><?php echo formatBytes($show->size);?></p>
                                <?php
                            }
                            ?>
                            <div>
                                <?php
                                if (in_array($getfiletype, $view_format_type)) {
                                    ?>
                                        <button class="btnsmall" title="View" onclick="window.location.href='index.php?req=view&idview=<?php echo urlencode($show->id);?>';"><img src="Resource/Eye.png"></button>
                                        <button class="btnsmall" title="Download" onclick="window.location.href='Element/view/viewware.php?secure=download_file&fileid=<?php echo XSS($show->id);?>';"><img src="Resource/Download.png"></button>
                                    <?php
                                } else {
                                    ?>
                                        <button class="btnsmall" style="visibility: hidden;"></button> <!--This is dummy button to maintain frame in place-->
                                        <button class="btnsmall" title="Download" onclick="window.location.href='Element/view/viewware.php?secure=download_file&fileid=<?php echo XSS($show->id);?>';"><img src="Resource/Download.png"></button>
                                    <?php
                                }
                                ?>
                                <button class="btnsmall" title="Edit" onclick="window.location.href='index.php?req=file_update&idedit=<?php echo urlencode($show->id);?>';"><img src="Resource/Edit.png"></button>
                                <form id="fileDelete_<?php echo XSS($show->id);?>" action="./Element/File/fileact.php" method="post" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                                    <input type="hidden" name="reqact" value="deletefile"/>
                                    <input type="hidden" name="idfile" value="<?php echo urlencode($show->id);?>"/>
                                    <button type="button" class="btnsmall" title="Delete" onclick="showModalConfirm('Are you sure you want to delete this file? You will not able to recover it afterward.', function() {document.getElementById('fileDelete_<?php echo XSS($show->id);?>').submit();});"><img src="Resource/Delete.png"></button>
                                </form>
                                <p></p>
                            </div>
                                <?php
                        } else {
                            if ($layout_val == 'list-div') {
                                ?>
                                    <p title="This type don't support share feature.">Share: N/A</p>
                                    <p><?php echo Dynamic_Items_Counter($obj->getnumfileinfolder($show->id));?></p> <!--Count item in folder-->
                                <?php
                            }
                            ?>
                            <!-- Info FIller -->
                            <div>
                                <button class="btnsmall" style="visibility: hidden;"></button> <!--This is dummy button to maintain frame in place-->
                                <form method="get" action="index.php" style="display: inline;">
                                    <input type="hidden" name="req" value="doc">
                                    <input type="hidden" name="sort_tag" value="<?php echo XSS($_GET["sort_tag"] ?? '');?>"> 
                                    <input type="hidden" name="layout_tag" value="<?php echo XSS($_GET["layout_tag"] ?? '');?>">
                                    <button type="submit" class="btnsmall" title="Open Folder" name="direct" value="<?php echo urlencode($show->id);?>"><img src="Resource/Go_Folder.png"></button>
                                </form>            
                                <button type="button" class="btnsmall" title="Edit" onclick="window.location.href='index.php?req=folder_update&idedit=<?php echo urlencode($show->id);?>';"><img src="Resource/Edit.png"></button>                      
                                <form id="folderDelete_<?php echo XSS($show->id);?>" action="./Element/File/fileact.php" method="post" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                                    <input type="hidden" name="reqact" value="deletefolder"/>
                                    <input type="hidden" name="idfile" value="<?php echo urlencode($show->id);?>"/>
                                    <button type="button" class="btnsmall" title="Delete" onclick="showModalConfirmFolder('Are you sure you want to delete this folder? It also delete everything in the folder!', function() {document.getElementById('folderDelete_<?php echo XSS($show->id);?>').submit();});"><img src="Resource/Delete.png"></button>
                                </form>
                                <p></p>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                            if ($show->mime_type != "nete/folder") {
                                $lastchecktime = strtotime($show->last_check);
                                $time_diff = $get_current_time - $lastchecktime;
                                $is_outdated = ($time_diff > RECHECK_FILE_STATUS_TIMER);
                                if ($is_outdated === true && $show->file_status === 0) {
                                ?>
                                <div style="display: flex; justify-content: center;">
                                    <div id="verify-status-<?php echo XSS($show->id);?>" class="infoframe"><img title="Scanning in progress..." src="Resource/shield_eye.png"></div>
                                </div>
                                <script>
                                    checkFileAsync('<?php echo XSS($show->id);?>');
                                </script>
                                <?php
                                } else {
                                    if ($show->file_status === 0) {
                                        ?>
                                        <div style="display: flex; justify-content: center;">
                                            <div title="Tamper status is ok!" class="infoframe"><img src="Resource/green_shield.png"></div>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div style="display: flex; justify-content: center;">
                                            <div title="Tamper detected in your file! You shouldn't interact with the file." class="infoframe"><img src="Resource/red_shield.png"></div>
                                        </div>
                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <div style="display: flex; justify-content: center;">
                                    <div title="Folder type, no tamper scan needed." class="infoframe"><img src="Resource/white_shield.png"></div>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                    <?php
                }
            } else {
            ?>
            <?php
            if (!empty($_GET['files_search'])) {
                ?>
                    <h1 align="center" class="text_container" >WE CAN'T FIND WHAT YOU LOOKING FOR. </h1>
                <?php
            } else {
                if ($_SESSION['Current_Folder'] === "NULL") {
                    ?>
                        <h1 align="center" class="text_container">LET'S UPLOAD YOUR FIRST FILE HERE!</h1>
                    <?php
                } else {
                    ?>
                        <h1 align="center" class="text_container">THIS FOLDER IS EMPTY, LET'S UPLOAD SOMETHING!</h1>
                    <?php
                }
                ?>
                    <div class="Card_File_Add" style="display: flex; justify-content: center; align-items: center; flex-direction: column;">
                        <svg width="300" height="400" version="1.1" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><linearGradient id="d"><stop stop-color="#fff" offset="0"/><stop stop-color="#fff" stop-opacity="0" offset="1"/></linearGradient><linearGradient id="e" x1="18.815" x2="18.815" y1="36.03" y2="-6.5562" gradientTransform="matrix(2.66 0 0 2.66 25.604 17.44)" gradientUnits="userSpaceOnUse"><stop stop-color="#cecece" offset="0"/><stop stop-color="#404040" stop-opacity="0" offset="1"/></linearGradient><linearGradient id="f" x1="73.419" x2="73.419" y1="21.788" y2="120.33" gradientTransform="translate(0 1.6792)" gradientUnits="userSpaceOnUse"><stop stop-color="#fff" offset="0"/><stop stop-color="#404040" stop-opacity="0" offset="1"/></linearGradient><mask id="c" maskUnits="userSpaceOnUse"><rect width="120" height="120" ry=".29814" fill="url(#a)" style="paint-order:stroke fill markers"/></mask><linearGradient id="a" x1="60" x2="60" y1="21.788" y2="88.075" gradientTransform="translate(1.5173e-6)" gradientUnits="userSpaceOnUse" xlink:href="#d"/><mask id="b" maskUnits="userSpaceOnUse"><rect width="120" height="120" ry=".29814" fill="url(#a)" style="paint-order:stroke fill markers"/></mask></defs><g><path d="m85.924 21.788h-42.768c-1.7311 0-3.1224 1.3913-3.1224 3.1224v70.174c0 1.7311 1.3913 3.1276 3.1224 3.1276h61.482c1.7311 0 3.1224-1.3965 3.1224-3.1276v-55.05z" fill="url(#e)" mask="url(#b)" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.862"/><path d="m35.412 29.801-19.488 7.6484c-1.6115.63237-2.398 2.4354-1.7656 4.0469l21.254 54.164z" fill="#cecece" mask="url(#c)"/><path d="m73.894 47.238a9.6001 9.6001 0 00-9.091 6.515 9.6001 9.6001 0 00-1.9078-.19122 9.6001 9.6001 0 00-9.6002 9.6001 9.6001 9.6001 0 009.6002 9.6001 9.6001 9.6001 0 005.4955-1.7555 9.6001 9.6001 0 001.7811.9917v-4.2848l-5.1764-.15009 8.7496-8.1904 8.2694 8.2694h-4.6138v4.4438a9.6001 9.6001 0 002.0001-1.0829 9.6001 9.6001 0 005.4988 1.7588 9.6001 9.6001 0 009.6001-9.6001 9.6001 9.6001 0 00-9.6001-9.6001 9.6001 9.6001 0 00-1.9145.19345 9.6001 9.6001 0 00-9.091-6.5172z" fill="url(#f)" style="paint-order:stroke fill markers"/></g></svg>
                        <a class="Card_Text">Click the "Upload File" button above to get started</a>
                    </div>
                <?php
            }
            ?>
            <?php
            }
            ?>
        </div>
        <!-- Modal System (Experimental) -->
        <style>
            .del_button {
                padding: 8px 20px; 
                border: none; 
                background: #d62828; 
                color: white; 
                border-radius: 5px;
                font-family: 'Roboto', sans-serif; 
                font-weight: bold; 
                margin-right: 10px; 
                transition: 0.1s; 
                cursor: pointer;
            }
            .del_button:hover {
                background: white;
                color: #d62828;
            }

            .normal_button {
                padding: 8px 20px; 
                border: none; 
                background: #0081d6;
                color: white; 
                border-radius: 5px;
                font-family: 'Roboto', sans-serif; 
                font-weight: bold; 
                transition: 0.1s; 
                cursor: pointer;
            }
            .normal_button:hover {
                background: white; 
                color: #0081d6;
            }
        </style>
        <!-- Confirm Delete Modal -->
        <div id="modalConfirm" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px);">
                <div style="display: flex; justify-content: right; align-items: center;">
                    <button class="cross_button" onclick="closeModalConfirm()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                </div>
                <svg width="64" height="64" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g stroke-linecap="round" stroke-linejoin="round"><path d="m20.48 1.7988h-16.078c-.65079 0-1.1738.52304-1.1738 1.1738v26.381c0 .65079.52304 1.1758 1.1738 1.1758h23.113c.65079 0 1.1738-.525 1.1738-1.1758v-20.695z" fill="#b10000" stop-color="#000000"/><g fill="#fff"><path d="m4.4023 1.4492c-.83832 0-1.5234.68512-1.5234 1.5234v26.381c-1e-7.83832.68442 1.5254 1.5234 1.5254h23.113c.83902 0 1.5234-.68707 1.5234-1.5254v-20.695a.35003.35003 0 00-.125-.26953l-8.209-6.8594a.35003.35003 0 00-.22461-.080078zm0 .69922h15.949l7.9883 6.6738v20.531c0 .46325-.36166.82617-.82422.82617h-23.113c-.46255 0-.82422-.36292-.82422-.82617v-26.381c0-.46325.36096-.82422.82422-.82422z" stop-color="#000000"/><path d="m28.69 8.6582-8.209-6.8594v5.6855c0 .65079.52304 1.1738 1.1738 1.1738z" stop-color="#000000"/><path d="m20.705 1.5293a.35003.35003 0 00-.57422.26953v5.6855c0 .83832.68512 1.5234 1.5234 1.5234h7.0352a.35003.35003 0 00.22461-.61914zm.125 1.0176 6.8965 5.7617h-6.0723c-.46325 0-.82422-.36096-.82422-.82422z" stop-color="#000000"/></g></g><path d="m22.521 27.177h-1.875l-4.6875-4.6289-4.6289 4.6289h-1.9336v-1.875l4.6289-4.6875-4.6289-4.6289v-1.9336h1.9336l4.6289 4.6289 4.6875-4.6289h1.875v1.9336l-4.6289 4.6289 4.6289 4.6289z" fill="#fff" stroke-width="1.8898" aria-label="r"/></svg>
                <br><br>
                <span id="confirmText"></span>
                <br><br>
                <button class="del_button" onclick="confirmDeleteAction()">Delete</button>
                <button class="normal_button" onclick="closeModalConfirm()">Cancel</button>
            </div>
        </div>
        <!-- Confirm Delete Modal but for folder -->
        <div id="modalConfirmFolder" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px);">
                <div style="display: flex; justify-content: right; align-items: center;">
                    <button class="cross_button" onclick="closeModalConfirmFolder()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                </div>
                <svg width="64" height="64" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g transform="translate(1.1789 -2.1892)"><g stroke-linecap="square"><path d="m1.8203 6.888 5.2791-.1156 2.1894 2.0602h18.579c.98701 0 1.7816.79713 1.7816 1.7873v13.228c0 .99015-.7946 1.7873-1.7816 1.7873h-25.48c-.98701 0-1.7753-.79715-1.7816-1.7873l-.093945-14.755c-.0062803-.99013 1.3077-2.2044 1.3077-2.2044z" fill="#710000" stop-color="#000000" style="paint-order:markers fill stroke"/><path d="m7.1953 6.5215-5.4746.11914-.070312.064453s-.34159.31575-.68359.76172c-.342.44597-.70697 1.0208-.70312 1.627l.09375 14.756c.007142 1.1218.90984 2.0352 2.0313 2.0352h25.48c1.1214 0 2.0312-.91342 2.0312-2.0371v-13.229c0-1.1237-.90984-2.0371-2.0312-2.0371h-18.48zm-.19336.50195 2.1875 2.0586h18.68c.85262 0 1.5312.6805 1.5312 1.5371v13.229c0 .85661-.67863 1.5371-1.5312 1.5371h-25.48c-.85262 0-1.5277-.68056-1.5332-1.5391l-.09375-14.754c-.0024357-.384.28617-.91159.59961-1.3203.28533-.37207.52328-.5905.57227-.63672z" fill="#fff" stop-color="#000000" style="paint-order:markers fill stroke"/><path d="m1.8203 14.734h5.2791l2.1894-2.1591h18.579c.98701 0 1.7816.79713 1.7816 1.7873v13.228c0 .99016-.7946 1.7873-1.7816 1.7873h-25.48c-.98701 0-1.7753-.79715-1.7816-1.7873l-.093947-14.755c-.006304-.99014 1.3077 1.8993 1.3077 1.8993z" fill="#b10000" stop-color="#000000" style="paint-order:markers fill stroke"/><path d="m9.1855 12.326-2.1895 2.1582h-5.0215c-.060801-.13252-.29378-.64708-.5918-1.1914-.1673-.30558-.32447-.57383-.48633-.74805-.040464-.04355-.081129-.08398-.14258-.11914-.061449-.03516-.17396-.0728-.28906-.02148s-.16006.1504-.17969.2207c-.01963.0703-.021965.13544-.021484.21094l.09375 14.756c.007142 1.1218.90984 2.0352 2.0313 2.0352h25.48c1.1214 0 2.0312-.91341 2.0312-2.0371v-13.227c0-1.1237-.90984-2.0371-2.0312-2.0371zm.20508.49805h18.479c.85262 0 1.5312.68245 1.5312 1.5391v13.227c0 .85661-.67863 1.5391-1.5312 1.5391h-25.48c-.85262 0-1.5277-.6825-1.5332-1.541l-.091797-14.338c.065845.10552.11149.15507.18164.2832.32084.58603.64844 1.3047.64844 1.3047l.066406.14648h5.541z" fill="#fff" stop-color="#000000" style="paint-order:markers fill stroke"/></g><path d="m20.769 26.664h-1.625l-4.0625-4.0117-4.0117 4.0117h-1.6758v-1.625l4.0117-4.0625-4.0117-4.0117v-1.6758h1.6758l4.0117 4.0117 4.0625-4.0117h1.625v1.6758l-4.0117 4.0117 4.0117 4.0117z" fill="#fff" stroke-width="1.8898" aria-label="r"/></g></svg>
                <br><br>
                <span id="confirmTextFolder"></span>
                <br><br>
                <button class="del_button" onclick="confirmDeleteActionFolder()">Delete</button>
                <button class="normal_button" onclick="closeModalConfirmFolder()">Cancel</button>
            </div>
        </div>
                
        <script>
        // Confirm modal logic
        let confirmCallback = null;
        function showModalConfirm(msg, callback) {
            document.getElementById('confirmText').innerText = msg;
            document.getElementById('modalConfirm').style.display = 'flex';
            confirmCallback = callback;
        }
        function closeModalConfirm() {
            document.getElementById('modalConfirm').style.display = 'none';
            confirmCallback = null;
        }
        function confirmDeleteAction() {
            if (typeof confirmCallback === 'function') {
                confirmCallback();
            }
            closeModalConfirm();
        }

        // Confirm modal logic folder
        let confirmCallback2 = null;
        function showModalConfirmFolder(msg, callback) {
            document.getElementById('confirmTextFolder').innerText = msg;
            document.getElementById('modalConfirmFolder').style.display = 'flex';
            confirmCallback2 = callback;
        }
        function closeModalConfirmFolder() {
            document.getElementById('modalConfirmFolder').style.display = 'none';
            confirmCallback2 = null;
        }
        function confirmDeleteActionFolder() {
            if (typeof confirmCallback2 === 'function') {
                confirmCallback2();
            }
            closeModalConfirmFolder();
        }
        </script>
</html>
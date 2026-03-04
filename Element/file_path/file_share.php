<!DOCTYPE html>
<html lang="en">
    <?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: /Index.php');
        die;
    }
    define('RECHECK_FILE_STATUS_TIMER', 30 * 24 * 60 * 60);
    $get_current_time = time();
    require "./Element/Database/filecls.php";
    $search_value = '';
    $obj = new index();
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

    // Search Detection
    if (isset($_GET['files_search'])) {
        $search_value = $_GET['files_search'];
        if (empty($search_value)) {
            $doc_list = $obj->sharefile();
        } else {
            $doc_list = $obj->sharefile_search($search_value);
        }
    } else {
        $doc_list = $obj->sharefile();
    }
    $countup = count($doc_list);
    $sort_1 = 'selected';
    $sort_2 = '';
    $sort_3 = '';
    if (isset($_GET["sort_tag"])) {
        $sort_data = $_GET["sort_tag"];
        if ($sort_data == "sort_date") {
            $sort_2 = 'selected';
            $sort_1 = '';
            usort($doc_list, function($a, $b) {
                $date_A = strtotime($a->date);
                $date_B = strtotime($b->date);
                return $date_B - $date_A;
            });
        }
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
    ?>
    <style>
        h1, h4, p {
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .outline {
            border: 1px solid #0081d6; 
            padding: 10px; 
            border-radius: 15px;
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

        .btn_search_share {
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

        #Unique_search_share {
            padding: 7px;
            border: 3px solid rgba(0, 128, 214, 0);
            background-color: rgba(0, 128, 214, 0.5);
            border-radius: 5px 0px 0px 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            transition: 0.1s;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
        }

        #Unique_search_share::placeholder {
            color: #0081d6;
        }

        .btn_search_share:hover {
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
    </style>
    <div>
        <div class="outline">
                <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <h1>PUBLIC FILES</h1><a title="<?php echo $countup?> total share by user!" class="Info"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg></a>
                </div>
                <div>
                    <form method="get" action="index.php" style="display: flex;">
                        <input type="hidden" name="req" value="docshare">
                        <input type="hidden" name="layout_tag" value="<?php echo XSS($_GET["layout_tag"] ?? '');?>">
                        <input type="text" id="Unique_search_share" name="files_search" placeholder="Search Everyone Files" value="<?php echo XSS($search_value)?>">
                        <button type="submit" class="btn_search_share"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"/></svg></button>
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
                        <button type="button" class="btn_card" onclick="window.location.href='index.php?req=doc';"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M20 5h-9.586L8.707 3.293A.997.997 0 0 0 8 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2z"></path></svg>My Files</button>
                    </form>
                </div>
        </div>
    </div>
        <div class="<?php echo $layout_val?>">
            <?php
            if ($countup>0){
                foreach ($doc_list as $show){
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
                                <p>Uploader: <?php echo XSS($show->username);?></p>
                                <p><?php echo formatBytes($show->size);?></p>
                            <?php
                        }
                        ?>
                        <div>
                        <?php
                            if (in_array($getfiletype, $view_format_type)) {
                                ?>
                                    <button class="btnsmall" title="View" onclick="window.location.href='index.php?req=view&idview=<?php echo urlencode($show->id)?>';"><img src="Resource/Eye.png"></button>
                                    <button class="btnsmall" title="Download" onclick="window.location.href='Element/view/viewware.php?secure=download_file&fileid=<?php echo XSS($show->id);?>';"><img src="Resource/Download.png"></button>
                                    <?php
                                    if (XSS($_SESSION['AUTHENTICATE_USER']) != XSS($show->iduser)){
                                        ?>
                                            <button class="btnsmall report" title="Report" onclick="window.location.href='index.php?req=file_report&reportid=<?php echo urlencode($show->id);?>';"><img src="Resource/btn_alert.png"></button>
                                        <?php
                                    } else {
                                        ?>
                                            <button class="btnsmall" style="visibility: hidden;"></button> <!--This is dummy button to maintain frame in place-->
                                        <?php
                                    }
                                    ?>
                                <?php
                            } else {
                                ?> 
                                    <button class="btnsmall" style="visibility: hidden;"></button> <!--This is dummy button to maintain frame in place-->
                                    <button class="btnsmall" title="Download" onclick="window.location.href='Element/view/viewware.php?secure=download_file&fileid=<?php echo XSS($show->id);?>';"><img src="Resource/Download.png"></button>
                                    <?php
                                    if (XSS($_SESSION['AUTHENTICATE_USER']) != XSS($show->iduser)){
                                        ?>
                                            <button class="btnsmall report" title="Report" onclick="window.location.href='index.php?req=file_report&reportid=<?php echo urlencode($show->id);?>';"><img src="Resource/btn_alert.png"></button>
                                        <?php
                                    } else {
                                        ?>
                                            <button class="btnsmall" style="visibility: hidden;"></button> <!--This is dummy button to maintain frame in place-->
                                        <?php
                                    }
                                    ?>
                                <?php
                            }
                            ?>
                            <p></p>
                        </div>
                        <?php
                            if ($show->mime_type != "nete/folder") {
                                $lastchecktime = strtotime($show->last_check);
                                $time_diff = $get_current_time - $lastchecktime;
                                $is_outdated = ($time_diff > RECHECK_FILE_STATUS_TIMER);
                                if ($is_outdated === true && $show->file_status === 0) {
                                ?>
                                <div style="display: flex; justify-content: center;">
                                    <div class="infoframe"><img title="This file is old. Process at your own risk!" src="Resource/shield_panic.png"></div>
                                </div>
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
                ?>
                <?php
            } else {
            ?>
            <?php
            if (!empty($_GET['files_search'])) {
                ?>
                    <h1 align="center" class="text_container" >WE CAN'T FIND WHAT YOU LOOKING FOR. </h1>
                <?php
            } else {
                ?>
                    <h1 align="center" class="text_container" >NOBODY HAVE SHARE FILES YET... </h1>
                <?php
            }
            ?>
            <?php
            }
            ?>
        </div>
</html>
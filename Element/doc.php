<!DOCTYPE html>
<html lang="en">
    <?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    require "./Element/Database/filecls.php";
    // Predefine value
    $obj = new index();
    $get_ID = $_SESSION['AUTHENTICATE_USER'];
    $search_value = '';

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
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        #Unique_search {
            padding-left: 10px;
            padding-right: 10px;
            border: 3px solid transparent;
            background-color: rgba(0, 128, 214, 0.5);
            border-radius: 5px 0px 0px 5px;
            font-family: 'Roboto', sans-serif;
            color: white;
            font-size: 15px;
            font-weight: bold;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
            outline: none;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: default;
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
                <?php
                if ($percent_used > 70) {
                    ?>
                        <a class="alert_ico" title="Your storage going short! Consider clean up some space?"><img src="Resource\alert.png"></a>
                    <?php
                }
                ?>
            </div>
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
                    <button type="button" class="btn_card" onclick="window.location.href='index.php?req=docshare';"><svg width="30" height="30" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m4.4844 2.2598c-.04974.0016949-.1018.013303-.15039.011719-.81992.033402-1.649.034135-2.4707.068359-.15839.054515-.23273.23328-.3457.34961-.32035.41041-.59072.9046-.55664 1.4414-.008158 2.4591.017723 4.9199.033203 7.3789.009841.3856-.024623.79963.18359 1.1445.17026.33424.48095.63457.86133.69531 2.3952.03355 4.7902.022874 7.1855.027344 1.5916-.0051 3.1841.007636 4.7754-.021484.49265-.10494.86493-.54575.99023-1.0195.07475-.55327.038882-1.115.050782-1.6719-.0032-2.0454-.000959-4.0918-.011719-6.1367-.12027-.48678-.44754-.97639-.95312-1.1152-.5381-.068788-1.0837-.036416-1.625-.048828-2.2906-.0031257-4.5805.0008421-6.8711 0-.32039-.35183-.63004-.71666-.95703-1.0605-.040636-.036379-.088932-.044664-.13867-.042969zm-.2832 1.0801.44727.49609c.18037.18809.3376.40219.53125.57422.15394.068319.33939.013512.50586.03125 2.6573.00559 5.3154-.0089129 7.9727.015625.1228.0059301.27203-.023071.3418.10938.13533.16134.059474.39064.085938.58203-.020647.050784.024425.19269 0 .23828l-.001954.0019531-.005859.0058593-.001953.0019532h-.001953v.0019531h-.001953c-.010422.002964-.026694-.0001494-.046875-.0097656-.3823-.046579-.77131-.018768-1.1562-.03125-2.5381-.00945-5.0752-.0042794-7.6133-.0058594-.18074.041048-.25282.24555-.38281.36133-.2235.26066-.44838.52254-.67188.7832-.62739-.0027158-1.2566.0069712-1.8809-.0097657-.11858-.28884-.26334-.57302-.40234-.84766-.0029822-.56304-.0067837-1.1264-.0097657-1.6895.088561-.19302.1904-.38246.32617-.54688.65545-.034276 1.3112-.030013 1.9668-.0625zm2.543 3.3926c.55718-.017544 1.0722.42768 1.1152.97266.086156.57722-.4046 1.1614-.99805 1.1934-.32641.037391-.66049-.08173-.88477-.31641-.38078-.34214-.43383-.96098-.13672-1.3691.20482-.28658.54207-.48345.9043-.48047zm2.9023.77539c.4539-.016549.87468.32173.94531.75977.1102.51499-.33079 1.0692-.87305 1.0703-.48001.050948-.93072-.33802-.98242-.79883-.082483-.51867.37837-1.027.91016-1.0312zm-2.9062 1.6582c.090574-.0006415.18157.004862.27148.015625.90951.10557 1.645.78935 2.0234 1.5742.19349.41321.32417.86186.35156 1.3164-1.2825-.000241-2.5652.0012-3.8477 0-.47015-.00776-.94166.013505-1.4102-.021485.05274-.60941.26168-1.2198.61133-1.7324.43822-.65367 1.1811-1.1526 2-1.1523zm2.9277.45703c.32936-.009402.65556.083863.9375.24805.78064.4562 1.2155 1.3358 1.293 2.2012h-2.2227c-.01476-.15044-.042479-.29946-.074219-.44727-.12282-.65026-.44182-1.257-.89453-1.7461.29091-.16087.62278-.26396.96094-.25586z" stroke-linecap="round" stroke-width=".042336" style="paint-order:stroke fill markers"/></svg>Public Files</button>
                </form>
            </div>    
        </div>
        <div class="subframe">
            <div>
                <form method="get" action="index.php" style="display: flex;">
                    <input type="hidden" name="req" value="doc">
                    <input type="hidden" name="sort_tag" value="<?php echo XSS($_GET["sort_tag"] ?? '')?>"> 
                    <input type="hidden" name="layout_tag" value="<?php echo XSS($_GET["layout_tag"] ?? '')?>">  
                    <button type="submit" title="Go Up To Root" class="btn_card_sub" name="direct" value="Root"><svg width="24" height="24" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m8.1151 1.8992c.19082.019202.35986.161.5005.34248 2.0712 2.2308 4.1364 4.4728 6.181 6.751.14223.1057.06121.35787-.06861.40408-.28622.16089-.60826.070363-.90952.10471-.31078.00159-.6216.00245-.93239.00368v3.6047c0 .55005-.31623.99295-.70897.99295h-2.557v-3.0392c0-.55004-.31623-.99295-.70897-.99295h-1.8226c-.39273 0-.70897.4429-.70897.99295v3.0392h-2.557c-.39273 0-.70897-.4429-.70897-.99295v-3.6034c-.22915-.0008201-.45871-.0015936-.68786-.0024517-.35631-.019712-.71932.025841-1.0714-.055433-.09998-.03472-.29605-.18746-.20055-.35723.64224-.80332 1.3415-1.5137 2.0126-2.2692 1.4517-1.5879 2.8977-3.1877 4.355-4.7652.15405-.12224.33626-.15872.51106-.15769.028145-.00248.05542-.00275.082683 0z" stroke-width=".85547" style="paint-order:stroke fill markers"/></svg></button>
                    <?php
                        if($_SESSION['Current_Folder'] == "NULL"){
                            ?>
                                <button type="button" title="Go Up One Level" class="btn_card_sub_disable" name="direct"><svg width="24" height="24" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m4.5024 2.4695c-.049493.00169-.10068.011925-.14903.010349-.81586.033237-1.6423.03425-2.46.068304-.1576.054246-.23014.23198-.34256.34773-.31877.40838-.58862.90128-.55471 1.4354-.008113 2.4469.018749 4.8938.034152 7.3406.00979.38369-.02504.79624.18214 1.1394.16942.33258.47738.63088.85587.69132 2.3833.03339 4.7677.02348 7.1513.02794 1.5837-.005 3.1669.0072 4.7503-.02174.49021-.10442.86055-.54174.98524-1.0132.07438-.55053.0399-1.1101.05174-1.6641-.0032-2.0353-.000636-4.0712-.01136-6.106-.11969-.48437-.44492-.97126-.948-1.1094-.53543-.068447-1.079-.03629-1.6176-.048641-2.2793-.0031111-4.5584-.0001956-6.8377-.0010349-.3188-.35009-.62675-.71343-.95212-1.0556-.040435-.036199-.08815-.043082-.13764-.041397zm-.2815 1.0742.44398.49469c.17947.18716.33718.40009.52988.57127.15318.067981.33732.013397.50297.031047 2.6442.00556 5.2885-.00992 7.9326.014488.12219.0059.27004-.023124.33945.10867.13466.16054.05958.39014.08589.58059-.02797.06881.06952.29927-.05899.23803-.3804-.046347-.76779-.018624-1.1508-.031045-2.5255-.0094-5.0511-.0036-7.5766-.00519-.17984.040841-.25149.24391-.38084.35911-.22239.25937-.44514.51888-.66752.77826-.62429-.0027002-1.251.0073431-1.8722-.0093102-.118-.2874-.26219-.57016-.40051-.84344-.002969-.56025-.0053148-1.1204-.0082839-1.6807.088122-.19206.18779-.38077.32289-.54436.65221-.034106 1.3057-.029768 1.9581-.062095zm3.7795 3.0778 3.3417 3.3003h-1.9891v2.1557h-2.7053v-2.1557h-1.9891z" stroke-linecap="round" stroke-width=".042127" style="paint-order:stroke fill markers"/></svg></button>
                            <?php
                        } else {
                            ?>
                                <button type="submit" title="Go Up One Level" class="btn_card_sub" name="direct" value="Up"><svg width="24" height="24" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m4.5024 2.4695c-.049493.00169-.10068.011925-.14903.010349-.81586.033237-1.6423.03425-2.46.068304-.1576.054246-.23014.23198-.34256.34773-.31877.40838-.58862.90128-.55471 1.4354-.008113 2.4469.018749 4.8938.034152 7.3406.00979.38369-.02504.79624.18214 1.1394.16942.33258.47738.63088.85587.69132 2.3833.03339 4.7677.02348 7.1513.02794 1.5837-.005 3.1669.0072 4.7503-.02174.49021-.10442.86055-.54174.98524-1.0132.07438-.55053.0399-1.1101.05174-1.6641-.0032-2.0353-.000636-4.0712-.01136-6.106-.11969-.48437-.44492-.97126-.948-1.1094-.53543-.068447-1.079-.03629-1.6176-.048641-2.2793-.0031111-4.5584-.0001956-6.8377-.0010349-.3188-.35009-.62675-.71343-.95212-1.0556-.040435-.036199-.08815-.043082-.13764-.041397zm-.2815 1.0742.44398.49469c.17947.18716.33718.40009.52988.57127.15318.067981.33732.013397.50297.031047 2.6442.00556 5.2885-.00992 7.9326.014488.12219.0059.27004-.023124.33945.10867.13466.16054.05958.39014.08589.58059-.02797.06881.06952.29927-.05899.23803-.3804-.046347-.76779-.018624-1.1508-.031045-2.5255-.0094-5.0511-.0036-7.5766-.00519-.17984.040841-.25149.24391-.38084.35911-.22239.25937-.44514.51888-.66752.77826-.62429-.0027002-1.251.0073431-1.8722-.0093102-.118-.2874-.26219-.57016-.40051-.84344-.002969-.56025-.0053148-1.1204-.0082839-1.6807.088122-.19206.18779-.38077.32289-.54436.65221-.034106 1.3057-.029768 1.9581-.062095zm3.7795 3.0778 3.3417 3.3003h-1.9891v2.1557h-2.7053v-2.1557h-1.9891z" stroke-linecap="round" stroke-width=".042127" style="paint-order:stroke fill markers"/></svg></button>
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
                <button type="button" class="btn_card_sub_text" onclick="window.location.href='index.php?req=create_folder';"><svg width="30" height="30" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m4.485 2.2606c-.04974.0017-.10118.01198-.14977.0104-.81991.03341-1.6505.03442-2.4722.06864-.15839.05452-.23128.23313-.34426.34946-.32035.4104-.59154.90575-.55747 1.4426-.0081 2.4591.01884 4.9181.03432 7.3771.0098.38559-.02516.8002.18305 1.1451.17026.33423.47975.63401.86013.69476 2.3951.03356 4.7914.0236 7.1868.02808 1.5915-.005 3.1826.0072 4.7738-.02185.49264-.10494.86483-.54443.99013-1.0182.07476-.55326.0401-1.1156.052-1.6724-.0031-2.0454-.000635-4.0914-.01143-6.1363-.12028-.48678-.44712-.97609-.9527-1.1149-.53809-.06879-1.0843-.03647-1.6256-.04888-2.2906-.0031-4.581-.000196-6.8716-.0011-.32039-.3518-.62986-.71694-.95685-1.0608-.04063-.03638-.08858-.0433-.13833-.0416zm-.2829 1.0796.44618.49715c.18036.18809.33885.40208.53251.57411.15394.06832.339.01346.50547.0312 2.6573.0056 5.3148-.01 7.972.01456.1228.0059.27138-.02324.34114.1092.13532.16134.05986.39208.08632.58347-.02811.06915.06987.30076-.05929.23921-.38229-.04658-.7716-.01872-1.1565-.0312-2.538-.0094-5.0762-.0036-7.6142-.0052-.18073.04105-.25274.24512-.38273.3609-.22349.26065-.44735.52146-.67084.78212-.62739-.0027-1.2572.0074-1.8815-.0094-.11858-.28882-.2635-.57299-.4025-.84763-.003-.56303-.0053-1.126-.0083-1.689.08856-.19302.18872-.38266.3245-.54707.65544-.03428 1.3122-.02992 1.9678-.0624zm3.245 3.4093h1.1066v2.1248h2.0957v1.0775h-2.0957v2.1259h-1.1066v-2.1259h-2.0957v-1.0775h2.0957v-1.0629z" stroke-linecap="round" stroke-width=".042336" style="paint-order:stroke fill markers"/></svg>New Folder</button>
                <?php
                if ($total_size < $maximum_allowed) {
                    ?>
                        <button type="button" class="btn_card_sub_text" onclick="window.location.href='index.php?req=create_file';"><svg width="30" height="30" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m3.6789 1.2684c-.81105 0-1.464.65291-1.464 1.464v10.535c0 .81105.65291 1.464 1.464 1.464h8.6423c.81105 0 1.464-.65291 1.464-1.464v-7.782l-4.5268-4.2172zm5.5573.76515 3.7052 3.452h-2.5078c-.6639 0-1.1975-.53469-1.1975-1.1986zm-1.956 4.7513h1.192v2.1716h2.1716v1.1931h-2.1716v2.1716h-1.192v-2.1716h-2.1716v-1.1931h2.1716z" style="paint-order:stroke fill markers"/></svg>New File</button>
                        <button type="button" class="btn_card_sub_text" onclick="window.location.href='index.php?req=upload_file';"><svg width="30" height="30" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m3.6797 1.2676c-.81105 0-1.4648.65379-1.4648 1.4648v10.535c0 .81105.65379 1.4648 1.4648 1.4648h2.7461v-3.293h-1.9004l3.4746-3.168 3.4727 3.168h-1.8984v3.293h2.7461c.81105 0 1.4648-.65379 1.4648-1.4648v-7.7812l-4.5273-4.2188h-5.5781zm5.5566.76562 3.7051 3.4531h-2.5078c-.6639 0-1.1973-.53532-1.1973-1.1992v-2.2539z" style="paint-order:stroke fill markers"/></svg>Upload File</button>
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
                            if ($getfiletype != "nete/folder") {
                                $image_src = $file_icons_mime[$getfiletype] ?? './Resource/FormatIcons/Unknow.png';
                            } else {
                                $image_src = $folder_icons_mime[$show->file_link];
                            }
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
                        <?php if ($getfiletype != "nete/folder") {
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
                                    <button type="button" class="btnsmall report" title="Delete" onclick="showModalConfirm('Are you sure you want to delete this file? You will not able to recover it afterward.', function() {document.getElementById('fileDelete_<?php echo XSS($show->id);?>').submit();});"><img src="Resource/Delete.png"></button>
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
                                    <button type="button" class="btnsmall report" title="Delete" onclick="showModalConfirmFolder('Are you sure you want to delete this folder? It also delete everything in the folder!', function() {document.getElementById('folderDelete_<?php echo XSS($show->id);?>').submit();});"><img src="Resource/Delete.png"></button>
                                </form>
                                <p></p>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                            if ($getfiletype != "nete/folder") {
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
                cursor: pointer;
            }
            .normal_button:hover {
                background: white; 
                color: #0081d6;
            }
        </style>
        <!-- Confirm Delete Modal -->
        <div id="modalConfirm" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(15px); backdrop-filter: blur(15px);">
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
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(15px); backdrop-filter: blur(15px);">
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
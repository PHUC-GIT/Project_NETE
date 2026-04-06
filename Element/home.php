<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../Index.php');
    die;
}
require "./Element/Database/filecls.php";
// The database string for usercls called from index.php
$Get_Name = $name_login ?? '';
$Get_FILE = $getuserinfo->user_file($user_id);
$Get_NOTE = $getuserinfo->user_NOTE($user_id);
$Get_ID = $getuserinfo->user_ID($Get_Name);
$Count_FILE = count($Get_FILE);
$storage_obj = new index();
$total_size = $storage_obj->get_totalsize();

// Get the tamper status stale one in the $Get_File
$StaleCount = 0;
$BadCount = 0;
$TotalCount = 0;
$StatusShieldShow = ["./Resource/green_shield.png", "./Resource/shield_panic.png", "./Resource/red_shield.png",];
$StatusInfo = ["It's all good!", "Hey, there are some old files!", "File compromised detected!"];
$flag = 0;
$portalbuilder = array();
foreach ($Get_FILE as $FindStale){
    $lastchecktime = strtotime($FindStale->last_check);
    $TamperinDB = $FindStale->file_status;
    $time_diff = $get_current_time - $lastchecktime;
    $is_outdated = ($time_diff > RECHECK_FILE_STATUS_TIMER);
    $status = null;

    if ($TamperinDB == 1) {
        $BadCount++;
        $status = "Flagged";
    } elseif ($is_outdated) {
        $StaleCount++;
        $status = "Stale";
    }

    if ($status) {
        $portalbuilder[$FindStale->id] = [
            "Target_File" => $FindStale->file_name,
            "Flag" => $status
        ];
    }
}
$TotalCount = $StaleCount + $BadCount;
if ($StaleCount > 0){ // Raised a panic flag!
    $flag = 1;
}
if ($BadCount > 0){
    $flag = 2;
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
if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
    $maximum_allowed = 0;
} else {
    $get_user_storage = $storage_obj->get_userlocated();
    $maximum_allowed = $get_user_storage->storage_allocated;
}
$size_show = formatBytes($total_size);

// Percent calculator
    $bar_status = "";
    $server_status = 'src="./Resource/server.png"';
    function getPercentUsed($used, $max) {
        if ($max == 0) return 0;
        return round(($used / $max) * 100, 2);
    }
    $percent_used = getPercentUsed($total_size, $maximum_allowed);
    if ($percent_used > 70) {
        $server_status = 'src="./Resource/server_alert.png" title="Your storage going short! Consider clean up some space?"';
        $bar_status = "warning";
    }

// Percent per bar
    // $doc_type = array('pdf', 'txt', 'docx', 'doc', 'pptx', 'ppt', 'xlsx', 'xls', 'sql');
    // $img_type = array('png', 'svg', 'jpg', 'ico');
    // $audioandvideo_type = array('mp3', 'mp4', 'ogg');
    // $archive_type = array('zip', '7z', 'rar', 'apk');

    $doc_type = array('application/pdf', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'application/vnd.sqlite3');
    $img_type = array('image/png', 'image/svg+xml', 'image/jpeg', 'image/vnd.microsoft.icon');
    $audioandvideo_type = array('audio/mpeg', 'video/mp4', 'audio/ogg');
    $archive_type = array('application/zip', 'application/x-7z-compressed', 'application/x-rar-compressed', 'application/x-rar', 'application/vnd.android.package-archive');

    $space_left = formatBytes($maximum_allowed - $total_size);

    $size_doc = 0;
    $size_img = 0;
    $size_audioandvideo = 0;
    $size_archive = 0;
    $size_unknow = 0;
    
    foreach ($Get_FILE as $cal_storage) {
        $cal_storage_get = $cal_storage->mime_type;
        if (in_array($cal_storage_get, $doc_type)) {
            $size_doc += $cal_storage->size;
        }
        elseif (in_array($cal_storage_get, $img_type)) {
            $size_img += $cal_storage->size;
        }
        elseif (in_array($cal_storage_get, $audioandvideo_type)) {
            $size_audioandvideo += $cal_storage->size;
        }
        elseif (in_array($cal_storage_get, $archive_type)) {
            $size_archive += $cal_storage->size;
        }
        else {
            $size_unknow += $cal_storage->size;
        }
    }
    
    // Now calculate percent for each type
    $doc_percent = getPercentUsed($size_doc, $maximum_allowed);
    $img_percent = getPercentUsed($size_img, $maximum_allowed);
    $audioandvideo_percent = getPercentUsed($size_audioandvideo, $maximum_allowed);
    $archive_percent = getPercentUsed($size_archive, $maximum_allowed);
    $unknow_percent = getPercentUsed($size_unknow, $maximum_allowed);
    
    // Format sizes for display
    $size_doc = formatBytes($size_doc);
    $size_img = formatBytes($size_img);
    $size_audioandvideo = formatBytes($size_audioandvideo);
    $size_archive = formatBytes($size_archive);
    $size_unknow = formatBytes($size_unknow);
?>
<html lang="en">
    <style>
        h1, h4, h3, p {
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        .Info_Style {
            font-size: 20px;
            font-weight: bold;
        }

        .Info_Style_Bar {
            font-size: 15px;
            font-weight: bold;
        }

        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .Small_Info_Style {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            cursor: default;
            margin-left: -5px;
            margin-right: 5px;
        }

        .counter {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
            cursor: default;
        }

        .text_inbox {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            cursor: default;
        }

        .outline {
            border: 1px solid #626267; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(98, 98, 103, 0.3); 
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
            z-index: 100;
        }

        .portal_container {
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid gray;
            color: white;
            padding: 15px 15px;
            border-radius: 10px;
            width: 1000px;
            height: 500px;
            text-align: center;
            position: relative;
            font-family: 'Roboto', sans-serif;
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
            fill: white;
        }
    </style>
    <div align="center">
        <div class="outline">
            <h1>USER PAGE</h1>
        </div>
    </div>
    <?php
    if (!isset($_SESSION['AUTHENTICATE_ADMIN'])) {
        ?>
        <h1 align="center" class="text_container">SYSTEM STATUS</h1><br>
        <div class="flex-div" align="center">
            <div class="Card">
                <div>
                    <div class="img_inside_file"><img src="./Resource/Files.png"></div>
                </div>
                <p></p>
                <h1 class="Info_Style">Files</h1>
                <p class="counter"><?php echo $Count_FILE?></p>
                <p></p>
            </div>
            <div class="Card">
                <div>
                    <div class="img_inside_file"><img src="./Resource/NoteH.png"></div>
                </div>
                <p></p>
                <h1 class="Info_Style">Notes</h1>
                <p class="counter"><?php echo $Get_NOTE?></p>
                <p></p>
            </div>
            <div class="Card">
                <div>
                    <div class="img_inside_file"><img src="<?php echo XSS($StatusShieldShow[$flag]);?>"></div>
                </div>
                <p></p>
                <h1 class="Info_Style">Tamper Sentinel</h1>
                <p class="text_inbox"><?php echo XSS($StatusInfo[$flag]);?></p>
                <p></p>
            </div>
            </div>
                <!-- Start of the quota bar -->
                <div class="flex-div-bar">
                <div class="Card_Storage_Bar">
                        <div class="storage-center">
                            <h1 class="Info_Style_Bar"><?php echo XSS($Get_Name)?> Drive</h1>
                            <div title ="Free: <?php echo $space_left?>" class="custom-meter <?php echo $bar_status?>">
                                <div title="Document: <?php echo $size_doc?>" class="custom-meter-fill fill-doc" style="width: <?php echo $doc_percent?>%;"></div>
                                <div title="Image: <?php echo $size_img?>" class="custom-meter-fill fill-img" style="width: <?php echo $img_percent?>%;"></div>
                                <div title="Audio & Video: <?php echo $size_audioandvideo?>" class="custom-meter-fill fill-audioandvideo" style="width: <?php echo $audioandvideo_percent?>%;"></div>
                                <div title="Archive: <?php echo $size_archive?>" class="custom-meter-fill fill-archive" style="width: <?php echo $archive_percent?>%;"></div>
                                <div title="Unknow: <?php echo $size_unknow?>" class="custom-meter-fill fill-unknow" style="width: <?php echo $unknow_percent?>%;"></div>
                            </div>
                            <h1 class="Info_Style_Bar"><?php echo $size_show?> / <?php echo formatBytes($maximum_allowed)?></h1>
                        </div>
                        <div class="storage-center">
                            <div class="img_inside_file_bar"><img <?php echo $server_status?>></div>
                        </div>
                </div>
                <div class="Card_Storage_Bar_Info">
                    <div class="storage-center">
                        <div style="width: 20px; height: 20px; background: #0081d6; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $size_doc?> Doc</div>
                        <div style="width: 20px; height: 20px; background: #0AC963; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $size_img?> Image</div>
                        <div style="width: 20px; height: 20px; background: #6700d6; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $size_audioandvideo?> Audio&Video</div>
                        <div style="width: 20px; height: 20px; background: #d60000; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $size_archive?> Archive</div>
                        <div style="width: 20px; height: 20px; background: #5a5a5a; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $size_unknow?> Unknow</div>
                        <div style="width: 20px; height: 20px; background: #eee; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $space_left?> Free</div>
                    </div>
                </div>
                <!-- End of the quota bar -->
            </div>
        <h1 align="center" class="text_container">TAMPER PORTAL</h1><br>
            <div class="flex-div" align="center">
                <div class="portal_container">
                    <div style="align-items: center;">
                        <div>
                            <span style="font-size: 15px; cursor: default;"><?php echo XSS($TotalCount)?> items need attention</span>
                        </div>
                    </div>
                    <br>
                    <div class="List_Container_Portal">
                        <?php
                        foreach ($portalbuilder as $listportal){
                            ?>
                            <a class="PortalList <?php echo XSS($listportal['Flag']);?>" href="index.php?req=doc&files_search=<?php echo XSS($listportal['Target_File']);?>">
                                <svg style="margin-left: 10px;" width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5zm-1.9902 6.4902h1.9805v2.2402l-.46875 4.8438h-1.0352l-.47656-4.8438v-2.2402zm.078125 7.7148h1.8301v1.8301h-1.8301v-1.8301z"/></svg>
                                <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo XSS($listportal['Target_File']);?></span>
                                <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"> | Status Detected: <?php echo XSS($listportal['Flag']);?></span>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php
    } else {
        ?>
        <h1 align="center" class="text_container">SORRY! ONLY AVAILABLE FOR USERS!</h1><br>
        <?php
    }
    ?>
</html>
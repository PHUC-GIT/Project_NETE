<!DOCTYPE html>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

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
            border: 1px solid #00b41e; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color: rgba(0, 180, 30, 0.3); 
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
            width: 650px;
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
            <h1>STATUS</h1>
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
                        <div style="width: 20px; height: 20px; background: #5a5a5a; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $size_unknow?> Unknown</div>
                        <div style="width: 20px; height: 20px; background: #eee; border-radius: 5px;"></div><div class="Small_Info_Style"><?php echo $space_left?> Free</div>
                    </div>
                </div>
                <!-- End of the quota bar -->
            </div>
        <h1 align="center" class="text_container">TAMPER PORTAL & QUICK ACCESS DETAIL</h1><br>
            <div class="flex-div" align="center">
                <!-- This is the tamper protal -->
                <div class="portal_container">
                    <div style="align-items: center;">
                        <div>
                            <span style="font-size: 15px; cursor: default;"><?php echo Dynamic_Items_Counter(XSS($TotalCount));?> need attention</span>
                        </div>
                    </div>
                    <br>
                    <?php
                    if (!empty($portalbuilder)) {
                        ?>
                        <div class="List_Container_Portal">
                            <?php
                            foreach ($portalbuilder as $listportal){
                                ?>
                                <a class="PortalList <?php echo XSS($listportal['Flag']);?>" href="index.php?req=doc&files_search=<?php echo XSS($listportal['Target_File']);?>">
                                    <svg style="margin-left: 10px;" width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5zm-1.9902 6.4902h1.9805v2.2402l-.46875 4.8438h-1.0352l-.47656-4.8438v-2.2402zm.078125 7.7148h1.8301v1.8301h-1.8301v-1.8301z"/></svg>
                                    <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo XSS($listportal['Target_File']);?>  | Status Detected: <?php echo XSS($listportal['Flag']);?></span>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="EmptyPlaceholder">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24"><path d="M20.995 6.9a.998.998 0 0 0-.548-.795l-8-4a1 1 0 0 0-.895 0l-8 4a1.002 1.002 0 0 0-.547.795c-.011.107-.961 10.767 8.589 15.014a.987.987 0 0 0 .812 0c9.55-4.247 8.6-14.906 8.589-15.014zM12 19.897C5.231 16.625 4.911 9.642 4.966 7.635L12 4.118l7.029 3.515c.037 1.989-.328 9.018-7.029 12.264z"></path><path d="m11 12.586-2.293-2.293-1.414 1.414L11 15.414l5.707-5.707-1.414-1.414z"></path></svg>
                            <p class="BelowPlaceholder">No attention needed</p>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!-- This is the quick access portal -->
                <div class="portal_container">
                    <div style="align-items: center;">
                        <div>
                            <span style="font-size: 15px; cursor: default;"><?php echo Dynamic_Items_Counter(count($_SESSION['QUICK_ACCESS_FILE']));?> in quick access</span>
                        </div>
                    </div>
                    <br>
                    <?php
                    if (!empty($_SESSION['QUICK_ACCESS_FILE'] ?? [])) {
                        ?>
                        <div class="List_Container_Portal">
                        <?php
                        foreach ($_SESSION['QUICK_ACCESS_FILE'] as $listportal){
                            ?>
                            <a class="PortalList QuickAccess" href="index.php?req=view&idview=<?php echo urlencode($listportal['ID']);?>">
                                <svg style="margin-left: 10px;" width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5zm-2.4668 7.6582h5.4668v5.4668l-2.25-2.25-4.7832 4.7832-.9668-.9668 4.7832-4.7832-2.25-2.25z"/></svg>
                                <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo XSS($listportal["File_Name"]);?> | <?php echo XSS(formatBytes($listportal["Size"]));?> | Type: <?php echo XSS($listportal["Type"]);?></span>
                            </a>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="EmptyPlaceholder">
                            <svg width="100" height="100" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m3.6797 1.2676c-.81105 0-1.4648.65379-1.4648 1.4648v10.535c0 .81105.65379 1.4648 1.4648 1.4648h8.6406c.81105 0 1.4648-.65379 1.4648-1.4648v-7.7812l-4.5273-4.2188h-5.5781zm5.5566.76562 3.7051 3.4531h-2.5078c-.6639 0-1.1973-.53532-1.1973-1.1992v-2.2539zm-1.3262 4.8359a3.0581 3.0581 0 01.81445.054688 3.0292 3.0292 0 011.5312.82422 3.0134 3.0134 0 01.64844.96289 3.0036 3.0036 0 01.23828 1.1797 3.0171 3.0171 0 01-.51758 1.6934 3.0309 3.0309 0 01-1.332 1.0957 3.0446 3.0446 0 01-1.1777.23828 3.0228 3.0228 0 01-2.1406-.88672l.47461-.47656a2.3772 2.3772 0 00.34961.28906 2.3405 2.3405 0 00.8418.35352 2.3943 2.3943 0 00.94922 0 2.3482 2.3482 0 00.44141-.13672 2.3745 2.3745 0 00.74805-.50586c.10665-.10631.203-.22284.28711-.34766a2.3334 2.3334 0 00.35547-.8418 2.3943 2.3943 0 000-.95117 2.356 2.356 0 00-2.7812-1.832 2.3664 2.3664 0 00-.84375.35547 2.3772 2.3772 0 00-.63477.63672 2.3405 2.3405 0 00-.40234 1.3164v.0078125h.67188l-1.0078 1.3379-1.0098-1.3379h.67383l-.0019531-.0078125a3.0154 3.0154 0 01.23828-1.1797 3.044 3.044 0 01.64844-.96094 3.016 3.016 0 011.5332-.82617 3.0581 3.0581 0 01.4043-.054688zm.11328 1.127c.11445-.0096201.23194.078047.24609.19336.025973.61937.022386 1.2396.035156 1.8594.22899.22936.45865.458.6875.6875.14132.15475.31271.28806.42383.4668.072903.19411-.1611.39303-.3418.29688-.3666-.32128-.70516-.67599-1.0469-1.0234-.070016-.094848-.20185-.1628-.21484-.28906-.028815-.63424-.021527-1.2695-.03125-1.9043-.018312-.13282.074378-.28677.21875-.2832.0075592-.0015216.015807-.0032649.023438-.0039062z" style="paint-order:stroke fill markers"/></svg>
                            <p class="BelowPlaceholder">No quick access found</p>
                        </div>
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
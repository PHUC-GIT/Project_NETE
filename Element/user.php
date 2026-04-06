<!DOCTYPE html>
<html lang="en">
    <?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    $get_user_list = $getuserinfo->user_list();
    // get master drive information.
    function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    $userallocated = 0;
    $mastervalue = 0;
    $redzone_diskspace = 10737418240; // Set as Free Space banned zone to keep safety free space
    $path = __DIR__;
    $free_bytes = @disk_free_space($path);
    if ($free_bytes === false) {
        $mastervalue = 'Could not retrieve disk information';
    } else {
        $final_bytes = $free_bytes - $redzone_diskspace;
        if ($final_bytes < 0) $final_bytes = 0; // If thing goes minus, make it zero.
        $mastervalue = $final_bytes;
    }
    $Storagedisplay = formatBytes($mastervalue);
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

        .bolder_text {
            font-weight: bold;
        }

        .outline {
            border: 1px solid #ff0062; 
            padding: 10px; 
            border-radius: 15px;
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
            transition: 0.1s;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff0062;
        }

        .btn_in_list {
            border: none;
            padding: 5px;
            background-color: #ff0062;
            border-radius: 5px;
            color: white;
            margin-left: 5px;
            margin-right: 5px;
            transition: 0.1s;
            cursor: pointer;
        }

        .btn_in_list:hover {
            background-color: white;
            color: #ff0062;
        }
    </style>
    <div>
        <div class="outline">
            <h1>USER MANAGER</h1>
        </div>
    </div>
        <div align="center" class="span_container">
            <table border="1">
                <p class="bolder_text">Current Active User</p>
                <p class="bolder_text">Master Storage Free Space: <?php echo XSS($Storagedisplay)?></p>
                <thead>
                    <th><p>Username</p></th>
                    <th><p>Comment</p></th>
                    <th><p>Storage_Allocated</p></th>
                    <th><p>Control</p></th>
                </thead>
                <tbody>
                    <?php
                    foreach($get_user_list as $list){
                        ?>
                    <tr align="center">
                        <td><p><?php echo XSS($list->username);?></p></td>
                        <td><p><textarea class="report_panel" rows="2" disabled><?php echo XSS($list->comment);?></textarea></p></td>
                        <td><p><?php echo XSS($list->storage_allocated); $userallocated = $userallocated + $list->storage_allocated;?></p></td>
                        <td>
                            <button type="button" class="btn_in_list" title="Edit" onclick="window.location.href='index.php?req=useredit&userid=<?php echo XSS($list->iduser);?>';">Edit</button>
                            <form name="del_user_<?php echo XSS($list->iduser);?>" id="del_user_<?php echo XSS($list->iduser);?>" method="post" action="Element/User/useract.php" style="display: inline;">
                                <input type="hidden" name="reqact" value="userdel"/>
                                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                                <input type="hidden" name="user_id" value="<?php echo XSS($list->iduser);?>"/>
                                <button type="button" class="btn_in_list" onclick="if(confirm('Do you want to delete this user? It is also clean up this user data.')){document.getElementById('del_user_<?php echo XSS($list->iduser);?>').submit();}">Delete User</button>
                            </form>
                        </td>
                    </tr>
                        <?php
                    }
                    ?>
                    <form name="add_user" id="actionform_PDF" method="post" action="Element/User/useract.php">
                        <input type="hidden" name="reqact" value="adduser"/>
                        <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                        <td><p><input type="text" name="new_username" id="new_username" placeholder="New Username" required/></p></td>
                        <td><p><textarea class="report_panel" name="new_comment" placeholder="New Comment..." rows="2"></textarea></p></td>
                        <td><p><input type="number" name="new_storage" id="new_storage" value="0"/></p></td>
                        <td><input class="btn_in_list" type="submit" value="Add New User"/></td></td>
                    </form>
                </tbody>
                <p class="bolder_text">Total of space allocated to user: <?php $userusespacedisplay = $userallocated; echo XSS(formatBytes($userusespacedisplay));?></p>
                <p class="bolder_text">Actually space left allow to allocate: <?php $RealMasterStorage = max(0, $mastervalue - $userusespacedisplay);  echo XSS(formatBytes($RealMasterStorage));?></p>
            </table>
        </div>
</html>
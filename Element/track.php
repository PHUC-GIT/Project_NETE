<!DOCTYPE html>
<html lang="en">
    <?php
    // NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)
    
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    require "./Element/Database/reportcls.php";
    require "./Element/Database/trackcls.php";
    $obj=new report();
    $obj1=new track();
    $get_sys_report_list = $obj->listreport();
    $get_user_report_list = $obj1->getreportlist();

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
            <h1>TRACKING REPORT CASE</h1>
            <div>
                <button class="btn_card pointer" onclick="window.location.href='index.php?req=report_eye_note';">Reported Notes</button>
                <button class="btn_card pointer" onclick="window.location.href='index.php?req=report_eye_file';">Reported Files</button>
            </div>
        </div>
    </div>
        <div align="center" class="span_container">
            <table border="1">
                <p class="bolder_text">Trouble Report Cause By User</p>
                <thead>
                    <th><p>User</p></th>
                    <th><p>IP</p></th>
                    <th><p>Cause</p></th>
                    <th><p>Date</p></th>
                </thead>
                <tbody>
                    <?php
                    foreach ($get_sys_report_list as $list){
                        ?>
                    <tr>
                        <td><p><?php echo XSS($list->user); ?></p></td>
                        <td><p><?php echo XSS($list->IP); ?></p></td>
                        <td><p><?php echo XSS($list->cause); ?></p></td>
                        <td><p><?php echo XSS($list->date); ?></p></td>
                    </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <table border="1">
                <p class="bolder_text">User Report Case</p>
                <thead>
                    <th><p>Reporter</p></th>
                    <th><p>Type</p></th>
                    <th><p>Type Assoc ID</p></th>
                    <th><p>REASON</p></th>
                    <th><p>Control</p></th>
                </thead>
                <tbody>
                    <?php
                    foreach ($get_user_report_list as $list){
                        ?>
                    <tr align="center">
                        <td><p><?php echo XSS($list->username); ?></p></td>
                        <td><p><?php echo XSS($list->ITEM_TYPE); ?></p></td>
                        <td><p><?php echo XSS($list->ITEM_ID); ?></p></td>
                        <td><textarea class="report_panel" rows="5" disabled><?php echo XSS($list->REASON); ?></textarea></td>
                        <td>
                            <form id="clearlist_<?php echo $list->ITEM_ID?>" action="./Element/Track/trackact.php" method="post" style="display: inline;">
                                <input type="hidden" name="reqact" value="deletelist"/>
                                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>">
                                <input type="hidden" name="item_type" value="<?php echo $list->ITEM_TYPE?>"/>
                                <input type="hidden" name="item_id" value="<?php echo $list->ITEM_ID?>"/>
                                <button type="button" class="btn_in_list" onclick="if(confirm('Are you sure this reported item innocent? It also delete all duplicated report.')){document.getElementById('clearlist_<?php echo $list->ITEM_ID?>').submit();}">Delete List</button>
                            </form>
                            <form id="clearlistthenfile_<?php echo $list->ITEM_ID?>" action="./Element/Track/trackact.php" method="post" style="display: inline;">
                                <input type="hidden" name="reqact" value="deletelistandfile"/>
                                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>">
                                <input type="hidden" name="item_type" value="<?php echo $list->ITEM_TYPE?>"/>
                                <input type="hidden" name="item_id" value="<?php echo $list->ITEM_ID?>"/>
                                <button type="button" class="btn_in_list" onclick="if(confirm('Are you sure this reported item guilty? It also delete all duplicated report.')){document.getElementById('clearlistthenfile_<?php echo $list->ITEM_ID?>').submit();}">Delete Data Assoc</button>
                            </form>
                        </td>
                    </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
</html>
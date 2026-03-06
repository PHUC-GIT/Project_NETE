<!DOCTYPE html>
<?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../../Index.php');
        die;
    }
    
    require "./Element/Database/filecls.php";
    require "./Element/Database/reportcls.php";

    $Get_Name = $name_login ?? '';
    if (!isset($_GET['idedit'])) {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No context have been given.");
        echo "<script>window.location.href='index.php?req=doc';</script>";
        die;
    }
    $getidfile = urldecode($_GET['idedit'] ?? '');
    $obj=new index();
    $report=new report();
    $get_ID = $_SESSION['AUTHENTICATE_USER'];
    $get_info = $obj->getinfo_edit($getidfile);
    $get_folder = $obj->getfolderavailable();
    $folder_file_check = $obj->checkiffolderexist($getidfile);
    $count_folder = count($get_folder);
    $get_type = $get_info->mime_type ?? '';
    $get_file_size = $get_info->size ?? 0;
    $ediable_content_type = array("text/plain");
    // If not owned files
    if (!$get_info) {
        $user = $Get_Name;
        $cause = "Unauthorized access ID files to edit!";
        $report->report_in($cause, $user);
        echo "<script>alert('!WARNING!: File is invalid for access.'); window.location.href='index.php?req=doc'; </script>";
        die;
    }
    if ($folder_file_check) {
        echo "<script>alert('!WARNING!: File type you try to edit is invalid!'); window.location.href='index.php?req=doc'; </script>";
        die;
    }
    $selector = $get_info->share;
    $is_yes = '';
    $is_no = '';
    if ($selector == 1) {
        $is_yes = 'selected';
    } else {
        $is_no = 'selected';
    }
?>
<html lang="en">
    <style>
        h1, p, a {
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        
        #notehere {
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 20px;
            font-weight: bold;
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
            padding: 7px;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px;
            margin-left: 10px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            transition: 0.1s;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #0081d6;
        }

        .btn_card.pointer {
            cursor: pointer;
        }
    </style>
    <div>
        <div class="outline">
            <div>
                <h1>EDIT FILE</h1>
            </div>
            <div>
                <?php
                if (in_array($get_type, $ediable_content_type) && $get_file_size <= 52428800) {
                    ?>
                        <button class="btn_card pointer" onclick="window.location.href='index.php?req=edittext_file&idview=<?php echo urlencode($get_info->id);?>';">Edit File Content</button>
                    <?php
                }
                ?>
                <button class="btn_card pointer" onclick="window.location.href='index.php?req=doc';">Return</button>
            </div>
        </div>
    </div>
        <div align="center" class="text_container">
            <form name="update_files" id="actionform_PDF" method="post" action="Element/File/fileact.php">
                <input type="hidden" name="reqact" value="updatefile"/>
                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                <h1>EDIT FILE DATA</h1>
                <p></p>
                <input type="hidden" name="id_get" value="<?php echo $get_info->id;?>">
                <p>Card Name *Change card name or leave it be*</p>
                <input type="text" maxlength="30" name="file_name" id="file_name" value="<?php echo XSS($get_info->file_name);?>"/>
                <p>Share *So other user can read*</p>
                <select name="is_share" style="cursor: pointer;">
                    <option value="0" <?php echo $is_no?>>NO</option>
                    <option value="1" <?php echo $is_yes?>>YES</option>
                </select>
                <?php
                $folder_ext_id = array_column($get_folder, 'id');
                if (in_array($_SESSION['Current_Folder'], $folder_ext_id)) {
                    $count_folder--;
                } else {
                    // Do nothing.
                }
                ?>
                <p>Move file to >> <?php echo XSS($count_folder);?> folder available</p>
                <select name="moving_file" style="cursor: pointer;">
                    <option value="<?php echo XSS($_SESSION['Current_Folder'])?>" selected>[ Do Not Move ]</option>
                    <option value="NULL">[ Move To Root ]</option>
                    <?php
                    if ($count_folder>0){
                        foreach($get_folder as $showfolder){
                            if ($showfolder->id === $_SESSION['Current_Folder']) {
                                // Skip from printing out option for current folder.
                            } else {
                                ?>
                                <option value="<?php echo XSS($showfolder->id);?>"><?php echo XSS($showfolder->file_name);?></option>
                                <?php
                            }
                        }
                    }
                    ?>
                </select>
                <p></p>
                <input id="btn_upload" type="submit" value="Save"/>
                <p></p>
            </form>
        </div>
</html>
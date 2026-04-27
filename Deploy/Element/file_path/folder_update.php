<!DOCTYPE html>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../../Index.php');
        die;
    }

    require "./Element/Database/filecls.php";
    require "./Element/Database/reportcls.php";
    if (!isset($_GET['idedit'])) {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No context have been given.");
        echo "<script>window.location.href='index.php?req=doc'; </script>";
        die;
    }
    $Get_Name = $name_login ?? '';
    $getidfile = urldecode($_GET['idedit'] ?? '');
    $obj=new index();
    $report=new report();
    $get_info = $obj->getinfo_edit($getidfile);
    $get_folder = $obj->getfolderavailable();
    $folder_file_check = $obj->checkiffolderexist($getidfile);
    $count_folder = count($get_folder);

    $selectedlist = array(
        'no' => '',
        'star' => '',
        'music' => '',
        'video' => '',
        'image' => '',
        'doc' => '',
        'danger' => '',
    );
    $selectedlist[$get_info->file_link] = "selected";

    // If not owned files
    if (!$get_info) {
        $user = $Get_Name;
        $cause = "Unauthorized access ID files to edit!";
        $report->report_in($cause, $user);
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File is invalid for access.");
        echo "<script>window.location.href='index.php?req=doc'; </script>";
        die;
    }
    if (!$folder_file_check) {
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
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
            z-index: 100;
        }

        .btn_card {
            padding: 7px;
            border: 3px solid #0081d6;
            background-color: #0081d6;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
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
            <h1>EDIT FOLDER</h1>
            <button class="btn_card pointer" onclick="window.location.href='index.php?req=doc';">Return</button>
        </div>
    </div>
        <div align="center" class="text_container">
            <form name="update_files" id="actionform_PDF" method="post" action="Element/File/fileact.php">
                <input type="hidden" name="reqact" value="updatefolder"/>
                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>    
                <h1>EDIT FOLDER</h1>
                <p></p>
                <input type="hidden" name="id_get" value="<?php echo $get_info->id ?? '';?>">
                <p>Folder Name</p>
                <input type="text" maxlength="100" name="folder_name" id="folder_name" value="<?php echo XSS($get_info->file_name ?? '');?>"/>
                <?php
                $folder_ext_id = array_column($get_folder, 'id');
                // Cound down for same current folder
                if (in_array($_SESSION['Current_Folder'], $folder_ext_id)) {
                    $count_folder--;
                }
                // Count down for this item folder
                if (in_array($get_info->id, $folder_ext_id)) {
                    $count_folder--;
                }
                $getcurrentfolderid = $get_info->id ?? '';
                ?>
                <p>Move folder to >> <?php echo XSS($count_folder);?> folder available</p>
                <select name="moving_file" style="cursor: pointer;">
                    <option value="<?php echo XSS($_SESSION['Current_Folder'])?>" selected>[ Do Not Move ]</option>
                    <option value="NULL">[ Move To Root ]</option>
                    <?php
                    if ($count_folder>0){
                        foreach($get_folder as $showfolder){
                            if ($showfolder->id === $_SESSION['Current_Folder'] || $showfolder->id === $getcurrentfolderid) {
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
                <p>Folder style</p>
                <select name="is_folder_style" style="cursor: pointer;">
                    <option value="no" <?php echo XSS($selectedlist["no"]);?>>Standard Folder</option>
                    <option value="star" <?php echo XSS($selectedlist["star"]);?>>Star Folder</option>
                    <option value="music" <?php echo XSS($selectedlist["music"]);?>>Music Folder</option>
                    <option value="video" <?php echo XSS($selectedlist["video"]);?>>Video Folder</option>
                    <option value="image" <?php echo XSS($selectedlist["image"]);?>>Image Folder</option>
                    <option value="doc" <?php echo XSS($selectedlist["doc"]);?>>Document Folder</option>
                    <option value="danger" <?php echo XSS($selectedlist["danger"]);?>>Danger Folder</option>
                </select>
                <p></p>
                <input id="btn_upload" type="submit" value="Save"/>
                <p></p>
            </form>
        </div>
</html>
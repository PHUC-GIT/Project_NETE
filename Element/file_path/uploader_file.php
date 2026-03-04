<!DOCTYPE html>
<?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: /Index.php');
        die;
    }

    require "./Element/Database/filecls.php";
    $obj=new index();
    $total_size = $obj->get_totalsize();
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
    $_SESSION['USER_CURRENT_STORAGE'] = $maximum_allowed - $total_size;
    if ($total_size>$maximum_allowed){
        header('location:index.php?req=doc');
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

        .Info {
            fill: white;
            cursor: pointer;
        }

        .Info:hover {
            fill: #7fcfff;
        }

        /* Bar Style */
        #progress_bar_container {
            width: 100%;
            border-radius: 5px; 
            background-color: #f1f1f1; 
            display: none;
        }

        #progress_bar {
            width: 0%; 
            height: 20px; 
            background-color: #0AC963;
            font-family: 'Roboto', sans-serif;
            text-align: center; 
            line-height: 20px; 
            color: white;
        }
    </style>
    <div>
        <div class="outline">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <h1>UPLOAD YOUR FILE</h1><a title="Your Storage: <?php echo $size_show?> / <?php echo formatBytes($maximum_allowed);?>" class="Info"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg></a>
            </div>
            <button class="btn_card pointer" onclick="window.location.href='index.php?req=doc';">Return</button>
        </div>
    </div>
        <div align="center" class="text_container">
            <form name="upload_files" id="uploadform_PDF" method="post" enctype="multipart/form-data" action="Element/File/fileact.php">
                <input type="hidden" name="reqact" value="addfile"/>
                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                <h1>NEW UPLOAD</h1>
                <p>INFO: Maximum 1GB per upload.</p>
                <p></p>
                <p>Card Name  *Can leave empty*</p>
                <input type="text" maxlength="30" name="file_name" id="file_name"/>
                <p></p>
                <p>Upload</p>
                <input id="key_upload_font" type="file" name="file_value" id="file_value" style="margin-left: 84px; cursor: pointer;"/>
                <p></p>
                <p>Share *So other user can see*</p>
                    <select name="is_share" style="cursor: pointer;">
                        <option value="0">NO</option>
                        <option value="1">YES</option>
                    </select>
                <p></p>
                <input id="btn_upload" type="submit" value="Upload"/></td>
                <p id="notehere"></p>
                <div id="progress_bar_container">
                    <div id="progress_bar">0%</div>
                </div>
            </form>
        </div>
</html>
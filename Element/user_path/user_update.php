<!DOCTYPE html>
<?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../../Index.php');
        die;
    }
    if (!isset($_GET['userid'])) {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No context have been given.");
        echo "<script>window.location.href='index.php?req=user';</script>";
        die;
    }
    $getiduser = urldecode($_GET['userid'] ?? '');
    $get_info = $getuserinfo->get_user_info($getiduser);
    if (!$get_info) {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "This user not exist.");
        echo "<script>window.location.href='index.php?req=user';</script>";
        die;
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
            font-family: 'Roboto', sans-serif;
            color: white;
            font-size: 15px;
            font-weight: bold;
            transition: 0.1s;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff0062;
        }

        .btn_card.pointer {
            cursor: pointer;
        }
    </style>
    <div>
        <div class="outline">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <h1>EDIT USER</h1>
            </div>
            <button class="btn_card pointer" onclick="window.location.href='index.php?req=user';">Return</button>
        </div>
    </div>
        <div align="center" class="text_container">
            <!-- Continue to change thing here-->
            <form name="update_user" id="actionform_PDF" method="post" action="Element/User/useract.php">
                <input type="hidden" name="reqact" value="userupdate"/>
                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                <input type="hidden" name="user_id" value="<?php echo XSS($get_info->iduser);?>"/>              
                <h1>Current Selected User: <?php echo XSS($get_info->username);?></h1>
                <p></p>
                <p>Change current Username</p>
                <input type="text" name="edit_username" id="edit_username" placeholder="Do not leave empty" value="<?php echo XSS($get_info->username);?>" required/>
                <p></p>
                <p>New Password [If not change, please don't touch]</p>
                <input type="text" name="new_password" id="new_password" placeholder="New Password"/>
                <p></p>
                <p>This Account Comment</p>
                <textarea name="row_comment" id="text_content" placeholder="The account Comment" rows="5" cols="33"><?php echo XSS($get_info->comment);?></textarea>
                <p>Current User Storage Allocated [Bytes]</p>
                <input type="number" name="storage" id="storage" value="<?php echo XSS($get_info->storage_allocated);?>"/>
                <p></p>
                <input id="btn_upload" type="submit" value="Update"/></td>
            </form>
        </div>
</html>
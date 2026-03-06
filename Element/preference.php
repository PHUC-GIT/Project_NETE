<!DOCTYPE html>
<?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    $selectedlist = array(
        "B1" => "",
        "B2" => "",
        "B3" => "",
        "B4" => "",
        "B5" => "",
        "B6" => "",
        "B7" => "",
    );
    $selectedlist[$getinfo] = "selected";
    
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
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        .btn_card {
            padding: 7px;
            border: 3px solid #626267;
            background-color: #626267;
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
            color: #626267;
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

        #btn_submit{
            padding: 10px 10px;
            background-color: #0698c0;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            transition: 0.1s;
            cursor: pointer;
        }

        #btn_submit:hover{
            background-color: #ffffff;
            color: #0698c0;
            transition: 0.1s;
        }
    </style>
    <div>
        <div class="outline">
            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                <h1>USER PREFERENCE</h1>
            </div>
        </div>
    </div>
        <div align="center" class="text_container">
            <form name="change_setting" id="actionform" method="post" action="Element/Preference/preferact.php">
            <input type="hidden" name="reqact" value="updatesetting"/>
            <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>        
            <h1>SETTING</h1>
            <p></p>
            <p>Background</p>
            <select name="is_background" style="cursor: pointer;">
                <option value="B1" <?php echo XSS($selectedlist["B1"])?>>GRAY VOID - NETE</option>
                <option value="B2" <?php echo XSS($selectedlist["B2"])?>>3 COLORS ABSTRACT - NETE</option>
                <option value="B3" <?php echo XSS($selectedlist["B3"])?>>Blocky Crystal - Pawel Czerwinski</option>
                <option value="B4" <?php echo XSS($selectedlist["B4"])?>>Trees And Fern In Forest - Lev Strelchenko</option>
                <option value="B5" <?php echo XSS($selectedlist["B5"])?>>Aurora Borealis - Stein Egil Liland</option>
                <option value="B6" <?php echo XSS($selectedlist["B6"])?>>Purple And Pink Diamond On Blue Background - Rostislav Uzunov</option>
                <option value="B7" <?php echo XSS($selectedlist["B7"])?>>2 People On The Boat View - Quang Nguyen Vinh</option>
            </select>
            <p></p>
            <input id="btn_submit" type="submit" value="Save Change"/></td>
            </form>
        </div>
</html>
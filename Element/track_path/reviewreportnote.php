<!DOCTYPE html>
<script src="JS/jquery-3.6.3.min.js" type="text/javascript"></script>
<script src="JS/Track.js" type="text/javascript"></script>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../../Index.php');
        die;
    }

    require "./Element/Database/trackcls.php";
    $user_note = '';
    $obj = new track();
    // Search Detection
    if (isset($_GET['NOTE_search'])) {
        $user_note = $_GET['NOTE_search'];
        if (empty($user_note)) {
            $note_list = $obj->getreported_note();
        } else {
            $note_list = $obj->reportnote_search($user_note);
        }
    } else {
        $note_list = $obj->getreported_note();
    }
    $countup = count($note_list);
    $sort_tag_value = '';
    $sel_0 = 'selected';
    $sel_1 = '';
    $sel_2 = '';
    $sel_3 = '';
    $sel_4 = '';
    $sel_5 = '';
    $sel_6 = '';
    if (isset($_GET['colour_tag'])) {
        $sort_tag_value = $_GET['colour_tag'];
        if ($sort_tag_value == 'card_default') {
            $sel_1 = 'selected';
            $sel_0 = '';
        }
        else if ($sort_tag_value == 'card_red') {
            $sel_2 = 'selected';
            $sel_0 = '';
        }
        else if ($sort_tag_value == 'card_blue') {
            $sel_3 = 'selected';
            $sel_0 = '';
        }
        else if ($sort_tag_value == 'card_green') {
            $sel_4 = 'selected';
            $sel_0 = '';
        }
        else if ($sort_tag_value == 'card_yellow') {
            $sel_5 = 'selected';
            $sel_0 = '';
        }
        else if ($sort_tag_value == 'card_purple') {
            $sel_6 = 'selected';
            $sel_0 = '';
        }
    }
?>
<html lang="en">
    <style>
        h1, p {
            color: white;
            font-family: 'Roboto', sans-serif;
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
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #ff0062;
            background-color: #ff0062;
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

        .btn_search_note_spy {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #ff0062;
            background-color: #ff0062;
            border-radius: 0px 5px 5px 0px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        #Unique_search_user_note_spy {
            padding: 7px;
            border: 3px solid rgba(255, 153, 0, 0);
            background-color: rgba(255, 0, 98, 0.5);
            border-radius: 5px 0px 0px 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        #Unique_search_user_note_spy::placeholder {
            color: #ff0062;
        }

        .btn_search_note_spy:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff0062;
            fill: #ff0062;
        }


        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff0062;
            fill: #ff0062;
        }

        .small_text {
            margin: 10px;
            font-weight: bold;
        }

        .css_sort {
            background: #ff0062;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            color: white;
            margin-left: 10px;
            cursor: pointer;
        }

        .css_sort:hover, .css_sort:focus{
            background-color: white;
            color: #ff0062;
        }

        option {
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
        }
    </style>
    <div align="center">
        <div class="outline">
            <h1>SUSPECTED NOTES</h1>
            <div>
                <div>
                    <form method="get" action="index.php" style="display: flex;">
                        <input type="hidden" name="req" value="report_eye_note">
                        <input type="text" id="Unique_search_user_note_spy" name="NOTE_search" placeholder="Who To Look?" value="<?php echo XSS($user_note)?>">
                        <button type="submit" class="btn_search_note_spy"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M19.023 16.977a35.13 35.13 0 0 1-1.367-1.384c-.372-.378-.596-.653-.596-.653l-2.8-1.337A6.962 6.962 0 0 0 16 9c0-3.859-3.14-7-7-7S2 5.141 2 9s3.14 7 7 7c1.763 0 3.37-.66 4.603-1.739l1.337 2.8s.275.224.653.596c.387.363.896.854 1.384 1.367l1.358 1.392.604.646 2.121-2.121-.646-.604c-.379-.372-.885-.866-1.391-1.36zM9 14c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"/></svg></button>
                        <select onchange="this.form.submit()" id="filter_colour_tag_spy" name="colour_tag" class="css_sort dropdown_control">
                            <option value="" <?php echo $sel_0?>>All Color</option>
                            <option value="card_default" <?php echo $sel_1?>>Black</option>
                            <option value="card_red" <?php echo $sel_2?>>Red</option>
                            <option value="card_blue" <?php echo $sel_3?>>Blue</option>
                            <option value="card_green" <?php echo $sel_4?>>Green</option>
                            <option value="card_yellow" <?php echo $sel_5?>>Yellow</option>
                            <option value="card_purple" <?php echo $sel_6?>>Purple</option>
                        </select>
                        <button type="button" class="btn_card" onclick="window.location.href='index.php?req=track';">Return</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-div">
        <?php
        if ($countup>0){
            foreach ($note_list as $show){
                if (str_contains($show->tag, $sort_tag_value)) {
                    ?>
                    <div class="Card_note <?php echo $show->tag?>">
                        <textarea name="text_content_edit" class="text_content_panel" rows="10" cols="33" disabled><?php echo XSS($show->text);?></textarea>
                        <br>
                        <p class="small_text">From user: <?php echo XSS($show->username);?></p>
                    </div>
                    <?php   
                }
            }
        } else {
            ?>
            <h1 align="center" class="text_container" >REPORT CASE EMPTY! HOORAY!</h1>
            <?php
        }
        ?>
    </div>
</html>
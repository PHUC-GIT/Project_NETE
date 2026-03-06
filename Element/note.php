<!DOCTYPE html>
<?php
    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    
    require "./Element/Database/notecls.php";
    $whois = "default";
    if (isset($_SESSION['AUTHENTICATE_USER'])) {
        $whois = $_SESSION['AUTHENTICATE_USER'];
    }
    if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
        $whois = $_SESSION['AUTHENTICATE_ADMIN'];
    }
    $obj = new index();
    $note_list = $obj->listnote($whois);
    $countup = count($note_list);

    // This for filter colour
    $sort_tag_value = '';
    $sel_0_filter = 'selected';
    $sel_1_filter = '';
    $sel_2_filter = '';
    $sel_3_filter = '';
    $sel_4_filter = '';
    $sel_5_filter = '';
    $sel_6_filter = '';
    if (isset($_GET['colour_tag'])) {
        $sort_tag_value = $_GET['colour_tag'];
        if ($sort_tag_value == 'card_default') {
            $sel_1_filter = 'selected';
            $sel_0_filter = '';
        }
        else if ($sort_tag_value == 'card_red') {
            $sel_2_filter = 'selected';
            $sel_0_filter = '';
        }
        else if ($sort_tag_value == 'card_blue') {
            $sel_3_filter = 'selected';
            $sel_0_filter = '';
        }
        else if ($sort_tag_value == 'card_green') {
            $sel_4_filter = 'selected';
            $sel_0_filter = '';
        }
        else if ($sort_tag_value == 'card_yellow') {
            $sel_5_filter = 'selected';
            $sel_0_filter = '';
        }
        else if ($sort_tag_value == 'card_purple') {
            $sel_6_filter = 'selected';
            $sel_0_filter = '';
        }
    }

    // This for each tag cards
    $sel_1 = '';
    $sel_2 = '';
    $sel_3 = '';
    $sel_4 = '';
    $sel_5 = '';
    $sel_6 = '';
    $sel_pub_1 = '';
    $sel_pub_2 = '';
    
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
            border: 1px solid #ff9900; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(255, 153, 0, 0.3); 
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
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #ff9900;
            background-color: #ff9900;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #ff9900;
            fill: #ff9900;
        }

        .css_sort {
            background: #ff9900;
            border-radius: 5px;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            color: white;
            transition: 0.1s;
            margin-left: 10px;
            cursor: pointer;
        }

        option {
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
        }

        .css_sort:hover, .css_sort:focus{
            background-color: white;
            color: #ff9900;
        }

        .Card_Note_Add {
            width: 329.6px;
            height: 337.8px;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            fill: gray;
            border: 3px dashed gray;
            display: inline-block;
            cursor: pointer;
            transition: 0.1s;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(7px);
            backdrop-filter: blur(7px);
        }

        .Card_Note_Add:hover {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            fill: white;
            border: 3px solid white;
            display: inline-block;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(7px);
            backdrop-filter: blur(7px);
        }
    </style>
    <div align="center">
        <div class="outline">
            <h1>MEMORY NOTES</h1>
            <div>
                <form method="get" action="index.php" style="display: flex;">
                    <input type="hidden" name="req" value="note">
                    <select onchange="this.form.submit()" id="sort_colour_tag" name="colour_tag" class="css_sort dropdown_control">
                        <option value="" <?php echo $sel_0_filter?>>All Color</option>
                        <option value="card_default" <?php echo $sel_1_filter?>>Black</option>
                        <option value="card_red" <?php echo $sel_2_filter?>>Red</option>
                        <option value="card_blue" <?php echo $sel_3_filter?>>Blue</option>
                        <option value="card_green" <?php echo $sel_4_filter?>>Green</option>
                        <option value="card_yellow" <?php echo $sel_5_filter?>>Yellow</option>
                        <option value="card_purple" <?php echo $sel_6_filter?>>Purple</option>
                    </select>
                    <button type="button" class="btn_card" onclick="window.location.href='index.php?req=note_public';"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m5 3a2 2 0 00-2 2v14a2 2 0 002 2h8l8-8v-8a2 2 0 00-2-2h-14zm1.0645 1.8809c.75429 0 1.3633.65973 1.3633 1.4766 0 .81684-.60899 1.4785-1.3633 1.4785-.75429 0-1.3652-.66168-1.3652-1.4785 0-.81684.61094-1.4766 1.3652-1.4766zm3.1191.63281c.64584 0 1.1699.56819 1.1699 1.2676 0 .69939-.52408 1.2656-1.1699 1.2656s-1.1699-.56623-1.1699-1.2656c0-.69939.52408-1.2676 1.1699-1.2676zm-3.1191 2.957c1.1844 0 2.1445 1.0396 2.1445 2.3223v.078125c0 .30615-.22901.55469-.51172.55469h-3.2676c-.28271 0-.51172-.24854-.51172-.55469v-.078125c0-1.2827.96204-2.3223 2.1465-2.3223zm3.1191.21094c.96876 0 1.7539.8513 1.7539 1.9004v.29492c0 .30351-.22559.54883-.50586.54883h-1.7656c.08042-.16495.12695-.35411.12695-.55469v-.078125c0-.6796-.2118-1.3059-.56641-1.8047.2754-.19398.60487-.30664.95703-.30664zm2.8164 3.3184h7l-7 7v-7z"/></svg>Public Notes</button>
                </form>
            </div>
        </div>
    </div>
    <div class="flex-div">
        <!-- Add note sector -->
        <form id="addNoteForm" action="./Element/Note/noteact.php" method="post">
            <input type="hidden" name="reqact" value="addnote">
            <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>">
            <div title="Add Note" class="Card_Note_Add" style="display: flex; justify-content: center; align-items: center;" onclick="document.getElementById('addNoteForm').submit();">
                <svg width="150" height="300" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m5 3a2 2 0 00-2 2v14a2 2 0 002 2h8l8-8v-8a2 2 0 00-2-2h-14zm3.1328 2.3359h.67773.67383v2.9062c.18788.058607.41957.0079816.62305.025391h2.0586v1.4785h-2.6582c-.053552.2056-.0055314.45709-.021484.67969v2.252h-1.3535v-2.9082c-.18788-.0586-.41762-.0060275-.62109-.023437h-2.0566v-1.4785h2.6543c.053552-.2056.0074845-.45709.023438-.67969v-2.252zm3.8672 6.6641h7l-7 7v-7z"/></svg>
            </div>
        </form>
        <!-- End of sector -->
        <?php
        if ($countup>0){
            foreach ($note_list as $show){
                if ($show->tag == 'card_default') {
                    $sel_1 = 'selected';
                }
                else if ($show->tag == 'card_red') {
                    $sel_2 = 'selected';
                }
                else if ($show->tag == 'card_blue') {
                    $sel_3 = 'selected';
                }
                else if ($show->tag == 'card_green') {
                    $sel_4 = 'selected';
                }
                else if ($show->tag == 'card_yellow') {
                    $sel_5 = 'selected';
                }
                else if ($show->tag == 'card_purple') {
                    $sel_6 = 'selected';
                }
                if ($show->public_value == '0') {
                    $sel_pub_1 = 'selected';
                }
                else if ($show->public_value == '1') {
                    $sel_pub_2 = 'selected';
                }
                if (str_contains($show->tag, $sort_tag_value)) {
                    ?>
                    <div class="Card_note <?php echo $show->tag?>">
                        <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>"/>
                        <input type="hidden" name="id_get" value="<?php echo $show->id;?>"/>
                        <textarea name="text_content_edit" class="text_content_panel" rows="10" cols="33"><?php echo XSS($show->text);?></textarea>
                        <div align="center">
                            <select name="tag" class="css_sel dropdown_control">
                                <option value="card_default" <?php echo $sel_1?>>Black</option>
                                <option value="card_red" <?php echo $sel_2?>>Red</option>
                                <option value="card_blue" <?php echo $sel_3?>>Blue</option>
                                <option value="card_green" <?php echo $sel_4?>>Green</option>
                                <option value="card_yellow" <?php echo $sel_5?>>Yellow</option>
                                <option value="card_purple" <?php echo $sel_6?>>Purple</option>
                            </select>
                            <select name="tag_public" class="css_sel dropdown_control">
                                <option value="0" <?php echo $sel_pub_1?>>Private</option>
                                <option value="1" <?php echo $sel_pub_2?>>Public</option>
                            </select>
                        </div>
                        <div align="center">
                            <form id="deleteNoteForm_<?php echo XSS($show->id);?>" action="./Element/Note/noteact.php" method="post">
                                <input type="hidden" name="reqact" value="deletenote">
                                <input type="hidden" name="csrf_token" value="<?php echo XSS($_SESSION['CSRF_TOKEN'] ?? '');?>">
                                <input type="hidden" name="id_get" value="<?php echo XSS($show->id);?>">
                                <button class="btnsmall_note" title="Delete" onclick="document.getElementById('deleteNoteForm_<?php echo XSS($show->id);?>').submit();"><img src="Resource/Delete.png"></button>
                            </form>
                            <p></p>
                        </div>
                    </div>
                    <?php
                }
                // Clean up card select...
                $sel_1 = '';
                $sel_2 = '';
                $sel_3 = '';
                $sel_4 = '';
                $sel_5 = '';
                $sel_6 = '';
                $sel_pub_1 = '';
                $sel_pub_2 = '';
            }
        } else {
        }
        ?>
    </div>
    <!-- Auto Save Model -->
    <script>
    // A global function to update all CSRF token inputs on the page
    function updateAllCsrfTokens(newToken) {
        document.querySelectorAll('input[name="csrf_token"]').forEach(input => {
            input.value = newToken;
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.Card_note').forEach(function(card) {
            const csrf = card.querySelector('input[name="csrf_token"]');
            const textarea = card.querySelector('textarea[name="text_content_edit"]');
            const tagSelect = card.querySelector('select[name="tag"]');
            const pubSelect = card.querySelector('select[name="tag_public"]');
            const idInput = card.querySelector('input[name="id_get"]');

            function autoSave() {
                const data = new FormData();
                data.append('reqact', 'ajax_updatenote');
                data.append('csrf_token', csrf.value)
                data.append('id_get', idInput.value);
                data.append('text_content_edit', textarea.value);
                data.append('tag', tagSelect.value);
                data.append('tag_public', pubSelect.value);

                fetch('./Element/Note/noteact.php', {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success && res.new_csrf_token) {
                        updateAllCsrfTokens(res.new_csrf_token);
                    } else {
                        showModalMessage(res.message || "Auto-save failed!");
                    }
                })
                .catch(() => {
                    showModalMessage("Auto-save failed!");
                });
            }

            textarea.addEventListener('blur', autoSave);
            tagSelect.addEventListener('change', autoSave);
            pubSelect.addEventListener('change', autoSave);
        });
    });
    </script>
</html>
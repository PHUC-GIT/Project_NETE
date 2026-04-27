<!DOCTYPE html>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

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
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        .Card_Note_Add:hover {
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            fill: white;
            border: 3px solid white;
            display: inline-block;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
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
                    <button type="button" class="btn_card" onclick="window.location.href='index.php?req=note_public';"><svg width="30" height="30" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m2.8412 2.2802-.24029.085668c-.50478.16593-.93651.54367-1.1941 1.005-.2975.48397-.20161 1.0754-.22984 1.6152-.0025 1.9149.0076 3.8301.01045 5.745.12544.46001.40884.84954.74071 1.1826.23064.33578.43502.71021.79086.93503.58185.43856 1.3426.37414 2.0278.43356 2.548.15328 5.0967.31579 7.6464.4367.96701.03105 1.896-.72393 2.0425-1.682.16798-2.0998.27212-4.2048.38759-6.3081.01338-.7127-.39689-1.3798-.97369-1.7771-.16916-.2259-.19611-.53262-.37715-.75743-.32149-.48301-.86048-.82612-1.4292-.91414zm1.9965 1.4146a.87939.88072 0 01.87862.88071.87939.88072 0 01-.87862.88071.87939.88072 0 01-.87966-.88071.87939.88072 0 01.87966-.88071zm2.2838.6237a.74428.7454 0 01.74385.74489.74428.7454 0 01-.74385.74594.74428.7454 0 01-.74489-.74594.74428.7454 0 01.74489-.74489zm6.7155.66863c.03134.065152.06269.13021.09403.19536.19585.40987.06107.87841.06999 1.3132-.09853 1.7559-.17942 3.5126-.32282 5.2654.000358.44464-.27689.85114-.67385 1.0416-.46612.23378-1.0034.09459-1.5013.09611-2.5971-.16285-5.1965-.2906-7.7916-.48371-.02516-.000802-.15717-.01293-.18074-.02612l-.0011-.001h-.001v-.004h.001v-.001h.0011c.0052-.0033.01913-.0061.04492-.0094 1.7534-.04899 3.5083-.03035 5.2623-.04911 1.6649-1.6218 3.3301-3.2434 4.9949-4.8653.0017-.82391.0025-1.6479.0042-2.4718zm-9.0004.66236a2.0886 2.6382 0 012.0769 2.3506h-4.1528a2.0886 2.6382 0 012.0759-2.3506zm2.2848.36148a1.7677 2.2328 0 011.7572 1.9892h-1.7572a2.0886 2.6382 0 00-.76788-1.7687 1.7677 2.2328 0 01.76788-.22044zm4.3837 1.6078c.28019.00954.58436-.010816.85146.029253-1.0937 1.0982-2.2086 2.1814-3.3358 3.2418-.04906-.72924-.03635-1.4663-.0031-2.1971.0941-.60117.66305-1.0869 1.2767-1.0583.40325-.019529.80725-.01517 1.2108-.015673z" stroke-width="5.349" style="paint-order:stroke fill markers"/></svg>Public Notes</button>
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
                <svg width="150" height="300" version="1.1" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m2.841 2.28-.2403.085671c-.5048.16593-.93654.54368-1.1942 1.0051-.29751.48399-.20162 1.0754-.22985 1.6152-.00252 1.915.00764 3.8302.010453 5.7452.12544.46003.40885.84957.74074 1.1827.23064.33579.43504.71023.79089.93507.58187.43857 1.3426.37415 2.0279.43358 2.5481.15328 5.0969.3158 7.6467.43671.96704.03106 1.8961-.72395 2.0425-1.6821.16799-2.0999.27213-4.205.38761-6.3083.013442-.71273-.3969-1.3799-.97372-1.7771-.16917-.22591-.19612-.53264-.37717-.75746-.3215-.48303-.86051-.82615-1.4292-.91417zm3.663 1.7437h1.121v2.1188h2.1188v1.121h-2.1188v2.1188h-1.121v-2.1188h-2.1188v-1.121h2.1188zm7.3332.96328c.03134.065154.06269.13022.09402.19537.19585.40989.06108.87844.07 1.3133-.09853 1.7559-.17942 3.5128-.32283 5.2656.000359.44466-.2769.85117-.67387 1.0416-.46614.23379-1.0035.0946-1.5013.09613-2.5972-.16286-5.1967-.29062-7.7919-.48373-.025151-.000802-.15717-.01287-.18074-.02613l-.00104-.0011h-.00104v-.0044h.00104v-.0011h.00104c.00525-.0033.019135-.0061.044925-.0094 1.7534-.04899 3.5084-.03035 5.2625-.04911 1.6649-1.6219 3.3302-3.2436 4.995-4.8655.0017-.82394.0026-1.648.0041-2.4719zm-2.3319 2.6318c.2802.00954.58438-.010811.85148.029255-1.0937 1.0982-2.2086 2.1815-3.3359 3.2419-.049063-.72926-.036359-1.4664-.00316-2.1971.094107-.60119.66307-1.087 1.2767-1.0583.40327-.01953.80728-.01517 1.2109-.015673z" stroke-width="5.3492" style="paint-order:stroke fill markers"/></svg>
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
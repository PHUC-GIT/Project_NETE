<!DOCTYPE html>
<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}

// History feature (SESSION)
if (!isset($_SESSION['BROWSER_History'])) {
    $_SESSION['BROWSER_History'] = array();
}
$hold_value = '';
if (isset($_GET['in_search'])) {
    $hold_value = $_GET['in_search'];
    // Check if the value is already in the array
    $key = array_search($hold_value, $_SESSION['BROWSER_History']);

    if ($key !== false) {
        // If found, remove the old, less recent entry
        unset($_SESSION['BROWSER_History'][$key]);
    }
    // Add the value to the beginning of the array
    $_SESSION['BROWSER_History'] = array_merge([$hold_value], $_SESSION['BROWSER_History']);
}
?>
<html lang="en">
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

        .h1_font {
            font-size: 55px;
        }

        .outline {
            border: 1px solid #009519; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(0, 149, 25, 0.3); 
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
            border: 3px solid #009519;
            background-color: #009519;
            border-radius: 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            margin-left: 10px;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #009519;
        }

        .view_value {
            border: none;
            border-radius: 7px;
            width: 99%;
            height: 74vh;
            background: black;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .file_view {
            margin-top: 15px;
        }

        #Unique_search {
            padding: 7px;
            width: 500px;
            border: 3px solid transparent;
            background-color:rgba(0, 149, 25, 0.5); 
            border-radius: 5px 0px 0px 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        #Unique_search::placeholder {
            color: #009519;
            text-align: center;
        }

        .History_Button {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #009519;
            background-color: #009519;
            border-radius: 0px 5px 5px 0px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .History_Button:hover {
            background-color: white;
            border: 3px solid white;
            fill: #009519;
        }
    </style>
    <div>
        <div class="outline">
            <div>
                <h1>BROWSER</h1>
            </div>
            <div>
                <form name="formbrowser" method="get" action="index.php" style="display:flex;">
                    <input type="hidden" name="req" value="browser">
                    <input type="text" id="Unique_search" name="in_search" placeholder="Site Link" value="<?php echo XSS($hold_value);?>">
                    <button type="button" class="History_Button" onclick="showModalHistory()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M12 8v5h5v-2h-3V8z"/><path d="M21.292 8.497a8.957 8.957 0 0 0-1.928-2.862 9.004 9.004 0 0 0-4.55-2.452 9.09 9.09 0 0 0-3.626 0 8.965 8.965 0 0 0-4.552 2.453 9.048 9.048 0 0 0-1.928 2.86A8.963 8.963 0 0 0 4 12l.001.025H2L5 16l3-3.975H6.001L6 12a6.957 6.957 0 0 1 1.195-3.913 7.066 7.066 0 0 1 1.891-1.892 7.034 7.034 0 0 1 2.503-1.054 7.003 7.003 0 0 1 8.269 5.445 7.117 7.117 0 0 1 0 2.824 6.936 6.936 0 0 1-1.054 2.503c-.25.371-.537.72-.854 1.036a7.058 7.058 0 0 1-2.225 1.501 6.98 6.98 0 0 1-1.313.408 7.117 7.117 0 0 1-2.823 0 6.957 6.957 0 0 1-2.501-1.053 7.066 7.066 0 0 1-1.037-.855l-1.414 1.414A8.985 8.985 0 0 0 13 21a9.05 9.05 0 0 0 3.503-.707 9.009 9.009 0 0 0 3.959-3.26A8.968 8.968 0 0 0 22 12a8.928 8.928 0 0 0-.708-3.503z"/></svg></button>
                </form>
            </div>
            <div>
                <button class="btn_card" onclick="window.location.href='index.php?req=utility';">Return</button>
            </div>
        </div>
    </div>
        <div class="file_view" align="center">
            <?php
                if(isset($_GET['in_search'])) {
                    $final_value = $hold_value;
                    if(!empty($_GET['in_search'])) {
                        ?>  
                            <iframe class="view_value" src="<?php echo XSS($final_value );?>" loading="lazy" referrerpolicy="no-referrer"></iframe>
                        <?php
                    } else {
                        ?>
                            <div class="view_value">
                                <!-- Information Modal -->
                                <div style="position:fixed;">
                                  <div style="background-color: gray;border: 1px solid white;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; fill: white; cursor:default;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
                                    <br><br>
                                    <span>Your Input Box Is Empty</span>
                                  </div>
                                </div>
                            </div>
                        <?php
                    }
                } else {
                    ?>
                        <div class="view_value">
                            <!-- Information Modal -->
                                <div style="position:fixed;">
                                  <div style="background-color: gray;border: 1px solid white;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; fill: white; cursor:default;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
                                    <br><br>
                                    <span>Input Website Link to view. <br> I don't responsible for shady link. <br> Also some site refuse to connect because of cross site policy and I can't fix that.</span>
                                  </div>
                                </div>
                        </div>
                    <?php
                }
            ?>
        </div>
        <!-- Modal System (Experimental) -->
        <style>
            .List_Container {
                max-height: 440px;
                overflow-y: auto;
                overflow-x: hidden;
                scrollbar-color: #009519 transparent;
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .HistoryRecord {
                display: flex;
                justify-content: left;
                align-items: center;
                border: none;
                border-radius: 10px;
                background: rgba(0, 149, 25, 0.3);
                text-decoration: none;
                color: white;
                font-size: 15px;
                min-height: 50px;
                cursor: pointer;
            }

            .HistoryRecord:hover {
                background: #009519;
            }
        </style>
        <!-- History Modal -->
        <div id="modalhistory" onclick="if(event.target === this) closeModalHistory()" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;width:500px;height:500px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(15px); backdrop-filter: blur(15px); fill: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-weight: bold; font-size: 30px; cursor: default;">Browser History</span>
                    </div>
                    <div>
                        <button class="cross_button" onclick="closeModalHistory()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                    </div>
                </div>
                <br>
                <?php
                if (!empty($_SESSION['BROWSER_History'] ?? [])) {
                    ?>
                    <div class="List_Container">
                    <?php
                    foreach($_SESSION['BROWSER_History'] as $showlist) {
                        ?>
                        <a class="HistoryRecord" href="index.php?req=browser&in_search=<?php echo XSS(urldecode($showlist));?>">
                            <svg style="margin-left: 10px;" width="30" height="30" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m5.1504 3.6934c-1.3622 0-2.4824 1.1202-2.4824 2.4824v19.648c0 1.3622 1.1202 2.4824 2.4824 2.4824h21.699c1.3622 0 2.4824-1.1202 2.4824-2.4824v-19.648c0-1.3622-1.1202-2.4824-2.4824-2.4824h-21.699zm.51758 3h20.664v18.613h-10.332c.082455-.094767.13819-.2137.23828-.29492.035209-.069075.063984-.13861.12305-.19336.055047-.11188.15308-.19732.19141-.31641.063358-.090375.1158-.18732.17188-.28125.026472-.061631.07283-.11867.09375-.18359.038201-.067215.063616-.13517.10352-.20117.01151-.084577.078389-.14693.083984-.23438.053219-.073756.063045-.16595.10742-.24414.01847-.1054.08763-.19206.089844-.30273.061883-.09776.049373-.22092.10547-.32031.017648-.098213.031232-.19434.068359-.28711.019402-.11625.016401-.23626.0625-.3457.028993-.19928.012185-.40267.066406-.59766.018745-.3055.030648-.61618-.011718-.91992-.058436-.20654-.008338-.43436-.087891-.63672-.013702-.12599-.02159-.25422-.072266-.37109-.001096-.097573-.039894-.18696-.070312-.27539-.00581-.10883-.074205-.19614-.087891-.30273-.048176-.09286-.048964-.19986-.10742-.28711-.013153-.096877-.077905-.17198-.099609-.26563-.066838-.11941-.11022-.25204-.17773-.37305-.069732-.14329-.15806-.27818-.22656-.41992-.059773-.069126-.097564-.15914-.1582-.22852-.066342-.12791-.17064-.22922-.23047-.35938-.12097-.10022-.18705-.24932-.29883-.35742-.080943-.067428-.13147-.16161-.21094-.22852-.12266-.11924-.23914-.2441-.36524-.35938-.087095-.059631-.14596-.1517-.23438-.20898-.10321-.047354-.16427-.15022-.25781-.20703-.066638-.064467-.16136-.085335-.22266-.1582-.088276-.035905-.14905-.11236-.23047-.1582-.099593-.018079-.15042-.12549-.25-.14453-.058328-.060298-.14463-.06171-.20898-.11328-.057276-.033531-.12057-.065035-.17969-.09375-.07313-.042119-.15122-.067104-.22656-.10547-.059688-.01892-.10732-.077349-.17578-.078126-.0631-.075958-.17107-.043415-.24219-.10352-.095898-.010841-.16229-.090504-.26172-.089844-.065639-.009586-.12033-.072716-.19531-.064453-.079583.009915-.13337-.073882-.2168-.064453-.08819.007684-.15139-.081708-.24609-.066406-.083029.019517-.14366-.033168-.21875-.052735-.11857-.029031-.249.009346-.35547-.058594-.20274-.044153-.41898.002187-.61914-.048828-.14946-.04543-.3084-.035428-.46289-.039062-.15006.001918-.30517.003738-.44727.054687-.20564.029936-.42719-.021075-.62305.058594-.1278.038716-.28398-.004933-.39844.083985-.10056.002849-.21702-.008322-.29688.066406-.08666-.007536-.16939.025424-.24219.064453-.084034-.011987-.15332.04596-.22461.066406-.10211-.007158-.17206.073394-.26758.087891-.048521.035142-.099348.046738-.1582.052734-.06005.022225-.10217.065623-.16797.072266-.066163.052067-.15233.06359-.21875.11523-.079188.021693-.13866.077019-.2168.10156-.055754.039001-.12188.066519-.18164.095703-.068606.017335-.11111.076552-.17969.09375-.067644.076528-.16897.10174-.24414.16797-.091875.070587-.2006.11883-.28516.19922-.12171.070431-.21952.17365-.33008.25781-.10778.046868-.1685.15572-.25977.22656-.028986.024725-.057896.048538-.085937.074219v-9.5098zm14.904 1.7324-.9668 3.6055-3.6055.96875 3.6055.9668.9668 3.6055.96875-3.6055 3.6055-.9668-3.6055-.96875-.96875-3.6055zm-6.457 7.7695c.079092.050554.14771.11726.22656.16602.064638.044067.13483.078127.17969.14648.084792.058999.15257.13626.23828.19531.15103.14757.3151.29081.44336.45703.058817.089692.16306.14394.21094.24414.062179.060629.099913.14257.16211.20312.037406.062676.082412.11277.11328.17969.082028.093445.13545.20949.21289.30664.026055.07993.080439.14414.11719.21875.034699.077386.080104.15018.11133.23047.041621.055997.060505.12761.097657.18555.024899.087174.075034.15866.099609.24609.041895.068101.046577.15403.089844.22266.008933.094541.087359.19002.066406.27734-.16454-.008265-.31747.064772-.48242.054688-.18864.017527-.39033-.012898-.57031.054687-.11672.056838-.23564.14892-.24219.29102-.053377.4595-.023266.92539.015625 1.3848.060592.37147.017495.7518.066406 1.125.013823.14633.15992.24105.30078.2168.012337-.006256.026189-.006016.035156-.003906.00106.000367.004862.00149.005859.001953.000934.000554.004992.003269.00586.003907l.001953.001953c.000729.000787.003248.005007.003906.005859l.001953.001953c.007649.01447.006601.035582-.005859.050781-.08978-.006056-.1786.012748-.25391.058594-.10157.007087-.21371.006769-.29492.076172-.1002.031268-.16068.12546-.25977.16016-.12458.11596-.23937.24868-.37695.35156-.055048.093084-.15906.13732-.24219.19922-.095129.071767-.17883.16116-.25976.24805-.15408.12115-.26005.288-.40625.41406-.083042.13015-.21189.23011-.30664.35352-.068855.086433-.1446.16713-.2207.24414-.077688.18597-.29302.25365-.41016.41016-.041346.042935-.087243.080048-.12305.12695h-.92774c-.000859-.074326-4.3e-5-.14952.00586-.22266.075442-.10518.21557-.15179.25781-.2832.1008-.17155.033918-.38792.10352-.57031.02565-.2045.026532-.41561-.033203-.61328-.013417-.13637-.012836-.27758-.070313-.4043-.00411-.099745-.037241-.19395-.070312-.28516-.021507-.12484-.098711-.22981-.16406-.33594-.071191-.10605-.17757-.18655-.29102-.23828-.16677-.026122-.33688-.032616-.50195-.070313-.14201-.019413-.29142.002808-.42969-.019531-.095769-.061798-.18166-.1369-.2793-.19531-.078019-.044142-.14601-.17792-.050781-.24414.19026-.20983.39909-.41074.59766-.61524.097354-.086949.14739-.21758.24805-.30273.079468-.12472.19372-.22301.30859-.3125.072342-.092802.19404-.12886.2793-.20898.081003-.027229.14773-.077129.22852-.10547.055754-.04788.14326-.007545.19727-.066406.17094-.040279.36729.007614.53906-.052735.51439-.050193 1.0292-.012103 1.5449-.044922.074243-.007251.12385-.065019.19922-.064453.058021-.033449.10945-.067914.16992-.097656.065658-.083578.19176-.089741.24219-.1875.097161-.083331.19933-.17026.26563-.2793.029776-.1167.093997-.22577.080078-.35156.010983-.13768-.056666-.25989-.083985-.39062-.07881-.16308-.23622-.25816-.37891-.35742-.11361-.028199-.19834-.11963-.31055-.15039-.066023-.047129-.14255-.071515-.21289-.11133-.074773-.01226-.14115-.042105-.21094-.068359-.29152-.027706-.58634-.014132-.87891-.007813-.094209.003398-.16611.066692-.25977.050781-.14266.010227-.2973-.003983-.42773.0625-.15324.005848-.31265.019467-.46289.003907-.04634-.056893-.17734-.032161-.16992-.125.029975-.088477.11033-.15187.14453-.24024.1012-.13673.070214-.32347.12695-.47852.026083-.077271.099501-.13098.17578-.1582.1074-.094224.25493-.146.33008-.27344.050587-.084666-.001342-.21796.082031-.27734.11142-.027126.22124-.053716.33789-.046875.27643-.015555.55602.008812.83008-.033203.063007-.03413.12502-.059532.19531-.078125.097561-.04653.18646-.10298.26758-.17578.044684-.010675.083655-.049767.10547-.083985zm1.6602.43555.001953.001953v.001954h-.001953c-.000297-.001714.000209-.002368 0-.003907zm-10.107 5.7148c.050644.002645.10188.005156.15234.009766.078517.066472.1897.032691.27539.064453.071773.049064.1631.039686.22656.10156.10243.039966.17204.13543.25.20898.12319.097522.20054.23862.28516.36523.0297.10333.13528.17271.13867.28516.025436.06433.074028.11016.089844.17969.046696.079699.034655.18384.09375.25976.052199.101.12348.19155.18359.28711.0995.074048.1351.19882.20898.29492.030972.073354.076482.1364.10547.21094.039144.052955.044598.12695.085938.17969.0089994.094985.078979.16198.095703.25391.052043.082389.085633.1754.13477.25977h-1.8594c-.035388-.036398-.070883-.072225-.10742-.10742-.12175-.12177-.24779-.2445-.33789-.39453-.0072831-.006027-.014436-.01322-.021484-.019532v-2.4395z" style="paint-order:stroke fill markers"/></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo XSS($showlist);?></span>
                        </a>
                        <?php
                    }
                    ?>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="EmptyPlaceholder">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24"><path d="M12 8v5h5v-2h-3V8z"></path><path d="M21.292 8.497a8.957 8.957 0 0 0-1.928-2.862 9.004 9.004 0 0 0-4.55-2.452 9.09 9.09 0 0 0-3.626 0 8.965 8.965 0 0 0-4.552 2.453 9.048 9.048 0 0 0-1.928 2.86A8.963 8.963 0 0 0 4 12l.001.025H2L5 16l3-3.975H6.001L6 12a6.957 6.957 0 0 1 1.195-3.913 7.066 7.066 0 0 1 1.891-1.892 7.034 7.034 0 0 1 2.503-1.054 7.003 7.003 0 0 1 8.269 5.445 7.117 7.117 0 0 1 0 2.824 6.936 6.936 0 0 1-1.054 2.503c-.25.371-.537.72-.854 1.036a7.058 7.058 0 0 1-2.225 1.501 6.98 6.98 0 0 1-1.313.408 7.117 7.117 0 0 1-2.823 0 6.957 6.957 0 0 1-2.501-1.053 7.066 7.066 0 0 1-1.037-.855l-1.414 1.414A8.985 8.985 0 0 0 13 21a9.05 9.05 0 0 0 3.503-.707 9.009 9.009 0 0 0 3.959-3.26A8.968 8.968 0 0 0 22 12a8.928 8.928 0 0 0-.708-3.503z"></path></svg>
                        <p class="BelowPlaceholder">Browser history empty</p>
                    </div>
                    <?php
                }
                ?>
                <br>
            </div>
        </div>
        <script>
        // History Modal 
        function showModalHistory() {
            document.getElementById('modalhistory').style.display = 'flex';
        }
        function closeModalHistory() {
            document.getElementById('modalhistory').style.display = 'none';
        }
        </script>
</html>
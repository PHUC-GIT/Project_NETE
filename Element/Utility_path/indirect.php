<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}
// History feature (SESSION)
if (!isset($_SESSION['INDIRECT_History'])) {
    $_SESSION['INDIRECT_History'] = array();
}
$hold_value = '';
if (isset($_GET['in_search'])) {
    $hold_value = $_GET['in_search'];
    // Check if the value is already in the array
    $key = array_search($hold_value, $_SESSION['INDIRECT_History']);

    if ($key !== false) {
        // If found, remove the old, less recent entry
        unset($_SESSION['INDIRECT_History'][$key]);
    }
    
    // Add the value to the beginning of the array
    $_SESSION['INDIRECT_History'] = array_merge([$hold_value], $_SESSION['INDIRECT_History']);
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
            border: 1px solid #FF071F; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(255, 7, 32, 0.3); 
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
            border: 3px solid #FF071F;
            background-color: #FF071F;
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
            color: #FF071F;
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
            background-color:rgba(255, 7, 32, 0.5);
            border-radius: 5px 0px 0px 5px;
            color: white;
            font-family: 'Roboto', sans-serif;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            transition: 0.1s;
            outline: none;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(15px);
            backdrop-filter: blur(15px);
        }

        #Unique_search::placeholder {
            color: #FF071F;
            text-align: center;
        }

        .History_Button {
            padding-left: 10px;
            padding-right: 10px;
            fill: white;
            border: 3px solid #FF071F;
            background-color: #FF071F;
            border-radius: 0px 5px 5px 0px;
            transition: 0.1s;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .History_Button:hover {
            background-color: white;
            border: 3px solid white;
            fill: #FF071F;
        }
    </style>
    <div>
        <div class="outline">
            <div>
                <h1>INDIRECT CAST</h1>
            </div>
            <div>
                <form name="formbrowser" method="get" action="index.php" style="display:flex;">
                    <input type="hidden" name="req" value="indirect">
                    <input type="text" id="Unique_search" name="in_search" placeholder="Youtube Video ID" value="<?php echo XSS($hold_value);?>">
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
                    $pre_value = "https://www.youtube-nocookie.com/embed/";
                    $final_value = $pre_value . $_GET['in_search'];
                    if(!empty($_GET['in_search'])) {
                        ?>  
                            <iframe class="view_value" src="<?php echo XSS($final_value);?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        <?php
                    } else {
                        ?>
                            <div class="view_value">
                                <!-- Information Modal -->
                                <div style="position:fixed;">
                                  <div style="background-color: gray;border: 1px solid white;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; fill: white; cursor:default;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24"><path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"></path></svg>
                                    <br><br>
                                    <span>Your ID Box Empty...</span>
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
                                <span>Enter Video ID To Watch!</span>
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
                max-height: 391px;
                overflow-y: auto;
                overflow-x: hidden;
                scrollbar-color: #FF071F transparent;
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
                background: rgba(255, 7, 32, 0.3);
                text-decoration: none;
                color: white;
                font-weight: bold;
                font-size: 15px;
                min-height: 50px;
                cursor: pointer;
                transition: 0.3s;
            }

            .HistoryRecord:hover {
                background: #FF071F;
            }
        </style>
        <!-- History Modal -->
        <div id="modalhistory" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;width:500px;height:500px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(15px); backdrop-filter: blur(15px); fill: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-weight: bold; font-size: 30px; cursor: default;">Indirect History</span>
                    </div>
                    <div>
                        <button class="cross_button" onclick="closeModalHistory()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                    </div>
                </div>
                <br>
                <div class="List_Container">
                <?php
                foreach($_SESSION['INDIRECT_History'] as $showlist) {
                    ?>
                        <a class="HistoryRecord" href="index.php?req=indirect&in_search=<?php echo XSS(urlencode($showlist));?>">
                            <svg style="margin-left: 10px;" width="32" height="32" version="1.1" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="m5.1504 3.6934c-1.3622 0-2.4824 1.1202-2.4824 2.4824v19.648c0 1.3622 1.1202 2.4824 2.4824 2.4824h21.699c1.3622 0 2.4824-1.1202 2.4824-2.4824v-19.648c0-1.3622-1.1202-2.4824-2.4824-2.4824zm.51758 3h20.664v18.613h-20.664z" stop-color="#000000" style="paint-order:stroke fill markers"/><g><path d="m4.459 3.7656-1.6895 1.8438 11.375 10.391-11.375 10.391 1.6895 1.8438 13.395-12.234z" stop-color="#000000" style="paint-order:stroke fill markers"/><path d="m4.5059 4.8242-.67578.73828 11.428 10.438-11.428 10.438.67578.73828 12.236-11.176z" stop-color="#000000" style="paint-order:stroke fill markers"/></g><path d="m28.321 16-3.9045 1.0483-1.0483 3.9045-1.0483-3.9045-3.9045-1.0483 3.9045-1.0483 1.0483-3.9045 1.0483 3.9045z" stroke-width="1.5935" style="paint-order:stroke fill markers"/></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo XSS($showlist);?></span>
                        </a>
                    <?php
                }
                ?>
                </div>
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
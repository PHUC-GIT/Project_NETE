<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../Index.php');
    die;
}
?>
<html lang="en">
    <!-- Local CSS -->
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

        .outline {
            border: 1px solid #3e30ff; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(62, 48, 255, 0.3); 
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            z-index: 100;
        }

        /* Application UI */

        .img_app {
            width: 45%;
            height: 45%;
            margin-left: auto;
            margin-right: auto;
            margin-top: 10%;
            margin-bottom: -15px;
        }

        .img_app img {
            max-width: 100%;
            max-height: 100%;
            display: block;
        }

        .App{
            width: 200px;
            border-radius: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            display: block;
            cursor: pointer;
            /* Glassy Effect! */
            -webkit-backdrop-filter: blur(7px);
            backdrop-filter: blur(7px);
        }

        .App:hover {
            background-color:rgba(255, 255, 255, 0.4);
        }

        b {
            cursor: pointer;
        }
    </style>
    <div align="center">
        <div class="outline">
            <h1>UTILITY</h1>
        </div>
    </div>
    <h1 align="center" class="text_container">APPS</h1><br>   
        <div class="flex-div" align="center">
            <div class="App" onclick="window.location.href='index.php?req=net';">
                <div>
                    <div class="img_app"><img src="./Resource/Net.png"></div>
                </div>
                <h4><b>Gateway</b></h4>
                <p></p>
            </div>
            <div class="App" onclick="window.location.href='index.php?req=browser';">
                <div>
                    <div class="img_app"><img src="./Resource/Net.png"></div>
                </div>
                <h4><b>Home Browser</b></h4>
                <p></p>
            </div>
            <div class="App" onclick="window.location.href='index.php?req=link';">
                <div>
                    <div class="img_app"><img src="./Resource/Link.png"></div>
                </div>
                <h4><b>Link</b></h4>
            </div>
            <div class="App" onclick="window.location.href='index.php?req=indirect';">
                <div>
                    <div class="img_app"><img src="./Resource/Indirect.png"></div>
                </div>
                <h4><b>Indirect</b></h4>
            </div>
            <div class="App" onclick="window.location.href='index.php?req=draw';">
                <div>
                    <div class="img_app"><img src="./Resource/Draw.png"></div>
                </div>
                <h4><b>Tiny Art</b></h4>
            </div>
        </div>
    <br><br>
    <?php
    if (!isset($_SESSION['AUTHENTICATE_ADMIN'])) {
        ?>
        <h1 align="center" class="text_container">SYSTEM APPS</h1><br>
            <div class="flex-div" align="center">
                <div class="App" onclick="window.location.href='index.php?req=doc';">
                    <div>
                        <div class="img_app"><img src="./Resource/Folder.png"></div>
                    </div>
                    <h4><b>My Files</b></h4>
                    <p></p>
                </div>
                <div class="App" onclick="window.location.href='index.php?req=note';">
                    <div>
                        <div class="img_app"><img src="./Resource/Note.png"></div>
                    </div>
                    <h4><b>Memory Notes</b></h4>
                    <p></p>
                </div>
                <div class="App" onclick="window.location.href='index.php?req=preference';">
                    <div>
                        <div class="img_app"><img src="./Resource/Preference.png"></div>
                    </div>
                    <h4><b>User Preference</b></h4>
                    <p></p>
                </div>
            </div>
        <br><br>
        <?php
    } else {
        ?>
        <h1 align="center" class="text_container">ADMIN SYSTEM</h1><br>
            <div class="flex-div" align="center">
                <div class="App" onclick="window.location.href='index.php?req=track';">
                    <div>
                        <div class="img_app"><img src="./Resource/Peek.png"></div>
                    </div>
                    <h4><b>Tracking Report Case</b></h4>
                    <p></p>
                </div>
            </div>
        <br><br>
        <?php
    }
    ?>
    <h1 align="center" class="text_container">INFORMATION</h1><br>
        <div class="flex-div" align="center">
            <div class="App" onclick="window.location.href='index.php?req=home';">
                <div>
                    <div class="img_app"><img src="./Resource/Home.png"></div>
                </div>
                <h4><b>User Page</b></h4>
                <p></p>
            </div>
            <div class="App" onclick="window.location.href='index.php?req=help';">
                <div>
                    <div class="img_app"><img src="./Resource/Question.png"></div>
                </div>
                <h4><b>Help</b></h4>
                <p></p>
            </div>
        </div>
    <br><br>
        
</html>
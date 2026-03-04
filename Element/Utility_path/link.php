<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: /Index.php');
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
            border: 1px solid #00d40b; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(0, 212, 11, 0.3); 
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
            border: 3px solid #00d40b;
            background-color: #00d40b;
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
            color: #00d40b;
        }

        .btn_card.pointer {
            cursor: pointer;
        }
    </style>
    <div align="center">
        <div class="outline">
            <div>
                <h1>LINK SHIFTER</h1>
            </div>
            <div>
                <button class="btn_card" onclick="history.back()">Return</button>
            </div>
        </div>
    </div>
        <div class="flex-div" align="center">
            <div class="Card">
                <h4><b>STEAM</b></h4>
                <p>Popular games site.</p>
                <button class="btnsmall" onclick=" window.open('https://store.steampowered.com/','_blank')"><img src="Resource/Go_Link.png"></button>
                <p></p>
            </div>
            <div class="Card">
                <h4><b>GAME JOLT</b></h4>
                <p>Fan made games site.</p>
                <button class="btnsmall" onclick=" window.open('https://gamejolt.com/','_blank')"><img src="Resource/Go_Link.png"></button>
            </div>
            <div class="Card">
                <h4><b>REDDIT</b></h4>
                <p>I could smell some chaos in here.</p>
                <button class="btnsmall" onclick=" window.open('https://www.reddit.com/','_blank')"><img src="Resource/Go_Link.png"></button>
            </div>
            <div class="Card">
                <h4><b>YOUTUBE</b></h4>
                <p>Community Video. A lot of AD</p>
                <button class="btnsmall" onclick=" window.open('https://www.youtube.com/','_blank')"><img src="Resource/Go_Link.png"></button>
            </div>
            <div class="Card">
                <h4><b>GITHUB</b></h4>
                <p>Community coder.</p>
                <button class="btnsmall" onclick=" window.open('https://github.com/','_blank')"><img src="Resource/Go_Link.png"></button>
            </div>
            <div class="Card">
                <h4><b>DuckDuckGo</b></h4>
                <p>If you preference to set out independence tab for browser</p>
                <button class="btnsmall" onclick=" window.open('https://duckduckgo.com/','_blank')"><img src="Resource/Go_Link.png"></button>
            </div>
        </div>
</html>
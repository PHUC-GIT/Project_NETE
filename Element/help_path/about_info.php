<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: ../../Index.php');
    die;
}
?>
<html lang="en">
    <style>
        h1, p, span {
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        span {
            font-weight: bold;
        }
        
        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
        }

        .Custom_Link {
            color:rgb(115, 122, 255);
            text-decoration: none;
        }

        .outline {
            border: 1px solid #9441e3; 
            padding: 10px; 
            border-radius: 15px;
            position: fixed;
            top: 4px;
            left: 103px;
            right: 4px;
            background-color:rgba(149, 65, 227, 0.3); 
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
            border: 3px solid #9441e3;
            background-color: #9441e3;
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
            color: #9441e3;
        }

        .btn_card.pointer {
            cursor: pointer;
        }
    </style>
    <div>
        <div class="outline">
            <h1>ABOUT / APPLICATION VERSION</h1>
            <div>
                <button class="btn_card pointer" onclick="history.back()">Return</button>
            </div>
        </div>
    </div>
    <div class="flex_div span_container">
        <span>
            ABOUT<br>
            Made By: <a class="Custom_Link" href="https://github.com/PHUC-GIT" target="_blank">PHUC-GIT</a><br>
            <br>
            NET.ESCAPE CRYSTAL GEMS © 2024-2026 ALL RIGHT RESERVED<br>
            Release: v0.4.2 - Build: 126.22032026<br> <!-- Away remember to changing this when you mess something in source. -->
            <br>
            GitHub Page: <a class="Custom_Link" href="https://github.com/PHUC-GIT/Project_NETE" target="_blank">Project_NET.E</a>
            <br>
            <details>
            <summary>
                Attribute & Credit
            </summary>
            <a>Library: <a class="Custom_Link" href="https://blog.jquery.com/2022/12/20/jquery-3-6-3-released-a-quick-selector-fix/" target="_blank">Jquery-3.6.3.min</a> - <a class="Custom_Link" href="https://code.jquery.com/jquery-3.6.3.min.js"> Download</a></a> (MIT License)</a></a><br>
            <a>Interactive Icons (Inline SVG, Icons image): <a class="Custom_Link" href="https://github.com/atisawd/boxicons" target="_blank">Boxicons V2</a> (MIT License)</a><br>
            <a>Interactive Icons (Icons image): <a class="Custom_Link" href="https://github.com/feathericons/feather" target="_blank">Feathericons</a> (MIT License)</a><br>
            <a>Fonts: <a class="Custom_Link" href="https://github.com/googlefonts/roboto-3-classic" target="_blank">Roboto Fonts</a> (OLF-1.1 License)</a><br>
            <a>Blocky Crystal Image: <a class="Custom_Link" href="https://unsplash.com/@pawel_czerwinski" target="_blank">Pawel Czerwinski</a> - <a class="Custom_Link" href="https://unsplash.com/license"> Unsplash</a></a><br>
            <a>2 People On The Boat Image: <a class="Custom_Link" href="https://www.pexels.com/photo/2-people-on-the-boat-2166711/" target="_blank">Quang Nguyen Vinh</a><br>
            <a>Aurora Borealis Image: <a class="Custom_Link" href="https://www.pexels.com/photo/aurora-borealis-1933239/" target="_blank">Stein Egil Liland</a><br>
            <a>Purple and pink diamond on blue background Image: <a class="Custom_Link" href="https://www.pexels.com/photo/purple-and-pink-diamond-on-blue-background-5011647/" target="_blank">Rostislav Uzunov</a><br>
            <a>Trees And Fern In Forest Image: <a class="Custom_Link" href="https://www.pexels.com/photo/trees-and-fern-in-forest-17893049/" target="_blank">Lev Strelchenko</a><br>
            </details>
        </span>
    </div>
    <p></p>
        <h1 align="center">History Of Development Logo</h1>
        <div class="Logo">
            <img src="Resource/Netscape-redesign-RE-VER2.png">
            <p align="center">NET.E Logo (9/2025)</p>
        </div>
        <p></p>
        <div class="Logo">
            <img src="Resource/NET_ESCAPE.png">
            <p align="center">NET.E Logo (2/2025)</p>
        </div>
        <p></p>
        <div class="Logo">
            <img align="center" src="Resource/Logo_For_Web.png" style="margin-left: 120px;">
            <p align="center">Homepage Logo (10/2024)</p>
        </div>
        <p></p>
        <div class="Logo">
            <img align="center" src="Resource/WEB_LOGO.png" style="margin-left: 120px;">
            <p align="center">Oldest But Joke Logo (6/2024)</p>
        </div>
</html>
<!DOCTYPE html>
<?php
// Check if someone direct URL without index.php
if (!defined('NETE_INTEGRITY_CHECK')) {
    header('location: /Index.php');
    die;
}
?>
<html lang="en">
    <style>
        h1, p, span {
            color: white;
            font-family: 'Roboto', sans-serif;
        }
        
        h1 {
            margin: 0;
            padding: 5px;
            font-size: 1.5rem;
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
    <div align="center">
        <div class="outline">
            <h1>PRIVACY</h1>
            <div>
                <button class="btn_card pointer" onclick="history.back()">Return</button>
            </div>
        </div>
    </div>
    <div class="flex_div span_container">
        <span>
            Your Privacy (For Example)<br>
            <br>
            Last Updated: 16/10/2025<br>
            <br>
            At NET.ESCAPE ("we," "us," or "our"), we are committed to protecting your privacy and ensuring a safe<br>
            and secure user experience. This Privacy Policy outlines the types of data we collect, how we use it, and the<br> 
            security measures we take to protect it, as well as how we handle any tracking logs related to unauthorized<br> 
            access attempts. By using our website, you agree to the collection and use of information in accordance with<br> 
            this policy.<br>
            <br>
            1. Data you proved.<br>
            a. Your files you upload.<br>
                - When you upload your file and set your files as share. You understand that you share your file for other user.<br>
                  Please do not upload file document that containt your private info.<br>
            <br>
            2. Security.<br>
            a. Recorded your data.<br>
                - Our system store metadata of your upload data for serving files properly<br>
            b. No cookie.<br>
                - Our system don't relied on heavy cookie so some of option you set like in "My Files" for example maybe reset on change data like upload, delete, update. (sorry for the inconvenience)<br> 
        </span>
    </div>
</html>
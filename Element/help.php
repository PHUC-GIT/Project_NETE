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
        h1, h2, h3, h4, p, span {
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
            margin-left: 10px;
            transition: 0.1s;
            cursor: pointer;
        }

        .btn_card:hover {
            background-color: white;
            border: 3px solid white;
            color: #9441e3;
        }
    </style>
    <div>
        <div class="outline">
            <h1>HELP / INFORMATION</h1>
            <div>
                <button class="btn_card pointer" onclick="window.location.href='index.php?req=privacy';">Privacy</button>
                <button class="btn_card pointer" onclick="window.location.href='index.php?req=about';">About</button>
            </div> 
        </div> 
    </div>
    <div class="flex_div span_container">
        <span>
            <h2>WELCOME TO NET.E: Crystal Gems</h2><br>
            <br>
            Getting Started: Side dock is the main navigation that help you moving around the system, Bottom Dock show you quick action and basic information.<br>
            <br>
            FEATURE:<br>
            <br>
            Apps (Small Utility): Contain selection of tools help to interact with Internet and other that usually don't touched the system part.<br>
            <br>
            + Gateway: The multi search engine that you can choiced search engine and input search.<br>
            + Home Browser: Peek other website using Iframe.<br>
            + Link: Hardcode link to other website.<br>
            + Indirect: Feed Youtube Video by video ID and using youtube no cookie embeded link.<br>
            + Tiny Art: A simple Art tool that you can draw and save your art work.<br>
            + History in Indirect, Home Browser: This will stored your input data but it's short life and will gone once you log off.<br>
            <br>
            File Manager (My Files): The storage of this self-host system that you can upload, download, review (Base on file that support by browser codec).<br>
            <br>
            + Upload limited: You can only upload one file each (upload archive instead if you want include multi file) and maximum 1GB size per upload.<br>
            + Edit File (Card): You can change display name for your file and move it to other folder and also set share mode for this file.<br>
            + Folder Structure: System allow you to move file around folder and also create folder then edit it name afterward.<br>
            + Edit Folder: you can only change display name for folder.<br>
            + Public Files: It's the shared file section where other user on system can access, download your file. You can also report suspection if you see violation winthin the file.<br>
            + Delete Data: When you delete file or folder the system will make confirm before do it so make a choice wisely because there is no trash bin. When delete folder, it will deleted all folder type and file inside that folder.<br>
            + Tamper Shield: System will check on your file integrity after while and will show status eye shield while doing so. If you see the shield turn red then you shouldn't interact with the file.<br>
            + Edit Content Files: You now able to created your file right on the server and you can also edit the file content itself but only type is supported.<br>
            <br>
            Memory Note: Simple note section that you can write down anything like your though, thing you found on internet.<br>    
            <br>
            + Create Note: You can click the big button that have note with plus icons, that will created blank note for you.<br>
            + Edit Note: Like simple note, you just click inside the note form and type as you please. Also you can set note color and note pirvacy from here.<br>
            + Public Note: When Note option have been set to public it will become visible to Public Note section that everyone can see what you write, and you can report suspection report too!<br>
            + Color: Note currently have 6 color. (Black, Red, Blue, Green, Yellow, Purple)<br>
            + Save Feature: Note will save when you stop edit or change note setting options.
            + Delete Note: To delete note. You just need click the trash bin icons right below, and the note will be gone.<br>
            <br>
            User Preference: User setting page.<br>
            <br>
            + Allow change background.<br>
            <br>
        </span>
    </div>
</html>
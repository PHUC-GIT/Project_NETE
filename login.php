<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)
ini_set('session.cookie_httponly', '1'); 
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', '1'); // Change it to '0' if you're run it on http localhost or without secured HTTPS but only if something unexpect happened.  
session_start();
header("X-Frame-Options: DENY");
header('Referrer-Policy: strict-origin-when-cross-origin');
header("X-Content-Type-Options: nosniff");
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <style>
        h4, p, a{
            color: white;
            font-family: 'Roboto', sans-serif;
        }

        .tiny_font {
            font-size: 1rem;
            cursor: default;
        }

        .Brand {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
            color: white;
            cursor: default;
        }

        #username::placeholder, #password::placeholder {
            color: #929292;
        }

        /* Display sector */
        body {
            max-width: 100%;
            overflow-x: hidden;
            background: #000000 url(Resource/Background/Begin.png) no-repeat fixed center;
            background-size: 100% 100%;
        }
    </style>
        <!-- Resource Link and Script -->
        <title>Login for User</title>
        <link rel="icon" type="image/x-icon" href="Resource/Web_ICO.png">
        <link type="text/css" rel="stylesheet" href="Style/fancy.css"/>
        <script src="JS/jquery-3.6.3.min.js" type="text/javascript"></script>
        <script src="JS/Function.js" type="text/javascript"></script>
    </head>
    <body>
        <div class="loginbody_pose">
            <div id="loginbody" align="center">
                <h4>LOGIN SYSTEM USER</h4>
                <div class="img_inside"><img src="./Resource/logo_white.png"></div>
                <form name="formlogin" method="post" action="Element/User/userverifyact.php">
                    <input type="hidden" name="reqact" value="checklogin">
                    <p></p>
                    <a class="Brand">NET.E SYSTEM</a>
                    <p></p>
                    <input type="text" name="username" id="username" placeholder="User Name" required/>
                    <p></p>
                    <input type="password" name="password" id="password" placeholder="Password" required/>
                    <p></p>
                    <input type="checkbox" name="show_ip" id="show_ip" value="Hide_IP" checked/><a class="tiny_font">Hide My IP</a>
                    <p></p>
                    <input id="btn_login" type="submit" value="Login"/>
                    <p></p>
                </form>
            </div>
        </div>
    </body>
    <!-- Modal System (Experimental) -->
        <style>
            .normal_button {
                padding: 8px 20px; 
                border: none; 
                background: #0081d6;
                color: white; 
                border-radius: 5px;
                font-family: 'Roboto', sans-serif; 
                font-weight: bold; 
                cursor: pointer;
            }
            .normal_button:hover {
                background: white; 
                color: #0081d6;
            }
        </style>
        <!-- Message Modal -->
        <div id="modalMessage" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(15px); backdrop-filter: blur(15px); fill: white;">
                <div style="display: flex; justify-content: right; align-items: center;">
                    <button class="cross_button" onclick="closeModalMessage()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" width="45" height="45" viewBox="0 0 24 24"><path d="M12.884 2.532c-.346-.654-1.422-.654-1.768 0l-9 17A.999.999 0 0 0 3 21h18a.998.998 0 0 0 .883-1.467L12.884 2.532zM13 18h-2v-2h2v2zm-2-4V9h2l.001 5H11z"/></svg>
                <br><br>
                <span id="modalText"></span>
                <br><br>
                <button class="normal_button" onclick="closeModalMessage()">Ok</button>
            </div>
        </div>
        <script>
        // Message modal. No logic beside the close button.
        function showModalMessage(msg) {
            document.getElementById('modalText').innerText = msg;
            document.getElementById('modalMessage').style.display = 'flex';
        }
        function closeModalMessage() {
            document.getElementById('modalMessage').style.display = 'none';
        }
        </script>
        <!-- Return Respond Pop Up -->
        <?php
        if (isset(($_SESSION['MODAL_ERROR_MESSAGE']))) {
            ?>
            <script>
                showModalMessage("<?php echo htmlspecialchars($_SESSION['MODAL_ERROR_MESSAGE'][1], ENT_QUOTES, 'UTF-8');?>");
            </script>
            <?php
        } else {

        }
        unset($_SESSION['MODAL_ERROR_MESSAGE']);
        ?>
</html>
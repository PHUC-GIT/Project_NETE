<!DOCTYPE html>
<?php
ini_set('session.cookie_httponly', '1'); 
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.cookie_secure', '1'); // Change it to '0' if you're run it on http localhost or without secured HTTPS but only if something unexpect happened.
session_start();
header("X-Frame-Options: DENY");
header('Referrer-Policy: strict-origin-when-cross-origin');
header("X-Content-Type-Options: nosniff");
define('NETE_INTEGRITY_CHECK', TRUE); // Everything must go through index.php
// Get user name variable. Use it anywhere or pass this variable to other variable
if (isset($_GET['req']) && $_GET['req'] === 'indirect') {
    header("Content-Security-Policy: frame-src 'self' https://www.youtube-nocookie.com/;");
}
// Check if user is login.
if (!isset($_SESSION['AUTHENTICATE_USER']) && !isset($_SESSION['AUTHENTICATE_ADMIN'])) {
    header('location:login.php');
    die;
}
// Global string
require "Element/Database/usercls.php";
$getuserinfo = new user();
$name_login = null;
if (isset($_SESSION['AUTHENTICATE_USER'])) {
    // Called database string
    require "Element/Database/prefercls.php";
    $getpreferinto = new preference();
    
    $getinfo = $getpreferinto->preferenceinfo();
    $value = $getinfo;
    $getusername = $getuserinfo->user_NAME($_SESSION['AUTHENTICATE_USER'])->username;
    $name_login = $getusername;
    $user_id = $_SESSION['AUTHENTICATE_USER'];

}
if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
    $name_login = "ROOT";
    $value = "B1";
    $user_id = 0;
}
$user_ip = isset($_SESSION['USER_ADDR_IP']) ? $_SESSION['USER_ADDR_IP'] : 'No Ip detected';

// Wrapper

function XSS($input) {
    return htmlspecialchars((string)$input, ENT_QUOTES, 'UTF-8');
}

function Dynamic_Items_Counter($item_input) {
    if ($item_input == 0) {
        return $item_input . " Items";
    }
    elseif ($item_input == 1) {
        return $item_input . " Item";
    }
    elseif ($item_input > 1) {
        return $item_input . " Items";
    }
}

// Active to regen
function regen_section() {
    $_SESSION['Section_Status'] = [
    'Home' => '', // Home section
    'Home_Disable' => '',
    'Utility' => '', // Utility section
    'Utility_Disable' => '',
    'Doc' => '', // Doc section
    'Doc_Disable' => '',
    'Note' => '', // Note section
    'Note_Disable' => '',
    'Help' => '', // Help section
    'Help_Disable' => '',
    'Track' => '', // Track section
    'Track_Disable' => '',
    'Key' => '', // Key section
    'Key_Disable' => '',
    'Preference' => '', // Setting section
    'Preference_Disable' => '',
    ];

    $_SESSION['Predefined_Icons_State'] = [
    'Home' => 'Resource/Side_Icons/User_Inactive.png', // Home Icons
    'Utility' => 'Resource/Side_Icons/Utility_Inactive.png', // Utlity Icons
    'Doc' => 'Resource/Side_Icons/File_Inactive.png', // Doc Icons
    'Note' => 'Resource/Side_Icons/Note_Inactive.png', // Note Icons
    'Help' => 'Resource/Side_Icons/Help_Inactive.png', // Help Icons
    'Track' => 'Resource/Side_Icons/Peek_Inactive.png', // Track Icons
    'Key' => 'Resource/Side_Icons/Key_Inactive.png', //Key Icons
    'Preference' => 'Resource/Side_Icons/Preference_Inactive.png',  // Setting Icons
    ];
}

// Make new session if it's not exist.
if (!isset($_SESSION['Predefined_Icons_State'])) {
    $_SESSION['Predefined_Icons_State'] = [
    'Home' => 'Resource/Side_Icons/User_Inactive.png', // Home Icons
    'Utility' => 'Resource/Side_Icons/Utility_Inactive.png', // Utlity Icons
    'Doc' => 'Resource/Side_Icons/File_Inactive.png', // Doc Icons
    'Note' => 'Resource/Side_Icons/Note_Inactive.png', // Note Icons
    'Help' => 'Resource/Side_Icons/Help_Inactive.png', // Help Icons
    'Track' => 'Resource/Side_Icons/Peek_Inactive.png', // Track Icons
    'Key' => 'Resource/Side_Icons/Key_Inactive.png', //Key Icons
    'Preference' => 'Resource/Side_Icons/Preference_Inactive.png',  // Setting Icons
    ];
}
// Passive Regen if it not exist
if (!isset($_SESSION['Section_Status'])) {
    $_SESSION['Section_Status'] = [
    'Home' => '', // Home section
    'Home_Disable' => '',
    'Utility' => '', // Utility section
    'Utility_Disable' => '',
    'Doc' => '', // Doc section
    'Doc_Disable' => '',
    'Note' => '', // Note section
    'Note_Disable' => '',
    'Help' => '', // Help section
    'Help_Disable' => '',
    'Track' => '', // Track section
    'Track_Disable' => '',
    'Key' => '', // Key section
    'Key_Disable' => '',
    'Preference' => '', // Setting section
    'Preference_Disable' => '',
    ];
}



if (isset($_GET['req'])) {
    $section_value = $_GET['req'];
    switch($section_value) {
        case 'home':
            regen_section();
            $_SESSION['Section_Status']['Home'] = 'active';
            $_SESSION['Section_Status']['Home_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Home'] = 'Resource/Side_Icons/User_Active.png';
            break;
        case 'utility':
            regen_section();
            $_SESSION['Section_Status']['Utility'] = 'active';
            $_SESSION['Section_Status']['Utility_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Utility'] = 'Resource/Side_Icons/Utility_Active.png';
            break;
        case 'help':
            regen_section();
            $_SESSION['Section_Status']['Help'] = 'active';
            $_SESSION['Section_Status']['Help_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Help'] = 'Resource/Side_Icons/Help_Active.png';
            break;
        case 'doc':
        case 'upload_file':
            regen_section();
            $_SESSION['Section_Status']['Doc'] = 'active';
            $_SESSION['Section_Status']['Doc_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Doc'] = 'Resource/Side_Icons/File_Active.png';
            break;
        case 'note':
            regen_section();
            $_SESSION['Section_Status']['Note'] = 'active';
            $_SESSION['Section_Status']['Note_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Note'] = 'Resource/Side_Icons/Note_Active.png';
            break;
        case 'track':
        case 'report_eye_note':
        case 'report_eye_file';
            regen_section();
            $_SESSION['Section_Status']['Track'] = 'active';
            $_SESSION['Section_Status']['Track_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Track'] = 'Resource/Side_Icons/Peek_Active.png';
            break;
        case 'user':
            regen_section();
            $_SESSION['Section_Status']['Key'] = 'active';
            $_SESSION['Section_Status']['Key_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Key'] = 'Resource/Side_Icons/Key_Active.png';
            break;
        case 'preference':
            regen_section();
            $_SESSION['Section_Status']['Preference'] = 'active';
            $_SESSION['Section_Status']['Preference_Disable'] = 'disabled';
            $_SESSION['Predefined_Icons_State']['Preference'] = 'Resource/Side_Icons/Preference_Active.png';
            break;
        
        // case for "Preference" here when it's complete...
    }
} else {
    regen_section();
}
?>
<html lang="en">
    <head>
        <?php
        if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
            ?>
            <title>NET.E Admin Interface</title>
            <?php
        } else {
            ?>
            <title>NET.E User Interface</title>
            <?php
        }
        ?>
        <!-- Meta Data -->
        <meta charset="UTF-8">

        <!-- Resource Link and Script -->
        <link rel="icon" type="image/x-icon" href="Resource/Web_ICO.png">
        <link type="text/css" rel="stylesheet" href="Style/fancy.css"/>
        <script src="JS/jquery-3.6.3.min.js" type="text/javascript"></script>
        <script src="JS/Function.js" type="text/javascript"></script>
        <script src="JS/droplistfix.js" type="text/javascript"></script>
        <?php
        // Preference for background image.
        $presetbackground = array(
            "B1" => "Resource/Background/B1.png",
            "B2" => "Resource/Background/B2.png",
            "B3" => "Resource/Background/B3.jpg",
            "B4" => "Resource/Background/B4.jpg",
            "B5" => "Resource/Background/B5.jpg",
            "B6" => "Resource/Background/B6.jpg",
            "B7" => "Resource/Background/B7.jpg",
        )
        ?>
        <style>
            /* Display background */
            body {
                max-width: 100%;
                overflow-x: hidden;
                background: #000000 url(<?php echo XSS($presetbackground[$value]);?>) no-repeat fixed center;
                background-size: 100% 100%;
            }
        </style>
    </head>
    <body>
        <!-- GSOD (Gray Screen Of Dead!)-->
        <div id="server-warning" class="dead_card"></div>
        <!-- Dead Modal -->
        <div id="modaldead" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center; pointer-events:all; cursor:none;">
          <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px); fill: red;">
            <svg width="45" height="45" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m4 3c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2h7v2h-3v2h8v-2h-3v-2h7c1.103 0 2-.897 2-2v-11c0-1.103-.897-2-2-2h-16zm0 2h16l.001953 9h-16.002v-9zm4.4746 1v1.0312l2.4687 2.4688-2.4687 2.5v1h1.0312l2.4687-2.4688 2.5 2.4688h1v-1.0312l-2.4688-2.4688 2.4688-2.4688v-1.0312h-1l-2.5 2.4688-2.4687-2.4688h-1.0312z"/></svg>
            <br><br>
            <span>Connection Interrupted</span>
          </div>
        </div>
        <script src="JS/RealTime.js" type="text/javascript"></script>
        <header>    
            <div class="Sidebar">
                <button class="btn pointer SidebarItem Home <?php echo XSS($_SESSION['Section_Status']['Home']);?>" onclick="window.location.href='index.php?req=home';" <?php echo XSS($_SESSION['Section_Status']['Home_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Home']);?>"></button>
                <button class="btn pointer SidebarItem Utility <?php echo XSS($_SESSION['Section_Status']['Utility']);?>" onclick="window.location.href='index.php?req=utility';" <?php echo XSS($_SESSION['Section_Status']['Utility_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Utility']);?>"></button>
                <?php
                if (!isset($_SESSION['AUTHENTICATE_ADMIN'])) {
                    ?>
                    <button class="btn pointer SidebarItem Doc <?php echo XSS($_SESSION['Section_Status']['Doc']);?>" onclick="window.location.href='index.php?req=doc';" <?php echo XSS($_SESSION['Section_Status']['Doc_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Doc']);?>"></button>
                    <button class="btn pointer SidebarItem Note <?php echo XSS($_SESSION['Section_Status']['Note']);?>" onclick="window.location.href='index.php?req=note';" <?php echo XSS($_SESSION['Section_Status']['Note_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Note']);?>"></button>
                    <button class="btn pointer SidebarItem Preference <?php echo XSS($_SESSION['Section_Status']['Preference']);?>" onclick="window.location.href='index.php?req=preference';" <?php echo XSS($_SESSION['Section_Status']['Preference_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Preference']);?>"></button>
                    <?php
                }
                ?>
                <button class="btn pointer SidebarItem Help <?php echo XSS($_SESSION['Section_Status']['Help']);?>" onclick="window.location.href='index.php?req=help';" <?php echo XSS($_SESSION['Section_Status']['Help_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Help']);?>"></button>
                <?php
                if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
                    ?>
                    <button class="btn pointer SidebarItem Track <?php echo XSS($_SESSION['Section_Status']['Track']);?>" onclick="window.location.href='index.php?req=track';" <?php echo XSS($_SESSION['Section_Status']['Track_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Track']);?>"></button>
                    <button class="btn pointer SidebarItem Key <?php echo XSS($_SESSION['Section_Status']['Key']);?>" onclick="window.location.href='index.php?req=user';" <?php echo XSS($_SESSION['Section_Status']['Key_Disable']);?>><img src="<?php echo XSS($_SESSION['Predefined_Icons_State']['Key']);?>"></button>
                    <?php
                }
                ?>
                <p></p>
            </div>
            <div class="Bottombar">
                <div style="display: flex;">
                    <a class="btn_gems_buttom MAIN_Q" onclick="showModalMenu()"><svg width="63.733mm" height="62.145mm" version="1.1" viewBox="0 0 63.733 62.145" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-81.24 -80.998)"><path d="m113.11 80.998c-17.57 0-31.865 13.908-31.865 31.072 0 17.164 14.295 31.073 31.865 31.073 17.57 0 31.867-13.909 31.867-31.073 0-17.164-14.297-31.072-31.867-31.072zm-2.3874 19.855c-.039.33031.0766.76435-.0563 1.0315-.98186.38617-2.1894.44525-2.9383 1.2625-.2364.3544-.0516.81899-.10749 1.2144.0107 2.4982.13695 4.9932.24495 7.4884.12331 3.5882.17594 7.178.20981 10.768-.60825.023-1.2165.046-1.8247.0692-4.4457-4.2995-8.8374-8.6527-13.336-12.901-1.4339-1.3022-2.8663-2.6143-4.3811-3.8256-.14656.13236-.03314.43313-.07493.61856.07043 3.4996.09772 7.0012.23978 10.499.10113 1.0877.04977 2.2064.3421 3.2675.16838.47344.80108.36697 1.2004.45165.74103.0876 1.5062.10884 2.2293.26406.04302.4316.0618.86503.09147 1.2976-2.0639-.0782-4.1221-.33399-6.1908-.22944-.33435.0206-.67606.0213-1.0154.032-.23336-.60848-.44615-1.227-.63769-1.8542.73705.0837 1.494.0424 2.2252-.0667.56044-.1814.52508-.877.56689-1.3415.17372-4.0548.09685-8.1153.07235-12.172-.0537-.97645.05876-1.9942-.26665-2.9347-.34138-.7758-1.1495-1.2574-1.9249-1.5555-.027-.006-.05488-.008-.08268-.007-.01909.005-.03691.0104-.05374.0176.15878-.42698.3278-.84928.50643-1.2666.28743.004.58161-.005.85938.0124 1.1135.0398 2.2326-.0652 3.3409-.003.65025.33611 1.1254.9244 1.6702 1.3963 4.9225 4.6774 9.7451 9.4541 14.692 14.107.17956.11913.0737-.27016.10386-.34726-.036-2.7126-.2615-5.4179-.33951-8.1292-.0919-1.5794-.18919-3.1624-.38706-4.7325-.0737-.56472-.78186-.6695-1.2413-.7984-.63271-.13062-1.2684-.24821-1.8826-.44338.0717-.28619-.14626-.82858.11989-.94258 2.6701-.14675 5.3513-.16236 8.0259-.24753zm10.566.0284c1.8778.19061 3.7635.3913 5.656.30851 4.3313-.14099 8.6657-.27558 12.999-.21549.17236-.003.35311.0221.53175.0388.59477 1.3941 1.0803 2.8437 1.4464 4.3382-.008.0295-.007.0623.009.094-.007.19589.16414.58307-.16019.59842-.32821.0169-.66274-.0121-.99477-.015-.49722-1.246-1.1848-2.4316-2.0541-3.4664-.25912-.28077-.713-.14216-1.0516-.23255-1.8796-.21329-3.7717-.1924-5.6606-.1912-.79688.0127-1.5979-.0112-2.3921.047-.088.78213-.0278 1.6002-.0517 2.3952.008 1.858-.0208 3.7187.0491 5.5748.47381.11781 1.0082.0434 1.5028.0729 1.5533-.0196 3.1281.0115 4.6571-.29042.75483-.16093.95351-.98933 1.1193-1.6138.13338-.51401.24268-1.0331.36225-1.5503.35613-.0104.71203-.0211 1.0682-.0315.13437 1.7023.0368 3.4148.077 5.1211.005 1.0247.0325 2.0487.0713 3.0727-.36825-.0104-.73659-.0211-1.1048-.0315-.25704-.93968-.37408-1.9337-.81028-2.8164-.36074-.62367-1.1993-.56202-1.8299-.61443-1.7044-.0335-3.4124-.023-5.1134.0971-.0647 1.1144-.008 2.2752-.0258 3.4075.0158 1.4184-.0231 2.842.0631 4.2571.18714.57094.88498.67121 1.4113.71261 2.0455.2108 4.1192.17359 6.1619-.0517.94686-.0829 1.8936-.37688 2.6071-1.0108 1.0262-.85248 1.7192-2.0258 2.1668-3.2525.15659-.0371.34515-.0176.51263-.0408-.2881 2.3051-.85538 4.5265-1.6681 6.6306-.69087-.0153-1.3867.002-2.0764-.0393-4.9589-.19434-9.9254-.20312-14.886-.0837-.90463.0377-1.8115.0428-2.714.11627.0463-.34479-.0865-.75055.0713-1.064 1.1841-.23069 2.4361-.23447 3.5688-.67386.31964-.22382.19519-.69509.25476-1.0232.0596-4.1852-.0244-8.3713-.0248-12.557.0133-1.3628-.0748-2.7293-.20981-4.0824-.22417-.35773-.75546-.3038-1.1193-.42013-.82962-.16376-1.6808-.18642-2.5156-.29508.0195-.40801.0376-.81569.0971-1.2196zm-5.5986 17.004c.0752-.004.15191-.001.22996.007 1.0205-.0593 2.0772.53886 2.4303 1.4795.15644.67302.1289 1.436-.21962 2.049-.6983 1.0898-2.3165 1.4216-3.4148.71417-.66714-.40399-1.1038-1.1588-1.0036-1.928-.0712-.60606.16768-1.2113.63097-1.6242.36556-.36978.82039-.67148 1.3467-.69712zm5.5893 7.3768c2.4875.004 5.0303.65005 7.3334 1.5234 3.2148 1.2191 5.9138 2.8656 7.2512 3.7491-5.4285 6.3314-13.599 10.361-22.754 10.361-6.3794 0-12.28-1.9564-17.109-5.285 1.0464.10994 2.1018.0348 3.1393-.14521 3.6687-.46494 8.6101-2.5547 15.136-7.8708 2.0829-1.6968 4.5152-2.3359 7.0027-2.3322z" fill-rule="evenodd" stop-color="#000000" stroke-linecap="round" stroke-linejoin="round" style="paint-order:markers fill stroke"/></g></svg>NET.E</a>
                    <?php
                    if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
                    ?>
                        <a title="Reported Notes" class="btn_gems_buttom REPORT_Q" href="index.php?req=report_eye_note"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m5 3a2 2 0 00-2 2v14a2 2 0 002 2h8l8-8v-8a2 2 0 00-2-2h-14zm3.9512 1.3223c3.0863 0 4.7305 3.4004 4.7305 3.4004a.39766.39766 0 010 .35547s-1.6442 3.4004-4.7305 3.4004c-3.0863 0-4.7285-3.4004-4.7285-3.4004a.39766.39766 0 010-.35547s1.6423-3.4004 4.7285-3.4004zm0 .79492c-2.3314 0-3.717 2.4689-3.8887 2.7832.17162.31428 1.5573 2.7832 3.8887 2.7832 2.3314 0 3.717-2.4689 3.8887-2.7832-.17162-.31428-1.5573-2.7832-3.8887-2.7832zm0 1.1934c.87368 0 1.5898.71616 1.5898 1.5898 0 .87368-.71616 1.5898-1.5898 1.5898s-1.5898-.71616-1.5898-1.5898c0-.87368.71616-1.5898 1.5898-1.5898zm3.0488 5.6895h7l-7 7v-7z"/></svg>Reported Notes</a>
                        <a title="Reported Files" class="btn_gems_buttom REPORT_Q" href="index.php?req=report_eye_file"><svg width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5zm-1.3008 7.7363c3.0863 0 4.7285 3.4004 4.7285 3.4004a.39766.39766 0 010 .35547c0-2e-6-1.6423 3.4004-4.7285 3.4004-3.0863 0-4.7305-3.4004-4.7305-3.4004a.39766.39766 0 010-.35547s1.6442-3.4004 4.7305-3.4004zm0 .79492c-2.3314 0-3.717 2.4689-3.8887 2.7832.17162.31428 1.5573 2.7832 3.8887 2.7832 2.3314 0 3.717-2.4689 3.8887-2.7832-.17162-.31428-1.5573-2.7832-3.8887-2.7832zm0 1.1934c.87368 0 1.5898.71616 1.5898 1.5898 2e-6.87368-.71616 1.5898-1.5898 1.5898s-1.5918-.71616-1.5918-1.5898c0-.87368.71812-1.5898 1.5918-1.5898z"/></svg>Reported Files</a>
                    <?php
                    }
                    ?>
                </div>
                <div style="display: flex;">
                    <a class="info_bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: white;"><path d="m21.484 7.125-9.022-5a1.003 1.003 0 0 0-.968 0l-8.978 4.96a1 1 0 0 0-.003 1.748l9.022 5.04a.995.995 0 0 0 .973.001l8.978-5a1 1 0 0 0-.002-1.749z"/><path d="m12 15.856-8.515-4.73-.971 1.748 9 5a1 1 0 0 0 .971 0l9-5-.971-1.748L12 15.856z"/><path d="m12 19.856-8.515-4.73-.971 1.748 9 5a1 1 0 0 0 .971 0l9-5-.971-1.748L12 19.856z"/></svg> 109.06032026</a>
                    <a class="info_bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: white;"><path d="M7.5 6.5C7.5 8.981 9.519 11 12 11s4.5-2.019 4.5-4.5S14.481 2 12 2 7.5 4.019 7.5 6.5zM20 21h1v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h17z"></path></svg><?php echo XSS($name_login ?? '')?></a>
                    <a class="info_bottom"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: white;"><circle cx="12" cy="12" r="4"></circle><path d="M13 4.069V2h-2v2.069A8.01 8.01 0 0 0 4.069 11H2v2h2.069A8.008 8.008 0 0 0 11 19.931V22h2v-2.069A8.007 8.007 0 0 0 19.931 13H22v-2h-2.069A8.008 8.008 0 0 0 13 4.069zM12 18c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6z"></path></svg> <?php echo XSS($user_ip)?></a>
                    <a class="info_bottom" id="timeDisplay" style="margin-right: 5px;">00/00/0000 | 0:00 ?M</a>
                    <script src="JS/Sub_Func.js" type="text/javascript"></script>
                </div>
            </div>
        </header>
        <!-- Modal System (Experimental) -->
        <style>
            .del_button {
                padding: 8px 20px; 
                border: none; 
                background: #d62828; 
                color: white; 
                border-radius: 5px;
                font-family: 'Roboto', sans-serif; 
                font-weight: bold; 
                margin-right: 10px; 
                transition: 0.1s; 
                cursor: pointer;
            }
            .del_button:hover {
                background: white;
                color: #d62828;
            }

            .normal_button {
                padding: 8px 20px; 
                border: none; 
                background: #0081d6;
                color: white; 
                border-radius: 5px;
                font-family: 'Roboto', sans-serif; 
                font-weight: bold; 
                transition: 0.1s; 
                cursor: pointer;
            }
            .normal_button:hover {
                background: white; 
                color: #0081d6;
            }
        </style>
        <!-- Message Modal -->
        <div id="modalMessage" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px); fill: white;">
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
        <!-- Welcome Modal -->
        <div id="modalWelcome" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;min-width:300px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px); fill: white;">
                <div style="display: flex; justify-content: right; align-items: center;">
                    <button class="cross_button" onclick="closeModalWelcome()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                </div>
                <svg width="60" height="60" version="1.1" viewBox="0 0 63.733 62.145" xmlns="http://www.w3.org/2000/svg"><g transform="translate(-81.24 -80.998)"><path d="m113.11 80.998c-17.57 0-31.865 13.908-31.865 31.072 0 17.164 14.295 31.073 31.865 31.073 17.57 0 31.867-13.909 31.867-31.073 0-17.164-14.297-31.072-31.867-31.072zm-2.3874 19.855c-.039.33031.0766.76435-.0563 1.0315-.98186.38617-2.1894.44525-2.9383 1.2625-.2364.3544-.0516.81899-.10749 1.2144.0107 2.4982.13695 4.9932.24495 7.4884.12331 3.5882.17594 7.178.20981 10.768-.60825.023-1.2165.046-1.8247.0692-4.4457-4.2995-8.8374-8.6527-13.336-12.901-1.4339-1.3022-2.8663-2.6143-4.3811-3.8256-.14656.13236-.03314.43313-.07493.61856.07043 3.4996.09772 7.0012.23978 10.499.10113 1.0877.04977 2.2064.3421 3.2675.16838.47344.80108.36697 1.2004.45165.74103.0876 1.5062.10884 2.2293.26406.04302.4316.0618.86503.09147 1.2976-2.0639-.0782-4.1221-.33399-6.1908-.22944-.33435.0206-.67606.0213-1.0154.032-.23336-.60848-.44615-1.227-.63769-1.8542.73705.0837 1.494.0424 2.2252-.0667.56044-.1814.52508-.877.56689-1.3415.17372-4.0548.09685-8.1153.07235-12.172-.0537-.97645.05876-1.9942-.26665-2.9347-.34138-.7758-1.1495-1.2574-1.9249-1.5555-.027-.006-.05488-.008-.08268-.007-.01909.005-.03691.0104-.05374.0176.15878-.42698.3278-.84928.50643-1.2666.28743.004.58161-.005.85938.0124 1.1135.0398 2.2326-.0652 3.3409-.003.65025.33611 1.1254.9244 1.6702 1.3963 4.9225 4.6774 9.7451 9.4541 14.692 14.107.17956.11913.0737-.27016.10386-.34726-.036-2.7126-.2615-5.4179-.33951-8.1292-.0919-1.5794-.18919-3.1624-.38706-4.7325-.0737-.56472-.78186-.6695-1.2413-.7984-.63271-.13062-1.2684-.24821-1.8826-.44338.0717-.28619-.14626-.82858.11989-.94258 2.6701-.14675 5.3513-.16236 8.0259-.24753zm10.566.0284c1.8778.19061 3.7635.3913 5.656.30851 4.3313-.14099 8.6657-.27558 12.999-.21549.17236-.003.35311.0221.53175.0388.59477 1.3941 1.0803 2.8437 1.4464 4.3382-.008.0295-.007.0623.009.094-.007.19589.16414.58307-.16019.59842-.32821.0169-.66274-.0121-.99477-.015-.49722-1.246-1.1848-2.4316-2.0541-3.4664-.25912-.28077-.713-.14216-1.0516-.23255-1.8796-.21329-3.7717-.1924-5.6606-.1912-.79688.0127-1.5979-.0112-2.3921.047-.088.78213-.0278 1.6002-.0517 2.3952.008 1.858-.0208 3.7187.0491 5.5748.47381.11781 1.0082.0434 1.5028.0729 1.5533-.0196 3.1281.0115 4.6571-.29042.75483-.16093.95351-.98933 1.1193-1.6138.13338-.51401.24268-1.0331.36225-1.5503.35613-.0104.71203-.0211 1.0682-.0315.13437 1.7023.0368 3.4148.077 5.1211.005 1.0247.0325 2.0487.0713 3.0727-.36825-.0104-.73659-.0211-1.1048-.0315-.25704-.93968-.37408-1.9337-.81028-2.8164-.36074-.62367-1.1993-.56202-1.8299-.61443-1.7044-.0335-3.4124-.023-5.1134.0971-.0647 1.1144-.008 2.2752-.0258 3.4075.0158 1.4184-.0231 2.842.0631 4.2571.18714.57094.88498.67121 1.4113.71261 2.0455.2108 4.1192.17359 6.1619-.0517.94686-.0829 1.8936-.37688 2.6071-1.0108 1.0262-.85248 1.7192-2.0258 2.1668-3.2525.15659-.0371.34515-.0176.51263-.0408-.2881 2.3051-.85538 4.5265-1.6681 6.6306-.69087-.0153-1.3867.002-2.0764-.0393-4.9589-.19434-9.9254-.20312-14.886-.0837-.90463.0377-1.8115.0428-2.714.11627.0463-.34479-.0865-.75055.0713-1.064 1.1841-.23069 2.4361-.23447 3.5688-.67386.31964-.22382.19519-.69509.25476-1.0232.0596-4.1852-.0244-8.3713-.0248-12.557.0133-1.3628-.0748-2.7293-.20981-4.0824-.22417-.35773-.75546-.3038-1.1193-.42013-.82962-.16376-1.6808-.18642-2.5156-.29508.0195-.40801.0376-.81569.0971-1.2196zm-5.5986 17.004c.0752-.004.15191-.001.22996.007 1.0205-.0593 2.0772.53886 2.4303 1.4795.15644.67302.1289 1.436-.21962 2.049-.6983 1.0898-2.3165 1.4216-3.4148.71417-.66714-.40399-1.1038-1.1588-1.0036-1.928-.0712-.60606.16768-1.2113.63097-1.6242.36556-.36978.82039-.67148 1.3467-.69712zm5.5893 7.3768c2.4875.004 5.0303.65005 7.3334 1.5234 3.2148 1.2191 5.9138 2.8656 7.2512 3.7491-5.4285 6.3314-13.599 10.361-22.754 10.361-6.3794 0-12.28-1.9564-17.109-5.285 1.0464.10994 2.1018.0348 3.1393-.14521 3.6687-.46494 8.6101-2.5547 15.136-7.8708 2.0829-1.6968 4.5152-2.3359 7.0027-2.3322z" fill-rule="evenodd" stop-color="#000000" stroke-linecap="round" stroke-linejoin="round" style="paint-order:markers fill stroke"/></g></svg>
                <br><br>
                <span>Welcome, </span><a style="font-family: 'Roboto', sans-serif; font-weight: bold;"><?php echo XSS($name_login ?? '')?>!</a>
                <br><br>
                <span>Current Build: 109.06032026</span>
                <br><br>
                <button class="normal_button" onclick="closeModalWelcome()">Ok</button>
            </div>
        </div>
        <!-- Menu Modal -->
         <div id="modalmenu" style="display:none; position:fixed; z-index:102; left:0; top:0; width:100vw; height:100vh; align-items:center; justify-content:center;">
            <div style="background-color: rgba(0, 0, 0, 0.5);border: 1px solid gray;color: white;padding:15px 15px;border-radius:10px;width:500px;height:500px;text-align:center;position:relative;font-family: 'Roboto', sans-serif; -webkit-backdrop-filter: blur(7px); backdrop-filter: blur(7px); fill: white;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-weight: bold; font-size: 30px; cursor: default;">NET.E Menu</span>
                    </div>
                    <div>
                        <button class="cross_button" onclick="closeModalMenu()"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"/></svg></button>
                    </div>
                </div>
                <br>
                <div class="List_Container_Menu">
                    <a class="MenuList Main" href="index.php">
                        <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M20 3H4c-1.103 0-2 .897-2 2v11c0 1.103.897 2 2 2h7v2H8v2h8v-2h-3v-2h7c1.103 0 2-.897 2-2V5c0-1.103-.897-2-2-2zM4 14V5h16l.002 9H4z"/></svg>
                        <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Blank Screen</span>
                    </a>
                    <a class="MenuList Home" href="index.php?req=home">
                        <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M7.5 6.5C7.5 8.981 9.519 11 12 11s4.5-2.019 4.5-4.5S14.481 2 12 2 7.5 4.019 7.5 6.5zM20 21h1v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h17z"/></svg>
                        <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">User Page</span>
                    </a>
                    <a class="MenuList Utility" href="index.php?req=utility">
                        <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M4 11h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1zm10 0h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1zM4 21h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1zm10 0h6a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1z"/></svg>
                        <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Utility</span>
                    </a>
                    <?php
                    if (!isset($_SESSION['AUTHENTICATE_ADMIN'])) {
                        ?>
                        <a class="MenuList Doc" href="index.php?req=doc">
                            <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M20 5h-9.586L8.707 3.293A.997.997 0 0 0 8 3H4c-1.103 0-2 .897-2 2v14c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V7c0-1.103-.897-2-2-2z"/></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">My Files</span>
                        </a>
                        <a class="MenuList Note" href="index.php?req=note">
                            <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8l8-8V5a2 2 0 0 0-2-2zm-7 16v-7h7l-7 7z"/></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Memory Notes</span>
                        </a>
                        <a class="MenuList Preference" href="index.php?req=preference">
                            <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="m2.344 15.271 2 3.46a1 1 0 0 0 1.366.365l1.396-.806c.58.457 1.221.832 1.895 1.112V21a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-1.598a8.094 8.094 0 0 0 1.895-1.112l1.396.806c.477.275 1.091.11 1.366-.365l2-3.46a1.004 1.004 0 0 0-.365-1.366l-1.372-.793a7.683 7.683 0 0 0-.002-2.224l1.372-.793c.476-.275.641-.89.365-1.366l-2-3.46a1 1 0 0 0-1.366-.365l-1.396.806A8.034 8.034 0 0 0 15 4.598V3a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v1.598A8.094 8.094 0 0 0 7.105 5.71L5.71 4.904a.999.999 0 0 0-1.366.365l-2 3.46a1.004 1.004 0 0 0 .365 1.366l1.372.793a7.683 7.683 0 0 0 0 2.224l-1.372.793c-.476.275-.641.89-.365 1.366zM12 8c2.206 0 4 1.794 4 4s-1.794 4-4 4-4-1.794-4-4 1.794-4 4-4z"/></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">User Preference</span>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a class="MenuList Track" href="index.php?req=track">
                            <svg style="margin-left: 10px;" width="30" height="30" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g fill="#fff" stroke="none"><path d="m12 3c-7.7619 0-11.895 8.5527-11.895 8.5527a1.0001 1.0001 0 000 .89453s4.1326 8.5527 11.895 8.5527c7.7619 0 11.895-8.5527 11.895-8.5527a1.0001 1.0001 0 000-.89453s-4.1326-8.5527-11.895-8.5527zm0 2c5.8634 0 9.3477 6.2096 9.7793 7-.43163.79042-3.9159 7-9.7793 7-5.8634 0-9.3477-6.2096-9.7793-7 .43163-.79042 3.9159-7 9.7793-7z" stop-color="#000000"/><path d="m12 8c-2.1973 0-4 1.8027-4 4 0 2.1973 1.8027 4 4 4 2.1973 0 4-1.8027 4-4 0-2.1973-1.8027-4-4-4zm0 2c1.1164 0 2 .88359 2 2s-.88359 2-2 2-2-.88359-2-2 .88359-2 2-2z" stop-color="#000000"/><path d="m11.952 9.6238c-.96218-.00277-1.8652.65296-2.1905 1.5533-.18103.4852-.18351 1.037-.040709 1.5322.31175.97881 1.2967 1.7382 2.342 1.6601 1.0372.0097 1.9939-.77334 2.2406-1.7717.32032-1.109-.32724-2.3901-1.404-2.8012-.29983-.12092-.62436-.17814-.94737-.17271z"/></g></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Tracking Report Case</span>
                        </a>
                        <a class="MenuList Key" href="index.php?req=user">
                            <svg style="margin-left: 10px;" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24"><path d="M3.433 17.325 3.079 19.8a1 1 0 0 0 1.131 1.131l2.475-.354C7.06 20.524 8 18 8 18s.472.405.665.466c.412.13.813-.274.948-.684L10 16.01s.577.292.786.335c.266.055.524-.109.707-.293a.988.988 0 0 0 .241-.391L12 14.01s.675.187.906.214c.263.03.519-.104.707-.293l1.138-1.137a5.502 5.502 0 0 0 5.581-1.338 5.507 5.507 0 0 0 0-7.778 5.507 5.507 0 0 0-7.778 0 5.5 5.5 0 0 0-1.338 5.581l-7.501 7.5a.994.994 0 0 0-.282.566zM18.504 5.506a2.919 2.919 0 0 1 0 4.122l-4.122-4.122a2.919 2.919 0 0 1 4.122 0z"/></svg>
                            <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">User Manager</span>
                        </a>
                        <?php
                    }
                    ?>
                    <a class="MenuList Help" href="index.php?req=help">
                        <svg style="margin-left: 10px;" width="30" height="30" version="1.1" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="m6 2a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2v-12l-6-6h-8zm7 2 5 5h-4-1v-5zm-1.5371 7.0547c.74653 0 1.3427.20312 1.7871.60937.44445.40625.66602.89627.66602 1.4727 0 .33333-.078125.64062-.23438.92188-.15625.28125-.46115.62456-.91602 1.0273-.30556.27083-.50586.46918-.59961.59766-.093751.12847-.16189.27669-.20703.44336s-.07118.4375-.078125.8125h-.90234c-.003473-.125-.003907-.21875-.003907-.28125 0-.36806.052083-.68576.15625-.95312.07639-.20139.199-.40451.36914-.60938.125-.14931.34896-.36567.67188-.65039.32639-.2882.53754-.51736.63477-.6875.097222-.17014.14648-.3572.14648-.55859 0-.36458-.14301-.68272-.42773-.95703-.28472-.27778-.63368-.41797-1.0469-.41797-.39931 0-.73264.125-1 .375s-.44206.64062-.52539 1.1719l-.96289-.11328c.086806-.71181.34245-1.2582.76953-1.6367.43056-.37847.99826-.56641 1.7031-.56641zm-.54102 6.6973h1.0684v1.0684h-1.0684v-1.0684z"/></svg>
                        <span style="margin-left: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Help</span>
                    </a>
                    <form id="logoutform" action="./Element/User/userverifyact.php" method="post">
                        <input type="hidden" name="reqact" value="logout"/>
                        <input class="MenuList Exit" style="width: 100%; background: #ff0000; font-family: 'Roboto', sans-serif; font-size: 15px; font-weight: bold;" type="submit" value="Log Out"></input>
                    </form>
                </div>
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
        // Welcome Modal
        function showModalWelcome() {
            document.getElementById('modalWelcome').style.display = 'flex';
        }
        function closeModalWelcome() {
            document.getElementById('modalWelcome').style.display = 'none';
        }
        // History Modal 
        function showModalMenu() {
            document.getElementById('modalmenu').style.display = 'flex';
        }
        function closeModalMenu() {
            document.getElementById('modalmenu').style.display = 'none';
        }
        </script>
        <!-- Return Respond Pop Up -->
        <?php
        if (isset(($_SESSION['MODAL_ERROR_MESSAGE']))) {
            ?>
            <script>
                showModalMessage("<?php echo XSS($_SESSION['MODAL_ERROR_MESSAGE'][1]);?>");
            </script>
            <?php
            unset($_SESSION['MODAL_ERROR_MESSAGE']);
        }
        if (isset(($_SESSION['WELCOME_POPUP']))) {
            ?>
            <script>
                showModalWelcome();
            </script>
            <?php
            unset($_SESSION['WELCOME_POPUP']);
        }
        ?>
        <div class="Screen">
            <?php require "./Element/center.php"?>
        </div>
    </body>
</html>
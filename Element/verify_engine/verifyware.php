<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    session_start();
    set_time_limit(0);
    ignore_user_abort(true);
    // define!
    define('STATUS_OK', 0); // Defined ok: 0 = OK
    define('STATUS_TAMPERED', 1); // Defined not ok: 1 = Tampered
    define('LOCK_EXPIRY', 300); // 5 minutes expire for lock.
    require '../../Element/Database/filecls.php';
    require '../../Element/Database/verifycls.php';
    // Check if the legit of id file. IT SHOULD HAVE IF YOU GO BY SYSTEM URL!
    if (isset($_GET['fileid'])) {
        $getidfile = $_GET['fileid'];
    } else {
        die();
    }
    // Get SESSION user!
    if (isset($_SESSION['AUTHENTICATE_USER'])) {
    } else {
        die();
    }
    $obj=new index();
    $lock_handler=new verify_logic();
    $get_info = $obj->getinfo($getidfile);

    if(isset($_GET['secure'])) {
        $requestaction = $_GET['secure'];
        switch ($requestaction) {
            case 'hash_check':
                header('Content-Type: application/json');

                // Check if this shit don't exist
                $actual_path = "../." . $get_info->file_link;
                if (!file_exists($actual_path)) {
                    $obj->updatestatus(STATUS_TAMPERED, $getidfile);
                    echo json_encode([
                        'title_status' => "Error",
                        'status' => 1,
                        'icon_url' => "../../Resource/alert.png"
                    ]);
                    die();
                }

                $lock_exist = false;
                $lock_status = $lock_handler->get_verifylock($getidfile);

                if ($lock_status) {
                    if (strtotime($lock_status->start_time) > (time() - LOCK_EXPIRY)) {
                        $lock_exist = true;
                    } else {
                        $lock_handler->del_lock($getidfile);
                    }
                }

                $checking_icon = "../../Resource/shield_eye.png";

                if ($lock_exist) {
                    echo json_encode([
                        'title_status' => "Scanning in progress...",
                        'status' => 'busy', 
                        'icon_url' => $checking_icon 
                    ]);
                    die();
                }

                try {
                    $lock_handler->add_lock($getidfile);
                } catch (PDOException $e) {
                    echo json_encode([
                        'title_status' => "Scanning in progress...",
                        'status' => 'busy', 
                        'icon_url' => $checking_icon
                    ]);
                    die();
                }

                //Got ya hash!
                $get_pyshical_hash = hash_file('sha256', $actual_path);
                $title_status_out = "Placeholder";
                
                if ($get_pyshical_hash !== false && $get_pyshical_hash === $get_info->sha256_hash) {
                    $define_status = STATUS_OK;
                    $outputpath = "../../Resource/green_shield.png";
                    $title_status_out = "Tamper status is ok!";
                } else {
                    $define_status = STATUS_TAMPERED;
                    $outputpath = "../../Resource/red_shield.png";
                    $title_status_out = "Tamper detected in your file! You shouldn't interact with the file.";
                }

                $obj->updatestatus($define_status, $getidfile);

                $lock_handler->del_lock($getidfile);
                
                echo json_encode([
                    'title_status' => $title_status_out,
                    'status' => $define_status,
                    'icon_url' => $outputpath 
                ]);

                die();
        }
    }
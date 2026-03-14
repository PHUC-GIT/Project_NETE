<?php
    ini_set('session.cookie_httponly', '1'); 
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', '1'); // Change it to '0' if you're run it on http localhost or without secured HTTPS but only if something unexpect happened. 
    session_start();
    header("X-Frame-Options: DENY");
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("X-Content-Type-Options: nosniff");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $crsf_get = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
        if ($crsf_get === $_SESSION['CSRF_TOKEN']) {
            // CSRF Matched and reload anew.
            unset($_SESSION['CSRF_TOKEN']);
            // Recheck if the session already empty to insert new token.
            $regen_csrf = bin2hex(random_bytes(64 / 2));
            $_SESSION['CSRF_TOKEN'] = $regen_csrf;

            // Head
            require '../../Element/Database/filecls.php';
            require '../../Element/Database/usercls.php';
            // Get SESSION user, And verify one also.
            if (isset($_SESSION['AUTHENTICATE_USER'])) {
                $getuserinfo = new user();
                $resultget = $getuserinfo->user_Name($_SESSION['AUTHENTICATE_USER']);
                $session_user = $resultget->username ?? null;
                if (!$session_user) {
                    // Destroy the session if user don't found
                    if (session_status() === PHP_SESSION_ACTIVE) {
                      session_unset();
                      session_destroy();
                    }
                    session_start();
                    session_regenerate_id(true);
                    if (!headers_sent()) {
                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "System unable to processe your info.");
                        header('location:../../login.php');
                    }
                    die;
                }
            }
            // Get SESSION Storage user!
            if (isset($_SESSION['USER_CURRENT_STORAGE'])) {
                $session_storage_left = $_SESSION['USER_CURRENT_STORAGE'];
            } else {
                $request_storage = new index();
                $total_size = $request_storage->get_totalsize();
                $get_user_storage = $request_storage->get_userlocated();
                $maximum_allowed = $get_user_storage->storage_allocated;
                // Load this so system can see how much storage left.
                $session_storage_left = $maximum_allowed - $total_size;
                unset($request_storage);
            }

            if(isset($_POST['reqact'])) {
                $requestaction = $_POST['reqact'];
                switch ($requestaction) {
                    case 'addfile':
                        if ($_FILES['file_value']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file_value']['tmp_name'])) {
                            // This open finfo
                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                            $getuserdir = $session_user . $_SESSION['SALT_VALUE'];
                            $hashfolder = hash('sha256', $getuserdir);
                            $targetdir = "../../User_Data/" . $hashfolder . "/";
                            $targetfile = $targetdir . basename($_FILES["file_value"]["name"]);
                            $filetype = strtolower(pathinfo($targetfile,PATHINFO_EXTENSION));
                            $get_mime = finfo_file($finfo, $_FILES['file_value']['tmp_name']);
                            $get_file_hash256 = hash_file('sha256', $_FILES['file_value']['tmp_name']);
                            // Predefined mime that use for high mistaked mime
                            $pre_mime = array('docx', 'xlsx', 'pptx', 'apk', 'xapk');
                        
                            $ban_format = array('exe', 'msi', 'bat', 'cmd', 'com', 'scr', 'js', 'jse', 'vbs', 'vbe', 'wsf', 'wsh', 'ps1', 'ps2', 'psm1', 'psc1',
                            'jar', 'gadget', 'msu', 'msp', 'reg', 'sh', 'bash', 'csh', 'ksh', 'zsh', 'php', 'php3', 'php4', 'php5', 'phtml',
                            'pl', 'py', 'rb', 'dll', 'sys', 'drv', 'asp', 'aspx', 'cer', 'csr', 'jsp', 'jspx', 'war', 'ear', 'cpl', 'adp',
                            'ade', 'adp', 'bas', 'chm', 'hta', 'inf', 'ins', 'isp', 'mde', 'msc', 'msh', 'msh1', 'msh2', 'mshxml', 'msh1xml',
                            'msh2xml', 'psc1', 'psc2', 'sct', 'shb', 'shs', 'url', 'vb', 'vbe', 'vbs', 'ws', 'wsc', 'wsf', 'wsh');
                            $danger_mimes = array(
                                'application/x-msdownload',      // .exe, .dll
                                'application/x-msdos-program',   // .exe
                                // 'application/octet-stream',      // unknow file type.
                                'application/x-ms-installer',    // .msi
                                'application/x-msi',             // .msi
                                'application/x-ms-shortcut',     // .lnk
                                'application/x-bat',             // .bat
                                'application/x-cmd',             // .cmd
                                'application/x-sh',              // .sh
                                'application/x-shellscript',     // .sh
                                'application/x-php',             // .php
                                'application/x-perl',            // .pl
                                'application/x-python',          // .py
                                'application/x-ruby',            // .rb
                                'application/x-javascript',      // .js
                                'application/javascript',        // .js
                                'application/x-vbs',             // .vbs
                                'application/x-powershell',      // .ps1
                                'application/x-java-applet',     // .jar
                                'application/java-archive',      // .jar, .war, .ear
                                'application/x-msdos-windows',   // .com
                                'application/x-scr',             // .scr
                                'application/x-csh',             // .csh
                                'application/x-ksh',             // .ksh
                                'application/x-hta',             // .hta
                                'application/x-ms-shortcut',     // .lnk
                                'application/x-ms-wmd',          // .wmd
                                'application/x-ms-wmz',          // .wmz
                                'application/x-ms-xbap',         // .xbap
                                'application/x-ms-application',  // .application
                                'application/x-ms-clip',         // .clp
                                'application/x-mscardfile',      // .crd
                                'application/x-msmetafile',      // .wmf, .emf
                                'application/x-mswrite',         // .wri
                                'application/x-msaccess',        // .mdb
                                'application/x-msbinder',        // .obd
                                'application/x-mscalendar',      // .cal
                                'application/x-msmediaview',     // .mvb
                                'application/x-msmoney',         // .mny
                                'application/x-msexcel',         // .xls
                                'application/x-mspublisher',     // .pub
                                'application/x-mspowerpoint',    // .ppt
                                'application/x-msword',          // .doc
                                'application/x-msworks',         // .wps
                                'application/x-mswrite',         // .wri
                                'application/x-ms-xbap',         // .xbap
                                'application/x-ms-xpsdocument',  // .xps
                                'application/x-mscardfile',      // .crd
                                'application/x-msclip',          // .clp
                                // Scripting
                                'text/x-python',                 // .py
                                'text/x-perl',                   // .pl
                                'text/x-php',                    // .php
                                'text/x-shellscript',            // .sh
                                'text/x-vbs',                    // .vbs
                                'text/x-msdos-batch',            // .bat
                                'text/javascript',               // .js
                                'text/vbscript',                 // .vbs
                                'text/x-script.phyton',          // .py
                                'text/x-script.perl',            // .pl
                                'text/x-script.php',             // .php
                                'text/x-script.sh',              // .sh
                                'text/x-script.bash',            // .bash
                                'text/x-script.csh',             // .csh
                                'text/x-script.ksh',             // .ksh
                                'text/x-script.pl',              // .pl
                                'text/x-script.py',              // .py
                                'text/x-script.rb',              // .rb
                                'text/x-script.tcl',             // .tcl
                                'text/x-script.tcsh',            // .tcsh
                                'text/x-script.zsh',             // .zsh
                                );
                            $getfilename = $_FILES['file_value']['name'];
                            $getfilesize = $_FILES['file_value']['size'];
                            $oktoupload = 1;
                            // Physical Folder Manager
                            if (!is_dir($targetdir)) {
                                if (!mkdir($targetdir, 0755, true) && !is_dir($targetdir)) {
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to create user data folder.");
                                    header('location:../../index.php?req=doc');
                                    die;
                                }
                            }
                            
                            // Create .htaccess in folder if it don't exist.
                            if (!file_exists("../../User_Data/". $hashfolder . "/.htaccess")) {
                                // Add .htaccess to newly created folder.
                                $tothefolder = "../../User_Data/". $hashfolder . "/.htaccess";
                                $htaccesscontent = <<<EOT
                                    <IfModule mod_authz_core.c>
                                        Require all denied
                                    </IfModule>
                                                        
                                    <IfModule !mod_authz_core.c>
                                        Order Deny,Allow
                                        Deny from all
                                    </IfModule>
                                EOT;
                                if (file_put_contents($tothefolder, $htaccesscontent) !== false) {
                                } else {
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to secured container!");
                                    echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                    $oktoupload = 0;
                                    die;
                                }
                            }
                            if ($getfilesize > 1 * 1024 * 1024 * 1024) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File ejected because it size over allowed upload size.");
                                echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                $oktoupload = 0;
                            }
                            if (in_array($filetype, $ban_format)) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Prevent upload execution file. Please archive it if you want included execution file.");
                                // Change if you have new directory
                                echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                $oktoupload = 0;
                            }
                            if (in_array($get_mime, $danger_mimes)) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Prevent upload file. Dangerous file type detected!. Mimetype: " . "{$get_mime}" . ", if this is safe then including it in archive!");
                                echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                $oktoupload = 0;
                            }
                            if ($getfilesize > $session_storage_left) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Uploaded file is larger than your current storage space left! Please clean up then reupload again!");
                                echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                $oktoupload = 0;
                            }
                            if ($oktoupload == 1) {
                                $requestupload = new index();
                                // Check for file with same content.
                                $Request_Hash256 = $requestupload->checkfilehash($get_file_hash256);
                                if ($Request_Hash256) {
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "You have this file already!");
                                    echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                    die;
                                }

                                // Check if file is more than 100 for each user.
                                $Get_FIle_Num = $requestupload->countfile();
                                if ($Get_FIle_Num >= 100) {
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Maximum file capacity reached! Please clean up some space.");
                                    echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                    die;
                                }

                                $randomvaluefile = bin2hex(random_bytes(64 / 2));
                                $savetodir = $targetdir.$randomvaluefile;
                                clearstatcache(true, $savetodir);
                                // Revalid if the path already exist in the physical, this chance is increidible low! You win lottery if hit there.
                                if(file_exists($savetodir)) {
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "There is something wrong with uploaded file! Please try again!");
                                    echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                    die;
                                }
                                
                                // Redefined mime type for docx, xlsx, pptx if it mistake as "application/zip".
                                if (in_array($filetype, $pre_mime)) {
                                    switch ($filetype) {
                                        case 'docx':
                                            $final_mime_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                                            break;
                                        case 'xlsx':
                                            $final_mime_type = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                                            break;
                                        case 'pptx':
                                            $final_mime_type = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
                                            break;
                                        case 'apk':
                                        case 'xapk':
                                            $final_mime_type = 'application/vnd.android.package-archive';
                                            break;
                                    }
                                    $get_mime = $final_mime_type;
                                }
                                $parent_folder_id = $_SESSION['Current_Folder'];
                                // Check if files is in folder.
                                if ($parent_folder_id) {
                                    // Dir Logic
                                } else {
                                    $parent_folder_id = "NULL";
                                }

                                if (move_uploaded_file($_FILES['file_value']['tmp_name'], $savetodir)) {
                                    $file_name = isset($_POST['file_name']) ? $_POST['file_name'] : '';
                                    $is_share = isset($_POST['is_share']) ? $_POST['is_share'] : 0;
                                    if ($file_name == ""){
                                        $file_name = $getfilename;
                                    }
                                    $file_link = "./User_Data/".$hashfolder."/".$randomvaluefile;
                                    $file_size = $getfilesize;
                                    $uploader = $session_user;


                                    $result = $requestupload->addfile($file_name, $file_link, $file_size, $filetype, $get_file_hash256, $parent_folder_id, $get_mime , $is_share);
                                    if ($result) {
                                        header('location:../../index.php?req=doc');
                                        die;
                                    } else {
                                        if (file_exists($savetodir)) {
                                            unlink($savetodir);
                                        }
                                        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Insert file Error! [Database side]");
                                        echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                        die;
                                    }
                                } else {
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Upload file can not be saved.");
                                    echo "<script>window.location.href='../../index.php?req=doc';</script>";
                                    die;
                                }
                            }
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "There is something wrong with uploaded file! Please try again!");
                            echo "<script>window.location.href='../../index.php?req=doc';</script>";
                            break; 
                        }
                        // Some stupid logic that I don't remember why it here.
                        if ($oktoupload != 1) {
                            die;
                        }
                        die;
                    
                    case 'addfolder':
                        $parent_folder_id = $_SESSION['Current_Folder'];
                        // Check if files is in folder.
                        if ($parent_folder_id) {
                            // Dir Logic
                        } else {
                            $parent_folder_id = "NULL";
                        }
                        $is_share = 0;
                        $file_name = !empty($_POST['folder_name']) ? $_POST['folder_name'] : "Untitled";
                        $filetype = "folder";
                        $get_mime = "nete/folder"; // <<< This one is unique folder identity. You need use replace option when changing.
                        $file_link = "no";
                        $file_size = 0;
                        $uploader = $session_user;
                        $requestfolder = new index();
                        // Check if user have more than 100 folder.
                        $Get_Folder_Num = $requestfolder->countfolder();
                        if ($Get_Folder_Num >= 100) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Maximum folder capacity reached! Please management your space.");
                            echo "<script>window.location.href='../../index.php?req=doc';</script>";
                            die;
                        }

                        $result = $requestfolder->addfile($file_name, $file_link, $file_size, $filetype, $get_file_hash256="NULL", $parent_folder_id, $get_mime , $is_share);
                        if ($result) {
                            header('location:../../index.php?req=doc');
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "There is problem when creating folder!");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        header('location:../../index.php?req=doc');
                        die;
                    
                    case 'createfile':
                        // Prepare director
                        $getuserdir = $session_user . $_SESSION['SALT_VALUE'];
                        $hashfolder = hash('sha256', $getuserdir);
                        $targetdir = "../../User_Data/" . $hashfolder . "/";
                        $parent_folder_id = $_SESSION['Current_Folder'];
                        // Check if files is in folder.
                        if ($parent_folder_id) {
                            // Dir Logic
                        } else {
                            $parent_folder_id = "NULL";
                        }
                        $allow_mime = array('text/plain');
                        $cover_mime_to_type = array(
                            'text/plain' => 'txt',
                        );
                        $is_share = 0;
                        $file_name = !empty($_POST['file_name']) ? $_POST['file_name'] : "Untitled";
                        $getfilesize = 0;
                        $get_mime = !empty($_POST['file_mime_type']) ? $_POST['file_mime_type'] : "text/plain";
                        $filetype = $cover_mime_to_type[$get_mime];
                        $get_file_hash256 = "NULL";
                        $requestcreation = new index();
                        $Get_FIle_Num = $requestcreation->countfile();
                        if ($Get_FIle_Num >= 100) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Maximum file capacity reached! Please clean up some space.");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        // Check if incoming mime is correct.
                        if (!in_array($get_mime, $allow_mime)) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File Type Invalid.");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        // Create file operation
                        // Physical Folder Manager
                        if (!is_dir($targetdir)) {
                            if (!mkdir($targetdir, 0755, true) && !is_dir($targetdir)) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to create user data folder.");
                                header('location:../../index.php?req=doc');
                                die;
                            }
                        }
                        // Create .htaccess in folder if it don't exist.
                        if (!file_exists("../../User_Data/". $hashfolder . "/.htaccess")) {
                            // Add .htaccess to newly created folder.
                            $tothefolder = "../../User_Data/". $hashfolder . "/.htaccess";
                            $htaccesscontent = <<<EOT
                                <IfModule mod_authz_core.c>
                                    Require all denied
                                </IfModule>
                                                    
                                <IfModule !mod_authz_core.c>
                                    Order Deny,Allow
                                    Deny from all
                                </IfModule>
                            EOT;
                            if (file_put_contents($tothefolder, $htaccesscontent) !== false) {
                            } else {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to secured container!");
                                header('location:../../index.php?req=doc');
                                die;
                            }
                        }
                        // Create empty file.
                        $randomvaluefile = bin2hex(random_bytes(64 / 2));
                        $savetodir = $targetdir.$randomvaluefile;
                        $target_permission = 0600; // This one only use for Linux. Windows will ignore. But may it cause fatal error?
                        $filecontent = ''; // This file content away be empty!
                        if (file_put_contents($savetodir, $filecontent) === false) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to created new file");
                            header('location:../../index.php?req=doc');
                            die;
                        }

                        if (!chmod($savetodir, $target_permission)) {
                            error_log("NETE_REPORT -> failed to secured file: $savetodir");
                        }
                        $getfilesize = filesize($savetodir);
                        $get_file_hash256 = hash_file('sha256', $savetodir);

                        // Save thing to disk!
                        $file_link = "./User_Data/".$hashfolder."/".$randomvaluefile;
                        $result = $requestcreation->addfile($file_name, $file_link, $getfilesize, $filetype, $get_file_hash256, $parent_folder_id, $get_mime, $is_share);
                        if ($result) {
                            header('location:../../index.php?req=doc');
                            die;
                        } else {
                            unlink($savetodir);
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "There is problem when creating file!");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        header('location:../../index.php?req=doc');
                        die;
                    
                    case 'deletefolder':
                        $delete_file = new index();
                        $getidfile = isset($_POST['idfile']) ? $_POST['idfile'] : '';
                        // Call the combined recursive function
                        $result = $delete_file->deletefolder($getidfile);
                        if ($result) {
                            // All files and records are gone.
                            header('location:../../index.php?req=doc');
                        } else {
                            // An error occurred during deletion (physical or database).
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to delete the folder. Some files may have been left behind.");
                            header('location:../../index.php?req=doc');
                        }
                        die;
                    
                    case 'deletefile':
                        $delete_file = new index();
                        $getidfile = isset($_POST['idfile']) ? $_POST['idfile'] : '';
                        $file_info = $delete_file->getinfo_edit($getidfile);
                        if ($file_info) {
                            $delete_dir = "../." . $file_info->file_link;
                            if (file_exists($delete_dir) && !unlink($delete_dir)) {
                                // Deletion failed. Do NOT touch the database.
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Can't delete the file!");
                            } else {
                                // Physical file is deleted or already gone. Proceed to delete the record.
                                $result = $delete_file->deletefile($getidfile);
                                if (!$result) {
                                    // Database record deletion failed.
                                    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to delete record!");
                                }
                            }
                        } else {
                            // File ID not found or not owned by the user.
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File not found or access denied.");
                        }
                        header('location:../../index.php?req=doc');
                        die;
                    
                    case 'updatefile':
                        $getidfile = isset($_POST['id_get']) ? $_POST['id_get'] : '';
                        $is_share =  isset($_POST['is_share']) ? $_POST['is_share'] : 0;
                        $moving_file = isset($_POST['moving_file']) ? $_POST['moving_file'] : 'NULL';
                        $file_name = !empty($_POST['file_name']) ? $_POST['file_name'] : bin2hex(random_bytes(10 / 2));
                        $update_file = new index();
                        // Skip if moving is have text "NULL".
                        if ($moving_file !== "NULL"){
                            if (!$update_file->checkiffolderexist($moving_file)) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "The target folder is invalid.");
                                header('location:../../index.php?req=doc');
                                die;
                            }
                        }
                        $result = $update_file->updatefile($file_name, $is_share, $moving_file, $getidfile);
                        if ($result) {
                            header('location:../../index.php?req=doc');
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to update file!");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        header('location:../../index.php?req=doc');
                        die;
                    
                    case 'updatefolder':
                        $getidfile = isset($_POST['id_get']) ? $_POST['id_get'] : '';
                        $moving_file = isset($_POST['moving_file']) ? $_POST['moving_file'] : 'NULL';
                        $folder_name = !empty($_POST['folder_name']) ? $_POST['folder_name'] : bin2hex(random_bytes(10 / 2));
                        $update_file = new index();
                        // Skip if moving is have text "NULL".
                        if ($moving_file !== "NULL"){
                            if (!$update_file->checkiffolderexist($moving_file)) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "The target folder is invalid.");
                                header('location:../../index.php?req=doc');
                                die;
                            }

                            if ($update_file->CheckMoveConflict($getidfile, $moving_file)) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "The folder can not moving into its children.");
                                header('location:../../index.php?req=doc');
                                die;
                            }
                        }
                        $result = $update_file->updatefolder($folder_name, $moving_file, $getidfile);
                        if ($result) {
                            header('location:../../index.php?req=doc');
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to update folder!");
                            header('location:../../index.php?req=doc');
                            break;
                        }
                        header('location:../../index.php?req=doc');
                        die;
                    
                    case 'update_editfile':
                        $getidfile = isset($_POST['id_get']) ? $_POST['id_get'] : '';
                        $getcontent = isset($_POST['file_content']) ? $_POST['file_content'] : '';
                        $allow_mime = array('text/plain');
                        $update_content = new index();
                        $get_file_info = $update_content->getinfo_edit($getidfile);
                        $actual_path = "../." . $get_file_info->file_link;
                        $get_mime = $get_file_info->mime_type;
                        $get_size = $get_file_info->size;
                        if (!$get_file_info) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Error occured when try to prepare your save info!");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        if (!in_array($get_mime, $allow_mime)) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File type don't allow to save the edit content!");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        // In case of someone can POST the heavy file.
                        if ($get_size >= 52428800) {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "File too big to save.");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        // Update File Content
                        if (file_exists($actual_path)) {
                            $old_content = file_get_contents($actual_path) ?? 'FATAL ERROR! CONTENT RETRIVE FAILURE!';
                            if (file_put_contents($actual_path, $getcontent) === false) {
                                $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to save your edit content file!");
                                header('location:../../index.php?req=doc');
                                die;
                            }
                            $getfilesize = filesize($actual_path);
                            $get_file_hash256 = hash_file('sha256', $actual_path);
                        }
                        if ($getfilesize > $session_storage_left || $getfilesize >= 52428800) {
                            // Rollback
                            file_put_contents($actual_path, $old_content);
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Quota limit or maximum file size exceeded! Changes reverted and file content restored.");
                            header('location:../../index.php?req=doc');
                            die;
                        }
                        $result = $update_content->updatefilecontent($getfilesize, $get_file_hash256, $getidfile);
                        if ($result) {
                            header('location:../../index.php?req=edittext_file&idview='.$getidfile); // Return to edit area
                            die;
                        } else {
                            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Failed to update new info data!");
                            header('location:../../index.php?req=doc');
                            break;
                        }
                        header('location:../../index.php?req=doc');
                        die;

                    
                }
            }
            // Tail
        } else {
            // Regen the CSRF even on fail.
            unset($_SESSION['CSRF_TOKEN']);
            // Recheck if the session already empty to insert new token.
            $regen_csrf = bin2hex(random_bytes(64 / 2));
            $_SESSION['CSRF_TOKEN'] = $regen_csrf;
            
            $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Something when wrong. Please try again.");
            header('location:../../index.php?req=doc');
            die;
        }
    } else {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "No POST handle detected.");
        header('location:../../index.php?req=doc');
        die;
    }
?>
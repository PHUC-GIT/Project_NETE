<?php
    session_start();
    set_time_limit(0);
    require '../../Element/Database/trackcls.php';
    // Check if the legit of id file. IT SHOULD HAVE IF YOU GO BY SYSTEM URL!
    if (isset($_GET['fileid'])) {
        $getidfile = $_GET['fileid'];
    } else {
        die("ERROR: No ID context to read the files.");
    }
    // Get SESSION user!
    if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
    } else {
       die("!FATAL ERROR!: Sir. Where your co government ID card?");
    }
    $obj=new track();
    $get_info = $obj->getinfo_spy($getidfile);
        // If not flagged
        if (!$get_info) {
        die("Unauthorized detected! This file isn't in report list. Rejected review for maintain user privacy harmony.");
    }
    $totalwhitelist = ["image/jpeg", "image/vnd.microsoft.icon", "image/png", "image/x-icon", "video/mp4", "audio/mpeg", "audio/ogg", "application/pdf", "text/plain"];

    if(isset($_GET['secure'])) {
        $requestaction = $_GET['secure'];
        switch ($requestaction) {
            case 'view_file':
                // Reconfirm mime type from file view html
                if (!in_array($get_info->mime_type, $totalwhitelist)) {
                    die("File rejected. This file type don't allow to read.");
                }

                $actual_path = "../." . $get_info->file_link;
                if (!file_exists($actual_path)) {
                    http_response_code(404);
                    $putitred=$obj->redtrigger($getidfile);
                    die('File not found.');
                }
                header("Content-Type: ". $get_info->mime_type);
                header("X-Content-Type-Options: nosniff");
                header("Content-Disposition: inline; filename=\"" . basename(htmlspecialchars($get_info->file_name, ENT_QUOTES, 'UTF-8')) . "\"");

                // Caching header
                // header('Cache-Control: public, max-age=86400'); // Cache for 1 day
                // header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');

                $fp = fopen($actual_path, 'rb');
                while (!feof($fp)) {
                    echo fread($fp, 8192);
                    flush();
                    if (connection_aborted()) break;
                }
                fclose($fp);
                die;

            case 'view_media':
                // Reconfirm mime type from file view html
                if (!in_array($get_info->mime_type, $totalwhitelist)) {
                    die("File rejected. This file type don't allow to read.");
                }

                $actual_path = "../." . $get_info->file_link;
                if (!file_exists($actual_path)) {
                    http_response_code(404);
                    $putitred=$obj->redtrigger($getidfile);
                    die('File not found.');
                }
                
                 // --- Core Logic for Byte-Range Streaming ---
                $fileSize = filesize("../." . $get_info->file_link);
                $range = 0;
                $start = 0;
                $end = $fileSize - 1;

                // Check for Range header
                if (isset($_SERVER['HTTP_RANGE'])) {
                    // Parse the range header
                    $range = $_SERVER['HTTP_RANGE'];
                    $range = str_replace('bytes=', '', $range);
                    list($start, $end) = explode('-', $range);
                
                    // Ensure valid range
                    $start = max(0, intval($start)); // Start byte cannot be negative
                    $end = ($end === '') ? $fileSize - 1 : min($fileSize - 1, intval($end)); // End byte cannot exceed file size
                
                    if ($start > $end || $start >= $fileSize) {
                        header('HTTP/1.1 416 Requested Range Not Satisfiable');
                        header('Content-Range: bytes */' . $fileSize);
                        exit;
                    }
                
                    // Set 206 Partial Content status
                    header('HTTP/1.1 206 Partial Content');
                    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $fileSize);
                } else {
                    // Full content request (initial load or browser without range support)
                    header('HTTP/1.1 200 OK');
                }
            
                // Common headers for both partial and full content
                header("Content-Type: " . $get_info->mime_type);
                header("X-Content-Type-Options: nosniff");
                header("Content-Length: " . ($end - $start + 1)); // Important for partial content
                header("Accept-Ranges: bytes"); // Tell the browser we support byte ranges
                header("Content-Disposition: inline; filename=\"" . basename(htmlspecialchars($get_info->file_name, ENT_QUOTES, 'UTF-8')) . "\"");
            
                // Clear any output buffers to prevent corruption
                if (ob_get_level()) {
                    ob_end_clean();
                }
            
                // Open the file for reading
                $fp = fopen("../." . $get_info->file_link, 'rb');
                if ($fp === false) {
                    http_response_code(500);
                    die("Error opening file for streaming.");
                }
            
                // Seek to the starting byte
                fseek($fp, $start);
            
                // Read and output the requested bytes
                $buffer = 4096; // Chunk size
                $bytesLeft = $end - $start + 1;
                while (!feof($fp) && $bytesLeft > 0) {
                    $chunk = fread($fp, min($buffer, $bytesLeft));
                    echo $chunk;
                    $bytesLeft -= strlen($chunk);
                    flush(); // Flush output buffer
                }
            
                fclose($fp);
                die;
            
            case 'download_file':
                header('Accept-Ranges: bytes');
                header("Content-Type: ". $get_info->mime_type);
                header("X-Content-Type-Options: nosniff");
                header("Content-Disposition: attachment; filename=\"" . basename(htmlspecialchars($get_info->file_name, ENT_QUOTES, 'UTF-8')) . "." . basename(htmlspecialchars($get_info->type, ENT_QUOTES, 'UTF-8')). "\"");
                header("Content-Length: " . $get_info->size); // Use this if you don't trust database thingy: filesize("../." . $get_info->file_link)

                // Optional: Add caching headers for downloads (less common but can be done)
                header('Cache-Control: public, max-age=0, must-revalidate');
                header('Pragma: public');
                
                $fp = fopen("../." . $get_info->file_link, 'rb');
                while (!feof($fp)) {
                    echo fread($fp, 8192);
                    flush();
                    if (connection_aborted()) break;
                }
                fclose($fp);
                die;
        }
    }
<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');
require "AIengine.php";

if (!isset($_POST['input']) || empty(trim($_POST['input']))) {
    ob_end_clean();
    echo json_encode([
        "mode"     => "error",
        "label"    => "ERROR",
        "response" => "D.U.M.B AI needs input to be dumb about."
    ]);
    exit;
}

$words  = load_words();
$lang   = load_grammar();
$result = DUMB_respond($words, $lang);
ob_end_clean();
echo json_encode($result);

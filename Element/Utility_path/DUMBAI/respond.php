<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

error_reporting(0);
ini_set('display_errors', 0);
ob_start();
header('Content-Type: application/json');
require "AIengine.php";
$whitelist = array("M1", "M2", "M3");

if (!isset($_POST['input']) || empty(trim($_POST['input']))) {
    ob_end_clean();
    echo json_encode([
        "mode"     => "error",
        "label"    => "ERROR",
        "response" => "D.U.M.B AI needs input to be dumb about."
    ]);
    die;
}

if (!in_array($_POST['model'], $whitelist)) {
    ob_end_clean();
    echo json_encode([
        "mode"     => "error",
        "label"    => "ERROR",
        "response" => "This model don't exist..."
    ]);
    die;
}

$words = load_words();
$lang  = load_grammar();
$input = trim($_POST['input']);
$model = trim($_POST['model']);
$result = DUMB_respond($words, $lang, $input, $model);
ob_end_clean();
echo json_encode($result);

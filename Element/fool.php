<?php
// NET.ESCAPE | (C) 2024-2026 PHUC-GIT | GNU AGPLv3 (See /LICENSE)

    // Check if someone direct URL without index.php
    if (!defined('NETE_INTEGRITY_CHECK')) {
        header('location: ../Index.php');
        die;
    }
    require "./Element/Database/reportcls.php";
    $get_name = "default";
    if (isset($_SESSION['AUTHENTICATE_USER'])) {
        $get_name = $_SESSION['AUTHENTICATE_USER'];
    }
    if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
        $get_name = $_SESSION['AUTHENTICATE_ADMIN'];
    }
    $obj=new report();
    $user = $name_login ?? '';
    $cause = "Unauthorized access by manipulate req url";
    if (isset($_SESSION['AUTHENTICATE_ADMIN'])) {
        $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Invalid URL Detected.");
        echo "<script>window.location.href='./index.php';</script>";
        die;
    }
    $obj->report_in($cause, $user);
    $_SESSION['MODAL_ERROR_MESSAGE'] = array(true, "Request URL Invalid.");
    echo "<script>window.location.href='./index.php';</script>";
    die;
?>
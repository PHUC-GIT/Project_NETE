<?php
// Check if a file was submitted via the form
if ($_FILES['idkey']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['idkey']['tmp_name'])) {

    // 1. Get file hash password.
    $get_hash_value = hash_file('sha256', $_FILES['idkey']['tmp_name']);

    // 2. Display the result to user.
    echo "<h1>Generated SHA256 Hash:</h1>";
    echo "<p>Copy this entire string and paste it into the 'key_value' column of your admin's row in the database:</p>";
    echo "<textarea rows='4' cols='80' readonly>{$get_hash_value}</textarea>";
} else {
    die('There is problem with uploaded files.');
}
?>
<?php
// Check if a password was submitted via the form
if (isset($_POST['password']) && !empty($_POST['password'])) {
    
    // 1. Get the plaintext password
    $plaintext_password = $_POST['password'];
    
    // 2. GENERATE the secure hash using the recommended default algorithm (Bcrypt/Argon2)
    // This function automatically handles secure salting and cost factors.
    $hashed_password = password_hash($plaintext_password, PASSWORD_DEFAULT);
    
    // 3. Display the result to the user
    echo "<h1>Generated Password Hash:</h1>";
    echo "<p>Copy this entire string and paste it into the 'password' column of your user's row in the database:</p>";
    echo "<textarea rows='4' cols='80' readonly>{$hashed_password}</textarea>";
    
} else {
    echo "<p>No password provided. Please go back and enter a password.</p>";
    die;
}
?>
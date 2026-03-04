<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NET.E Hash Generator</title>
</head>
<body>
    <h1>Password Hash Generator</h1>
    <p>Use this tool to generate a secure hash for manual insertion into the database's user table.</p>
    <form method="POST" action="process.php">
        <label for="password">Enter Your Text As Password:</label><br>
        <input type="text" id="password" name="password" required><br><br>
        <input type="submit" value="Generate Hash">
    </form>
    <h1>SHA256 Hash For Admin Key_Value File</h1>
    <form method="POST" enctype="multipart/form-data" action="file_process.php">
        <label for="idkey">Upload:</label><br>
        <input id="key_upload_font" type="file" name="idkey" id="idkey"/><br><br>
        <input type="submit" value="Get SHA256 Hash">
    </form>
</body>
</html>
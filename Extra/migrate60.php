<?php
// NETE - Migration Script
// CHAR64 + Custom ID migration
// Run once then DELETE this file immediately!

$servername = '127.0.0.1';
$dbname     = 'db_name'; // Your target database name goes here
$username   = 'root'; // Your username on DB.
$password   = ''; // Your DB password. Leave empty if your DB profile don't have one.

try {
    $pdo = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$steps = [

    // Step 1: Drop all FK constraints
    "Drop FK: files → user"         => "ALTER TABLE `files` DROP FOREIGN KEY `FK_USER_OWNER`",
    "Drop FK: note → user"          => "ALTER TABLE `note` DROP FOREIGN KEY `FK_USER`",
    "Drop FK: preference → user"    => "ALTER TABLE `preference` DROP FOREIGN KEY `FK_USERID`",
    "Drop FK: user_report → user"   => "ALTER TABLE `user_report` DROP FOREIGN KEY `user_report_ibfk_1`",

    // Step 2: Migrate PK columns (Custom ID = 32 hex chars, fits in CHAR(64) with room)
    "Alter: user.iduser → CHAR(64)"         => "ALTER TABLE `user` MODIFY `iduser` CHAR(64) NOT NULL COMMENT 'Unique id'",

    // Step 3: Migrate FK columns to CHAR(64) to match user PK
    "Alter: files.Uploader → CHAR(64)"      => "ALTER TABLE `files` MODIFY `Uploader` CHAR(64) NOT NULL COMMENT 'Foreign Key'",
    "Alter: note.Whois → CHAR(64)"          => "ALTER TABLE `note` MODIFY `Whois` CHAR(64) NOT NULL COMMENT 'Foreign key'",
    "Alter: preference.userid → CHAR(64)"   => "ALTER TABLE `preference` MODIFY `userid` CHAR(64) NOT NULL COMMENT 'id'",
    "Alter: user_report.USER_ID → CHAR(64)" => "ALTER TABLE `user_report` MODIFY `USER_ID` CHAR(64) DEFAULT NULL",

    // Step 4: Other ID columns
    "Alter: admin.id_admin → CHAR(64)"      => "ALTER TABLE `admin` MODIFY `id_admin` CHAR(64) NOT NULL COMMENT 'unique id'",
    "Alter: admin.key_value → CHAR(64)"     => "ALTER TABLE `admin` MODIFY `key_value` CHAR(64) DEFAULT NULL COMMENT 'That unique key'",
    "Alter: report.id → CHAR(64)"           => "ALTER TABLE `report` MODIFY `id` CHAR(64) NOT NULL COMMENT 'Unique ID'",
    "Alter: user_report.id → CHAR(64)"      => "ALTER TABLE `user_report` MODIFY `id` CHAR(64) NOT NULL",
    "Alter: files.id → CHAR(64)"            => "ALTER TABLE `files` MODIFY `id` CHAR(64) NOT NULL COMMENT 'unique id'",
    "Alter: note.id → CHAR(64)"             => "ALTER TABLE `note` MODIFY `id` CHAR(64) NOT NULL COMMENT 'Unique'",
    "Alter: verification_locks.verify_id → CHAR(64)" => "ALTER TABLE `verification_locks` MODIFY `verify_id` CHAR(64) NOT NULL",

    // Step 5: SHA-256 file fingerprint column — always 64 hex chars, CHAR(64) is a perfect fit
    "Alter: files.sha256_hash → CHAR(64)"   => "ALTER TABLE `files` MODIFY `sha256_hash` CHAR(64) NOT NULL COMMENT 'The unique hash of the file'",

    // Step 6: Convert all tables to utf8mb4
    "Charset: files → utf8mb4"              => "ALTER TABLE `files` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: note → utf8mb4"               => "ALTER TABLE `note` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: user → utf8mb4"               => "ALTER TABLE `user` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: preference → utf8mb4"         => "ALTER TABLE `preference` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: admin → utf8mb4"              => "ALTER TABLE `admin` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: report → utf8mb4"             => "ALTER TABLE `report` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: user_report → utf8mb4"        => "ALTER TABLE `user_report` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",
    "Charset: verification_locks → utf8mb4" => "ALTER TABLE `verification_locks` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci",

    // Step 7: Restore FK constraints
    "Restore FK: files → user"      => "ALTER TABLE `files` ADD CONSTRAINT `FK_USER_OWNER` FOREIGN KEY (`Uploader`) REFERENCES `user` (`iduser`)",
    "Restore FK: note → user"       => "ALTER TABLE `note` ADD CONSTRAINT `FK_USER` FOREIGN KEY (`Whois`) REFERENCES `user` (`iduser`)",
    "Restore FK: preference → user" => "ALTER TABLE `preference` ADD CONSTRAINT `FK_USERID` FOREIGN KEY (`userid`) REFERENCES `user` (`iduser`)",
    "Restore FK: user_report → user"=> "ALTER TABLE `user_report` ADD CONSTRAINT `user_report_ibfk_1` FOREIGN KEY (`USER_ID`) REFERENCES `user` (`iduser`)",
];

$success = 0;
$failed  = 0;

echo "<pre style='font-family:monospace;'>";
echo "=== NETE Migration: INT → CHAR(64) | Build 60+ & v0.7.0+ ===\n\n";

foreach ($steps as $label => $sql) {
    try {
        $pdo->exec($sql);
        echo "[OK]   $label\n";
        $success++;
    } catch (PDOException $e) {
        echo "[FAIL] $label\n";
        echo "       reason: " . $e->getMessage() . "\n";
        $failed++;
    }
}

echo "\n=== Done: $success ok, $failed failed ===\n";
echo "\n!! DELETE THIS FILE NOW !!\n";
echo "</pre>";
?>
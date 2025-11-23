<?php
// config.php
session_start();

$db_host = '127.0.0.1';
$db_name = 'eventhub';
$db_user = 'root';
$db_pass = ''; // change if you have a password

try {
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// helper: current user
function current_user() {
    return $_SESSION['user'] ?? null;
}
?>

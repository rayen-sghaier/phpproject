<?php
require 'config.php';

if (!current_user()) {
    header('Location: login.php');
    exit;
}

$event_id = $_GET['id'] ?? null;
if (!$event_id) {
    header('Location: index.php');
    exit;
}

// fetch event to check permission
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: index.php');
    exit;
}

$user = current_user();

// only admin or event creator can delete
if ($user['role'] === 'admin' || $user['id'] == $event['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
}

header('Location: index.php');
exit;

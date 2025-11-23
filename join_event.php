<?php
// join_event.php
require 'config.php';
if (!current_user()) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['event_id'] ?? 0);
    if ($event_id) {
        try {
            $stmt = $pdo->prepare("INSERT INTO registrations (user_id,event_id) VALUES (?,?)");
            $stmt->execute([current_user()['id'], $event_id]);
            header('Location: events.php?id=' . $event_id . '&joined=1');
            exit;
        } catch (Exception $e) {
            // duplicate or error
            header('Location: events.php?id=' . $event_id . '&joined=0');
            exit;
        }
    }
}
header('Location: index.php');
exit;
?>

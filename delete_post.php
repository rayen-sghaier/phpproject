<?php
require 'config.php';

$user = current_user();
if (!$user) {
    header("Location: login.php");
    exit;
}

$post_id = intval($_GET['id'] ?? 0);
if ($post_id === 0) {
    die("Invalid post ID");
}

// Get post info
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found");
}

// Only admin or post owner can delete
if ($user['role'] !== 'admin' && $post['user_id'] != $user['id']) {
    die("You are not allowed to delete this post");
}

// Delete media file if exists
if ($post['media'] && file_exists($post['media'])) {
    unlink($post['media']);
}

// Delete post from database
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

header("Location: post_feed.php");
exit;
?>

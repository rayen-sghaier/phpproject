<?php
require 'config.php';

$user = current_user();

// Only admin
if (!$user || $user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);

if ($id == 0) {
    die("User ID missing.");
}

// Prevent admin deleting himself
if ($id == $user['id']) {
    die("Admin cannot delete himself.");
}

// Get user info
$stmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ?");
$stmt->execute([$id]);
$targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$targetUser) {
    die("User not found.");
}

// If confirmed via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delete User</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm mx-auto" style="max-width:500px;">
        <div class="card-body text-center">
            <h4 class="card-title text-danger mb-3">Delete User?</h4>
            <p class="mb-4">Are you sure you want to delete <strong><?= htmlspecialchars($targetUser['name']) ?></strong>? This action cannot be undone.</p>

            <form method="post">
                <button type="submit" class="btn btn-danger">Yes, Delete</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php
require 'config.php';

$user = current_user();

// Only admin can access
if (!$user || $user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("User ID missing.");
}

$id = intval($_GET['id']);

// Get user info
$stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE id = ?");
$stmt->execute([$id]);
$targetUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$targetUser) {
    die("User not found.");
}

// If form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newRole = $_POST['role'];

    $update = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $update->execute([$newRole, $id]);

    header("Location: admin_dashboard.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit User Role</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-sm mx-auto" style="max-width:500px;">
        <div class="card-body">
            <h3 class="card-title mb-4">Edit Role: <?= htmlspecialchars($targetUser['name']) ?></h3>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= $targetUser['role'] === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $targetUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php
require 'config.php';

$user = current_user();

// Only admin
if (!$user || $user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$users = $pdo->query("
    SELECT id, name, email, role, created_at 
    FROM users ORDER BY created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$events = $pdo->query("
    SELECT e.id, e.title, e.event_date, e.location, e.created_at,
           u.name AS creator
    FROM events e 
    JOIN users u ON e.user_id = u.id
    ORDER BY e.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>

    <!-- BOOTSTRAP -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }
        .sidebar {
            height: 100vh;
            background: #343a40;
            padding: 20px;
        }
        .sidebar a {
            color: #ddd;
            display: block;
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background: #495057;
            color: #fff;
        }
        .title {
            font-weight: 700;
        }
    </style>
</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-3 sidebar">
            <h3 class="text-light mb-4">Admin Panel</h3>

            <a href="index.php">🏠 Home</a>
            <a href="#users">👤 Users</a>
            <a href="#events">📅 Events</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-9 p-4">

            <h2 class="title mb-4">Welcome, <?= htmlspecialchars($user['name']) ?></h2>

            <!-- USERS -->
            <h4 id="users" class="mb-3">👤 Users</h4>

            <div class="card shadow-sm mb-5">
                <div class="card-body">

                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th colspan="2">Actions</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td><?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class="badge bg-primary"><?= $u['role'] ?></span></td>
                                <td><?= $u['created_at'] ?></td>

                                <td>
                                    <a href="edit_role.php?id=<?= $u['id'] ?>"
                                       class="btn btn-sm btn-warning">
                                        Edit Role
                                    </a>
                                </td>

                                <td>
                                    <a href="delete_user.php?id=<?= $u['id'] ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this user?');">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>
            </div>

            <!-- EVENTS -->
            <h4 id="events" class="mb-3">📅 Events</h4>

            <div class="card shadow-sm">
                <div class="card-body">

                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Location</th>
                            <th>Creator</th>
                            <th>Created</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach($events as $ev): ?>
                            <tr>
                                <td><?= $ev['id'] ?></td>
                                <td><?= htmlspecialchars($ev['title']) ?></td>
                                <td><?= $ev['event_date'] ?></td>
                                <td><?= htmlspecialchars($ev['location']) ?></td>
                                <td><?= htmlspecialchars($ev['creator']) ?></td>
                                <td><?= $ev['created_at'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>

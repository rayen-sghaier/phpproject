<?php
// index.php
require 'config.php';

// search param
$search = trim($_GET['q'] ?? '');

// fetch events
if ($search) {
    $stmt = $pdo->prepare("SELECT e.*, u.name as creator FROM events e JOIN users u ON e.user_id = u.id WHERE e.title LIKE ? OR e.location LIKE ? ORDER BY e.event_date ASC");
    $stmt->execute(["%{$search}%","%{$search}%"]);
} else {
    $stmt = $pdo->query("SELECT e.*, u.name as creator FROM events e JOIN users u ON e.user_id = u.id ORDER BY e.event_date ASC");
}
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// fetch latest posts (limit 5)
$postsStmt = $pdo->query("
    SELECT p.*, u.name 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
");
$posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

$user = current_user();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>EventHub - Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
header { padding: 20px 0; background-color: #343a40; color: white; }
header h1 { margin: 0; font-size: 2.5rem; }
.navbar-links a { color: white; margin-left: 15px; text-decoration: none; }
.navbar-links a:hover { text-decoration: underline; }
.search-form { margin-top: 10px; }
.event-card, .post-card { margin-bottom: 20px; border-radius: 10px; }
.event-card h5, .post-card strong { margin-bottom: 10px; display: block; }
.event-card p, .post-card p { margin-bottom: 5px; }
.post-card img, .post-card video { max-width: 100%; border-radius: 8px; margin-top: 10px; }
</style>
</head>
<body>

<!-- Header / Navbar -->
<header class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1>EventHub</h1>
        <div class="navbar-links">
            <?php if($user): ?>
                Bonjour, <?=htmlspecialchars($user['name'])?> |
                <a href="create_event.php" class="btn btn-sm btn-light">Create Event</a>
                <a href="post_feed.php" class="btn btn-sm btn-light">Feed</a>
                <?php if($user['role'] === 'admin'): ?><a href="admin_dashboard.php" class="btn btn-sm btn-warning">Admin</a><?php endif; ?>
                <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-sm btn-light">Login</a>
                <a href="register.php" class="btn btn-sm btn-success">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Form -->
    <form method="get" class="search-form d-flex mt-3">
        <input name="q" class="form-control me-2" placeholder="Search events or location" value="<?=htmlspecialchars($search)?>">
        <button class="btn btn-primary">Search</button>
    </form>
</header>

<!-- Main Content -->
<main class="container mt-5">
    <!-- Upcoming Events -->
    <h2 class="mb-4">Upcoming Events</h2>
    <div class="row">
        <?php if(empty($events)): ?>
            <p class="text-muted">No events found.</p>
        <?php else: foreach($events as $ev): ?>
            <div class="col-md-6">
                <div class="card event-card shadow-sm p-3">
                    <h5><a href="events.php?id=<?= $ev['id'] ?>" class="text-decoration-none"><?= htmlspecialchars($ev['title']) ?></a></h5>
                    <p><?= nl2br(htmlspecialchars(substr($ev['description'],0,220))) ?><?php if(strlen($ev['description'])>220) echo '...'; ?></p>
                    <p class="text-muted mb-0">
                        <strong>Date:</strong> <?= date('Y-m-d H:i', strtotime($ev['event_date'])) ?> |
                        <strong>Location:</strong> <?= htmlspecialchars($ev['location']) ?> |
                        <strong>Creator:</strong> <?= htmlspecialchars($ev['creator']) ?>
                    </p>
                    <!-- Delete button for admin or event creator -->
                    <?php if($user && ($user['role'] === 'admin' || $user['id'] == $ev['user_id'])): ?>
                        <a href="delete_event.php?id=<?= $ev['id'] ?>" class="btn btn-sm btn-danger mt-2" onclick="return confirm('Are you sure you want to delete this event?')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- Latest Posts -->
    <h2 class="mt-5 mb-4">Latest Posts</h2>
    <div class="row justify-content-center">
        <?php if(empty($posts)): ?>
            <p class="text-muted">No posts yet.</p>
        <?php else: foreach($posts as $post): ?>
            <div class="col-md-6">
                <div class="card post-card shadow-sm p-3">
                    <strong><?= htmlspecialchars($post['name']) ?></strong>
                    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    <?php if(!empty($post['media'])): ?>
                        <?php if($post['media_type'] === 'image'): ?>
                            <img src="<?= $post['media'] ?>" alt="Post Image">
                        <?php else: ?>
                            <video controls>
                                <source src="<?= $post['media'] ?>">
                            </video>
                        <?php endif; ?>
                    <?php endif; ?>
                    <small class="text-muted"><?= $post['created_at'] ?></small>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
    <div class="text-center mt-3">
        <a href="post_feed.php" class="btn btn-primary">See All Posts</a>
    </div>
</main>

</body>
</html>

<?php
require 'config.php';

$user = current_user();
if (!$user) {
    header("Location: login.php");
    exit;
}

// Handle new post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    $mediaPath = null;
    $mediaType = null;

    if (!empty($_FILES['media']['name'])) {
        $filename = time() . '_' . $_FILES['media']['name'];
        $target = 'uploads/' . $filename;
        move_uploaded_file($_FILES['media']['tmp_name'], $target);

        $mediaPath = $target;
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $mediaType = in_array(strtolower($ext), ['mp4','mov','avi']) ? 'video' : 'image';
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, media, media_type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['id'], $content, $mediaPath, $mediaType]);
    header("Location: post_feed.php");
    exit;
}

// Fetch posts
$posts = $pdo->query("
    SELECT p.*, u.name
    FROM posts p 
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Post Feed - EventHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #f4f6f9;
}
.center-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 50px;
}
.card-post {
    width: 100%;
    max-width: 600px;
}
</style>
</head>
<body>

<div class="center-container">

    <!-- New Post Form -->
    <div class="card card-post mb-4 shadow-sm p-3">
        <form method="post" enctype="multipart/form-data">
            <textarea name="content" class="form-control mb-2" placeholder="What's on your mind?" required></textarea>
            <input type="file" name="media" class="form-control mb-2" accept="image/*,video/*">
            <button type="submit" class="btn btn-primary w-100">Post</button>
        </form>
    </div>

    <!-- Posts -->
    <?php foreach ($posts as $post): ?>
        <div class="card card-post mb-3 shadow-sm p-3">
            <strong><?= htmlspecialchars($post['name']) ?></strong><br>
            <?= nl2br(htmlspecialchars($post['content'])) ?><br>
            <?php if ($post['media']): ?>
                <?php if ($post['media_type'] === 'image'): ?>
                    <img src="<?= $post['media'] ?>" class="img-fluid mt-2">
                <?php else: ?>
                    <video controls class="w-100 mt-2">
                        <source src="<?= $post['media'] ?>">
                    </video>
                <?php endif; ?>
            <?php endif; ?>
            <small class="text-muted"><?= $post['created_at'] ?></small>
        </div>
    <?php endforeach; ?>

</div>
<?php if ($user['role'] === 'admin' || $post['user_id'] == $user['id']): ?>
    <a href="delete_post.php?id=<?= $post['id'] ?>" 
       class="btn btn-sm btn-danger mt-2"
       onclick="return confirm('Are you sure you want to delete this post?');">
       Delete
    </a>
<?php endif; ?>

</body>
</html>

<?php
// events.php
require 'config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}

// get event
$stmt = $pdo->prepare("SELECT e.*, u.name as creator FROM events e JOIN users u ON e.user_id = u.id WHERE e.id = ?");
$stmt->execute([$id]);
$ev = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ev) { echo "Event not found."; exit; }

// count attendees
$attStmt = $pdo->prepare("SELECT COUNT(*) FROM registrations WHERE event_id = ?");
$attStmt->execute([$id]);
$attCount = $attStmt->fetchColumn();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?=htmlspecialchars($ev['title'])?></title>
<link rel="stylesheet" href="assets/css/style.css"></head>
<body>
<a href="index.php">&larr; Back</a>
<h2><?=htmlspecialchars($ev['title'])?></h2>
<p><strong>Date:</strong> <?=date('Y-m-d H:i', strtotime($ev['event_date']))?></p>
<p><strong>Location:</strong> <?=htmlspecialchars($ev['location'])?></p>
<p><?=nl2br(htmlspecialchars($ev['description']))?></p>
<p><strong>Creator:</strong> <?=htmlspecialchars($ev['creator'])?></p>
<p><strong>Attendees:</strong> <?= $attCount ?></p>

<?php if(!current_user()): ?>
  <p><a href="login.php">Login</a> to RSVP.</p>
<?php else: ?>
  <form method="post" action="join_event.php">
    <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
    <button type="submit">RSVP / Join</button>
  </form>
<?php endif; ?>

</body>
</html>

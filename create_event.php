<?php
// create_event.php
require 'config.php';
if (!current_user()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $date = trim($_POST['event_date'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if (!$title || !$date) {
        $error = "Title and date required.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO events (title,description,event_date,location,user_id) VALUES (?,?,?,?,?)");
        $stmt->execute([$title,$desc,$date,$location,current_user()['id']]);
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Create Event</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<a href="index.php">&larr; Home</a>
<h2>Create Event</h2>
<?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  <label>Title: <input name="title" required></label><br>
  <label>Date & time: <input name="event_date" type="datetime-local" required></label><br>
  <label>Location: <input name="location"></label><br>
  <label>Description:<br><textarea name="description" rows="6"></textarea></label><br>
  <button>Create</button>
</form>
</body>
</html>

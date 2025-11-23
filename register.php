<?php
// register.php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$name || !$email || !$password) {
        $error = "Please fill all fields.";
    } else {
        // check existing
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
            $stmt->execute([$name,$email,$hash]);
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Register - EventHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { 
    background-color: #f8f9fa; 
    font-family: 'Segoe UI', sans-serif;
}
.register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.card-register {
    width: 100%;
    max-width: 400px;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    background-color: white;
}
.card-register h2 {
    text-align: center;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="register-container">
    <div class="card card-register">
        <h2>Register</h2>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input name="name" type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>

        <p class="text-center mt-3">
            <a href="login.php">Login</a>
        </p>
    </div>
</div>

</body>
</html>

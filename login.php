<?php
// login.php
require 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT id,name,email,password,role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id'=>$user['id'],
                'name'=>$user['name'],
                'email'=>$user['email'],
                'role'=>$user['role']
            ];
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Fill email and password.";
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login - EventHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { 
    background-color: #f8f9fa; 
    font-family: 'Segoe UI', sans-serif;
}
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.card-login {
    width: 100%;
    max-width: 400px;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    background-color: white;
}
.card-login h2 {
    text-align: center;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="login-container">
    <div class="card card-login">
        <h2>Login</h2>

        <?php if(!empty($_GET['registered'])): ?>
            <div class="alert alert-success">Registered! Login now.</div>
        <?php endif; ?>

        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center mt-3">
            <a href="register.php">Register</a>
        </p>
    </div>
</div>

</body>
</html>

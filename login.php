<?php
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

renderHeader("Login");
?>

<div class="hero">
    <h1>Welcome Back</h1>
    <p>Continue your journey on Blogger.</p>
</div>

<div class="glass-form">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn-neon" style="width: 100%;">Login Now</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
        Don't have an account? <a href="signup.php" style="color: var(--primary); text-decoration: none;">Sign Up</a>
    </p>
</div>

<?php renderFooter(); ?>

<?php
require_once 'db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->execute([$email, $username]);
        if ($stmt->fetch()) {
            $error = "Username or Email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: index.php");
                exit;
            } else {
                $error = "Registration failed.";
            }
        }
    }
}

renderHeader("Join Us");
?>

<div class="hero">
    <h1>Create Account</h1>
    <p>Join the community of modern storytellers.</p>
</div>

<div class="glass-form">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required placeholder="Pick a unique name">
        </div>
        <div class="input-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="yourname@example.com">
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Min. 8 characters">
        </div>
        <button type="submit" class="btn-neon" style="width: 100%;">Get Started</button>
    </form>
    <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
        Already have an account? <a href="login.php" style="color: var(--primary); text-decoration: none;">Login</a>
    </p>
</div>

<?php renderFooter(); ?>

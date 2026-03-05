<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found or unauthorized.");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
        if ($stmt->execute([$title, $content, $id])) {
            header("Location: post.php?id=$id");
            exit;
        } else {
            $error = "Update failed.";
        }
    }
}

renderHeader("Edit Post");
?>

<div class="hero">
    <h1>Refine Your Story</h1>
    <p>Give your post a fresh perspective.</p>
</div>

<div class="glass-form" style="max-width: 800px;">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div class="input-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>
        <div class="input-group">
            <label>Content</label>
            <textarea name="content" rows="12" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>
        <button type="submit" class="btn-neon">Update Post</button>
    </form>
</div>

<?php renderFooter(); ?>

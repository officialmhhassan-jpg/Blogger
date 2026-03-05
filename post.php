<?php
require_once 'db.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT p.*, u.username FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: index.php");
    exit;
}

renderHeader($post['title']);
?>

<?php
// Fetch comments
$stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE c.post_id = ? ORDER BY c.created_at ASC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();
?>

<div class="card" style="margin-top: 2rem;">
    <?php if (!empty($post['image'])): ?>
        <div style="margin-bottom: 2rem; border-radius: 15px; overflow: hidden; border: 1px solid var(--glass-border);">
            <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Cover Image" style="width: 100%; height: auto; max-height: 500px; object-fit: cover; display: block;">
        </div>
    <?php endif; ?>

    <div class="meta">
        <span>Posted by <strong style="color: var(--primary);"><?php echo htmlspecialchars($post['username']); ?></strong></span>
        <span>•</span>
        <span><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
            <div style="margin-left: auto; display: flex; gap: 15px;">
                <a href="edit_post.php?id=<?php echo $post['id']; ?>" style="color: var(--primary); font-size: 0.9rem; text-decoration: none;">Edit</a>
                <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?')" style="color: #ef4444; font-size: 0.9rem; text-decoration: none;">Delete</a>
            </div>
        <?php endif; ?>
    </div>
    
    <h1 style="font-size: 2.8rem; margin-bottom: 2rem;"><?php echo htmlspecialchars($post['title']); ?></h1>
    
    <div class="content" style="font-size: 1.15rem; line-height: 1.8; color: #e2e8f0; white-space: pre-wrap;">
        <?php echo nl2br(htmlspecialchars($post['content'])); ?>
    </div>
</div>

<div class="comments-section" style="margin-top: 4rem;">
    <h3 style="margin-bottom: 2rem; font-size: 1.5rem;">
        Comments (<?php echo count($comments); ?>)
    </h3>

    <?php if (isset($_SESSION['user_id'])): ?>
        <form action="comment_process.php" method="POST">
            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
            <textarea name="comment" rows="3" required></textarea>
            <button type="submit" class="btn-neon">Post Comment</button>
        </form>
    <?php else: ?>
        <p>Please <a href="login.php">login</a> to comment.</p>
    <?php endif; ?>

    <?php foreach ($comments as $comment): ?>
        <div style="margin-top: 10px;">
            <strong><?php echo htmlspecialchars($comment['username']); ?></strong>:
            <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
        </div>
    <?php endforeach; ?>
</div>

<?php renderFooter(); ?>

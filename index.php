<?php
require_once 'db.php';

// Fetch all posts with user info and comment count
$stmt = $pdo->query("
    SELECT p.*, u.username, 
    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count 
    FROM posts p 
    JOIN users u ON p.user_id = u.id 
    ORDER BY p.created_at DESC
");
$posts = $stmt->fetchAll();

renderHeader("Home");
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success" style="max-width: 1000px; margin: 1rem auto;"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<div class="hero">
    <h1>Explore Stories</h1>
    <p>Discover interesting thoughts from our community.</p>
</div>

<div class="feed">
    <?php if (empty($posts)): ?>
        <div class="card" style="text-align: center;">
            <h2>No posts yet.</h2>
            <p>Be the first to share something amazing!</p>
            <br>
            <a href="create_post.php" class="btn-neon">Create First Post</a>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="card">
                <?php if (!empty($post['image'])): ?>
                    <div style="margin-bottom: 1.5rem; border-radius: 12px; overflow: hidden; height: 200px; border: 1px solid var(--glass-border);">
                        <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Thumbnail" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                <?php endif; ?>
                <div class="meta">
                    <span>By <strong style="color: var(--primary);"><?php echo htmlspecialchars($post['username']); ?></strong></span>
                    <span>•</span>
                    <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                    <span>•</span>
                    <span style="color: var(--accent); font-weight: 600;"><?php echo $post['comment_count']; ?> Comments</span>
                </div>
                <h2><a href="post.php?id=<?php echo $post['id']; ?>" style="color: inherit; text-decoration: none;"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                <div class="content">
                    <?php 
                        $excerpt = strip_tags($post['content']);
                        echo htmlspecialchars(strlen($excerpt) > 150 ? substr($excerpt, 0, 150) . '...' : $excerpt); 
                    ?>
                </div>
                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn-neon">Read More</a>
                
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                    <div style="margin-top: 1.5rem; display: flex; gap: 10px;">
                        <a href="edit_post.php?id=<?php echo $post['id']; ?>" style="color: var(--primary); font-size: 0.9rem; text-decoration: none;">Edit</a>
                        <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #ef4444; font-size: 0.9rem; text-decoration: none;">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php renderFooter(); ?>

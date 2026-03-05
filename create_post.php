<?php
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $imagePath = null;

    if (empty($title) || empty($content)) {
        $error = "Please fill in all fields (Title and Content are required).";
    } else {
        // Image Upload Logic
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileExtension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $fileName;

            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($fileExtension), $allowedTypes)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    $imagePath = $targetPath;
                } else {
                    $error = "Failed to upload image.";
                }
            } else {
                $error = "Invalid image type. Allowed: JPG, PNG, GIF, WEBP.";
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, image) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$_SESSION['user_id'], $title, $content, $imagePath])) {
                    header("Location: index.php?msg=Post created successfully!");
                    exit;
                } else {
                    $error = "Failed to create post. Please try again.";
                }
            } catch (PDOException $e) {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}

renderHeader("Create Post");
?>

<div class="hero">
    <h1>Write Something</h1>
    <p>Share your thoughts with the world in style.</p>
</div>

<div class="glass-form" style="max-width: 800px;">
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="create_post.php" enctype="multipart/form-data">
        <div class="input-group">
            <label>Title</label>
            <input type="text" name="title" required placeholder="Enter your post title..." value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
        </div>
        
        <div class="input-group">
            <label>Content</label>
            <textarea name="content" rows="12" required placeholder="Tell your story here..."><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
        </div>

        <div class="input-group">
            <label>Cover Image (Optional)</label>
            <input type="file" name="image" accept="image/*" style="padding: 10px; border: 1px dashed var(--glass-border); background: var(--glass);">
        </div>

        <div style="display: flex; gap: 15px; align-items: center;">
            <button type="submit" class="btn-neon">Publish Post Now</button>
            <a href="index.php" style="color: var(--text-muted); text-decoration: none;">Cancel</a>
        </div>
    </form>
</div>

<?php renderFooter(); ?>

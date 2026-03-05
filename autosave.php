<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(403);
    exit;
}

$post_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

if (empty($title) && empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Nothing to save']);
    exit;
}

if ($post_id > 0) {
    // Update existing draft
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, status = 'draft' WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $post_id, $user_id]);
    echo json_encode(['success' => true, 'id' => $post_id, 'time' => date('H:i:s')]);
} else {
    // Create new draft
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, status) VALUES (?, ?, ?, 'draft')");
    $stmt->execute([$user_id, $title, $content]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId(), 'time' => date('H:i:s')]);
}
?>

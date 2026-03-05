<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php");
    exit;
}

$post_id = $_POST['post_id'];
$comment = trim($_POST['comment']);
$user_id = $_SESSION['user_id'];

if (!empty($comment) && !empty($post_id)) {
    $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
    $stmt->execute([$post_id, $user_id, $comment]);
}

header("Location: post.php?id=$post_id");
exit;
?>

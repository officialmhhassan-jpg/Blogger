<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: index.php");
exit;
?>

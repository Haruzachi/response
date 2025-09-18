<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['user']['id']) || !isset($_GET['id'])) {
    header("Location: ../manage.php");
    exit;
}

$user_id = (int)$_GET['id'];

if ($user_id === (int)$_SESSION['user']['id']) {
    die("You cannot delete your own account!");
}

try {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

header("Location: ../manage.php");
exit;
?>

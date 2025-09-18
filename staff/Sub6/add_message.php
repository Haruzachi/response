<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['message']) && !empty($_POST['department'])) {
    $user_id = $_SESSION['user']['id'];
    $message = trim($_POST['message']);
    $department = $_POST['department'];

    $stmt = $conn->prepare("INSERT INTO messages (user_id, message, department) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $message, $department]);
}

header("Location: coms.php");
exit;
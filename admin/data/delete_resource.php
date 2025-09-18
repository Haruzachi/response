<?php
require_once __DIR__ . '/../../config/db.php';
session_start();

if (!isset($_SESSION['user']['id']) || !isset($_GET['id'])) {
    header("Location: ../manage_resources.php");
    exit;
}

$resource_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
$stmt->execute([$resource_id]);

header("Location: ../manage_resources.php");
exit;
?>
<?php
require_once __DIR__ . '/../config/db.php';
session_start();

// Check if logged in
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Upload folder
$upload_dir = "../staff/profile_images/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $file = $_FILES['profile_image'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($file['type'], $allowed_types)) {

            // Unique filename per user
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $target_file = $upload_dir . "user_" . $user_id . "." . $ext;

            // Delete old image if it exists
            $stmt = $conn->prepare("SELECT profile_image FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $oldImage = $stmt->fetchColumn();

            if ($oldImage && file_exists($upload_dir . $oldImage)) {
                unlink($upload_dir . $oldImage);
            }

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $target_file)) {

                // Save just the filename in DB
                $fileNameOnly = "user_" . $user_id . "." . $ext;
                $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->execute([$fileNameOnly, $user_id]);

                // Update session
                $_SESSION['staff']['profile_image'] = $fileNameOnly;

                header("Location: ../staff/dashboard.php?upload=success");
                exit;
            } else {
                echo "Failed to upload file.";
            }
        } else {
            echo "Invalid file type. Please upload a JPG, PNG, or GIF image.";
        }
    } else {
        echo "File upload error.";
    }
}
?>
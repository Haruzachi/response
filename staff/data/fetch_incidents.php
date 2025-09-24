<?php
require_once __DIR__ . '/../../config/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Fetch only incidents that have coordinates
    $stmt = $conn->prepare("
        SELECT id, caller_name, incident_type, location, latitude, longitude, status, created_at
        FROM emergency_calls
        WHERE latitude IS NOT NULL AND longitude IS NOT NULL
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
<?php
require_once __DIR__ . '/../../config/db.php';

// Fetch only active or pending incidents
$stmt = $conn->prepare("
    SELECT id, caller_name, incident_type, location, latitude, longitude, status
    FROM emergency_calls
    WHERE latitude IS NOT NULL AND longitude IS NOT NULL
    ORDER BY created_at DESC
");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
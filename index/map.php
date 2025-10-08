<?php
require_once "./config/db.php";

if (isset($_GET['location'])) {
    $searchedLocation = htmlspecialchars($_GET['location']);
} else {
    $searchedLocation = "Unknown";
}

// Handle report submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['location'] ?? '';
    $latitude = $_POST['latitude'] ?? '';
    $longitude = $_POST['longitude'] ?? '';
    $description = $_POST['description'] ?? '';
    $photo = '';

    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $photoName = time() . "_" . basename($_FILES["photo"]["name"]);
        $targetFile = $targetDir . $photoName;
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFile)) {
            $photo = $photoName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO hazard_reports (location, latitude, longitude, description, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$location, $latitude, $longitude, $description, $photo]);

    header("Location: map.php?location=" . urlencode($location));
    exit;
}

$stmt = $conn->query("SELECT * FROM hazard_reports ORDER BY created_at DESC");
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hazard Reports Map</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
  html, body { height: 100%; margin: 0; }
  #map { height: 100vh; width: 100%; }
  .form-card {
    position: absolute;
    top: 20px; left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    z-index: 999;
    width: 90%;
    max-width: 500px;
  }
</style>
</head>
<body class="bg-gray-900">

<div id="map"></div>

<!-- Hazard Report Form -->
<div class="form-card">
  <h2 class="text-2xl font-bold mb-2 text-gray-800">Submit Hazard Observation</h2>
  <p class="text-gray-600 mb-4">You searched: <strong><?= $searchedLocation ?></strong></p>

  <form method="POST" enctype="multipart/form-data" class="space-y-3">
    <input type="text" name="location" value="<?= $searchedLocation ?>" class="w-full p-2 border border-gray-300 rounded">
    <textarea name="description" placeholder="Describe the situation..." class="w-full p-2 border border-gray-300 rounded"></textarea>
    <input type="file" name="photo" accept="image/*" class="w-full text-sm text-gray-500">
    <input type="hidden" name="latitude" id="latitude">
    <input type="hidden" name="longitude" id="longitude">
    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">Submit Report</button>
  </form>
  <button id="locateBtn" class="mt-3 w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded">Use My Current Location</button>
</div>

<script>
const map = L.map('map').setView([14.676, 121.0437], 13);

L.tileLayer('https://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
}).addTo(map);

const reports = <?php echo json_encode($reports); ?>;
reports.forEach(rep => {
  if (rep.latitude && rep.longitude) {
    const popup = `
      <strong>${rep.location}</strong><br>
      ${rep.description}<br>
      ${rep.photo ? `<img src="uploads/${rep.photo}" width="100%">` : ''}
    `;
    L.marker([rep.latitude, rep.longitude]).addTo(map).bindPopup(popup);
  }
});

document.getElementById('locateBtn').addEventListener('click', () => {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(pos => {
      const lat = pos.coords.latitude;
      const lon = pos.coords.longitude;
      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lon;
      L.marker([lat, lon]).addTo(map).bindPopup('Your location').openPopup();
      map.setView([lat, lon], 15);
    });
  }
});
</script>
</body>
</html>

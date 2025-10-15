<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCprotektado - Emergency Response System</title>
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- 3D OSM Buildings plugin -->
  <script src="https://cdn.jsdelivr.net/npm/osmbuildings@0.2.2/OSMBuildings-Leaflet.js"></script>

  <style>
    html, body, #map {
      height: 100%;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background: #f3f4f6;
      overflow: hidden;
    }

    #map {
      z-index: 1;
    }

    /* Sidebar */
    #sidebar {
      position: absolute;
      top: 0;
      right: 0;
      width: 340px;
      height: 100%;
      background: #fff;
      color: #333;
      z-index: 999;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: -4px 0 15px rgba(0,0,0,0.15);
    }

    #sidebar h2 {
      font-size: 18px;
      font-weight: 600;
      color: #0077ff;
      text-align: left;
      margin: 20px;
      margin-bottom: 10px;
    }

    #searchBox {
      width: calc(100% - 70px);
      margin: 0 20px 10px 20px;
      padding: 12px 15px;
      border: 1px solid #ccc;
      border-radius: 50px;
      font-size: 15px;
      outline: none;
      transition: all 0.3s ease;
      background-color: #fafafa;
    }

    #searchBox:focus {
      border-color: #0077ff;
      background-color: #fff;
      box-shadow: 0 0 5px rgba(0,119,255,0.3);
    }

    #sidebar button {
      width: calc(100% - 40px);
      margin: 0 20px;
      padding: 12px;
      background: #0077ff;
      border: none;
      border-radius: 8px;
      color: white;
      font-weight: 500;
      cursor: pointer;
      font-size: 15px;
      transition: background 0.3s;
    }

    #sidebar button:hover {
      background: #005fcc;
    }

    .info-section {
      padding: 15px 20px;
      border-top: 1px solid #eee;
    }

    .info-section h3 {
      color: #0077ff;
      font-size: 16px;
      margin-bottom: 10px;
      font-weight: 600;
    }

    .hazard-box {
      background: #f9fafb;
      border: 1px solid #eee;
      border-radius: 10px;
      padding: 10px 15px;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .hazard-box span {
      font-weight: 500;
      color: #333;
    }

    .hazard-box i {
      font-style: normal;
      background: #0077ff;
      color: white;
      padding: 6px 8px;
      border-radius: 6px;
      font-size: 12px;
    }

    #footer {
      text-align: center;
      font-size: 12px;
      color: #777;
      padding: 10px 0 15px 0;
      border-top: 1px solid #eee;
    }
  </style>
</head>
<body>
<div id="map"></div>

<!-- Sidebar -->
<div id="sidebar">
  <div>
    <h2>Search Location</h2>
    <input type="text" id="searchBox" placeholder="Search location...">
    <button onclick="manualSearch()">Find</button>

    <div class="info-section">
      <h3>Hazard Levels In Your Area</h3>
      <div class="hazard-box"><span>Flood Hazard Level</span><i>LOW</i></div>
      <div class="hazard-box"><span>Landslide Hazard Level</span><i>LOW</i></div>
      <div class="hazard-box"><span>Storm Surge Hazard Level</span><i>LOW</i></div>
    </div>
  </div>
  <div id="footer">QCProtektado © 2025</div>
</div>

<script>
  // Define PH bounds
  const philippinesBounds = L.latLngBounds([4.2158, 116.1474], [21.3210, 126.8070]);

  // Initialize map
  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true,
    maxBounds: philippinesBounds,
    maxBoundsViscosity: 1.0
  }).setView([14.676, 121.043], 15); // Quezon City area

  // Base map (OpenStreetMap)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Add OSM Buildings 3D layer
  const osmb = new OSMBuildings(map)
    .load();

  // Search functionality
  let currentMarker = null;

  function manualSearch() {
    const input = document.getElementById('searchBox').value.trim();
    if (!input) return alert("Please enter a location.");

    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(input)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          const { lat, lon } = data[0];
          const target = L.latLng(lat, lon);
          if (currentMarker) map.removeLayer(currentMarker);
          currentMarker = L.marker(target).addTo(map);
          map.setView(target, 17);
        } else {
          alert("No matching location found in the Philippines for: " + input);
        }
      })
      .catch(() => alert("Error fetching location data."));
  }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCprotektado - Emergency Response System</title>
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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

    /* MAP AREA */
    #map {
      z-index: 1;
    }

    /* NOAH-style Sidebar */
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

    /* Header style */
    #sidebar h2 {
      font-size: 18px;
      font-weight: 600;
      color: #0077ff;
      text-align: left;
      margin: 20px;
      margin-bottom: 10px;
    }

    /* Search box */
    #searchBox {
      width: calc(100% - 40px);
      margin: 0 20px 10px 20px;
      padding: 5px 5px;
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

    /* Find button */
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

    /* Information Section */
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

    .hazard-box small {
      color: #666;
      font-size: 12px;
    }

    .hazard-box i {
      font-style: normal;
      background: #0077ff;
      color: white;
      padding: 6px 8px;
      border-radius: 6px;
      font-size: 12px;
    }

    /* Footer */
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
      <div class="hazard-box">
        <span>Flood Hazard Level</span>
        <i>LOW</i>
      </div>
      <div class="hazard-box">
        <span>Landslide Hazard Level</span>
        <i>LOW</i>
      </div>
      <div class="hazard-box">
        <span>Storm Surge Hazard Level</span>
        <i>LOW</i>
      </div>
    </div>
  </div>
  <div id="footer">QCProtektado © 2025</div>
</div>

<script>
  // Define the Philippines bounds
  const philippinesBounds = L.latLngBounds(
    [4.2158, 116.1474],
    [21.3210, 126.8070]
  );

  // Initialize map
  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true,
    maxBounds: philippinesBounds,
    maxBoundsViscosity: 1.0
  }).setView([12.8797, 121.7740], 6);

  // Base map layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    minZoom: 5,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Marker holder
  let currentMarker = null;

  // URL search query
  const params = new URLSearchParams(window.location.search);
  const q = params.get('location');
  if (q) {
    fetchLocation(q);
    document.getElementById('searchBox').value = q;
  }

  // Manual search
  function manualSearch() {
    const input = document.getElementById('searchBox').value.trim();
    if (input) {
      fetchLocation(input);
    } else {
      alert("Please enter a location.");
    }
  }

  // Fetch location and show marker
  function fetchLocation(query) {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          const { lat, lon, display_name } = data[0];
          const target = L.latLng(lat, lon);

          if (currentMarker) {
            map.removeLayer(currentMarker);
          }

          currentMarker = L.marker(target).addTo(map);
          currentMarker.bindPopup(`<b>${display_name}</b>`).openPopup();

          map.setView(target, 16, { animate: true });
        } else {
          alert("No matching location found in the Philippines for: " + query);
        }
      })
      .catch(err => {
        console.error(err);
        alert("Error fetching location data.");
      });
  }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCprotektado - Emergency Response System</title>
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body, #map { height: 100%; margin: 0; }
    body { background: #000; overflow: hidden; }

    /* Right sidebar overlay */
    #sidebar {
      position: absolute;
      top: 0;
      right: 0;
      width: 280px;
      height: 100%;
      background: rgba(0, 0, 0, 0.85);
      color: #fff;
      z-index: 999;
      padding: 20px;
      box-shadow: -3px 0 10px rgba(0,0,0,0.5);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    #sidebar h2 {
      margin-top: 0;
      font-size: 20px;
      color: #00c3ff;
      text-align: center;
    }

    #sidebar input {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 5px;
      background: #222;
      color: #fff;
      margin-bottom: 10px;
    }

    #sidebar button {
      width: 100%;
      padding: 10px;
      background: #00c3ff;
      border: none;
      border-radius: 5px;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
    }

    #sidebar button:hover {
      background: #009edb;
    }

    #footer {
      text-align: center;
      font-size: 12px;
      opacity: 0.6;
    }
  </style>
</head>
<body>
<div id="map"></div>

<!-- Sidebar (Right Side) -->
<div id="sidebar">
  <div>
    <h2>Search Location</h2>
    <input type="text" id="searchBox" placeholder="Enter location..." />
    <button onclick="manualSearch()">Find</button>
  </div>
  <div id="footer">
    QCProtektado © 2025
  </div>
</div>

<script>
  // Define the Philippines bounds
  const philippinesBounds = L.latLngBounds(
    [4.2158, 116.1474],  // Southwest
    [21.3210, 126.8070]  // Northeast
  );

  // Initialize map centered on the Philippines
  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true,
    maxBounds: philippinesBounds,
    maxBoundsViscosity: 1.0
  }).setView([12.8797, 121.7740], 6); // Center of PH

  // Add OpenStreetMap layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    minZoom: 5,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Read search query from URL
  const params = new URLSearchParams(window.location.search);
  const q = params.get('location');

  if (!q) {
    console.log("No search term provided.");
  } else {
    fetchLocation(q);
  }

  // Function to handle manual search from sidebar
  function manualSearch() {
    const input = document.getElementById('searchBox').value.trim();
    if (input) {
      fetchLocation(input);
    } else {
      alert("Please enter a location.");
    }
  }

  // Fetch and zoom to location
  function fetchLocation(query) {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          const { lat, lon, display_name } = data[0];
          const target = L.latLng(lat, lon);

          // Regular zoom animation
          map.setView(target, 16, { animate: true });

          // Add marker
          const marker = L.marker(target).addTo(map);
          marker.bindPopup(`<b>${display_name}</b>`).openPopup();
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

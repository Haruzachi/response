<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCprotektado - Emergency Response System</title>
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    * { box-sizing: border-box; }

    html, body {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
      background: #000;
      display: flex;
      overflow: hidden;
    }

    /* LEFT SIDE — MAP */
    #map {
      flex: 1;
      height: 100%;
      width: 50%;
      border-right: 2px solid #333;
    }

    /* RIGHT SIDE — SIDEBAR */
    #sidebar {
      width: 400px;
      background: #1a1a1a;
      color: #fff;
      display: flex;
      flex-direction: column;
      padding: 20px;
      overflow-y: auto;
    }

    #sidebar h2 {
      color: #00c8ff;
      margin-top: 0;
      border-bottom: 2px solid #00c8ff;
      padding-bottom: 8px;
    }

    #sidebar .info-box {
      background: #222;
      border: 1px solid #333;
      border-radius: 10px;
      padding: 15px;
      margin-top: 15px;
    }

    #sidebar .info-box h3 {
      margin: 0 0 10px;
      color: #ffcc00;
    }

    #sidebar .info-box p {
      margin: 5px 0;
      font-size: 14px;
    }

    #sidebar .search-bar {
      display: flex;
      gap: 10px;
      margin-bottom: 15px;
    }

    #sidebar input[type="text"] {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 5px;
      outline: none;
    }

    #sidebar button {
      background: #00c8ff;
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      padding: 10px 15px;
      cursor: pointer;
    }

    #sidebar button:hover {
      background: #00a6d6;
    }
  </style>
</head>
<body>
<div id="map"></div>

<script>
  // Define the Philippines bounds
  const philippinesBounds = L.latLngBounds(
    [4.2158, 116.1474],  // Southwest
    [21.3210, 126.8070]  // Northeast
  );

  // Initialize map centered on the Philippines
  const map = L.map('map', {
    zoomAnimation: true,     // enable default smooth zoom
    fadeAnimation: true,     // enable fade effect
    maxBounds: philippinesBounds,
    maxBoundsViscosity: 1.0
  }).setView([12.8797, 121.7740], 6); // Center of PH

  // Add OpenStreetMap layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    minZoom: 5,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Read search query
  const params = new URLSearchParams(window.location.search);
  const q = params.get('location');

  if (!q) {
    console.log("No search term provided.");
  } else {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(q)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          const { lat, lon, display_name } = data[0];
          const target = L.latLng(lat, lon);

          // Regular zoom animation (not fly, not instant)
          map.setView(target, 16, { animate: true });

          // Add marker right after zoom
          const marker = L.marker(target).addTo(map);
          marker.bindPopup(`<b>${display_name}</b>`).openPopup();
        } else {
          alert("No matching location found in the Philippines for: " + q);
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

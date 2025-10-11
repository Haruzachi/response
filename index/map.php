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

  <!-- MAP SECTION -->
  <div id="map"></div>

  <!-- SIDEBAR SECTION -->
  <div id="sidebar">
    <h2>QCProtektado</h2>

    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search location...">
      <button onclick="searchLocation()">Search</button>
    </div>

    <div id="locationInfo" class="info-box">
      <h3>Location Info</h3>
      <p><strong>Location:</strong> <span id="locName">None</span></p>
      <p><strong>Status:</strong> <span id="status">Awaiting search...</span></p>
    </div>

    <div class="info-box">
      <h3>Hazard Levels</h3>
      <p>Flood: <span style="color:#0f0;">Low</span></p>
      <p>Landslide: <span style="color:#0f0;">Little to none</span></p>
      <p>Storm Surge: <span style="color:#0f0;">Little to none</span></p>
    </div>

    <p style="font-size:12px; color:#999; margin-top:auto;">© QCProtektado System 2025</p>
  </div>

  <script>
    // Define PH bounds
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

    // Tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      minZoom: 5,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let currentMarker;

    // Function to search location
    function searchLocation() {
      const location = document.getElementById('searchInput').value.trim();
      if (!location) return alert("Please enter a location.");

      fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(data => {
          if (data.length > 0) {
            const { lat, lon, display_name } = data[0];
            const target = L.latLng(lat, lon);

            // Move map and marker
            map.setView(target, 16, { animate: true });
            if (currentMarker) map.removeLayer(currentMarker);

            currentMarker = L.marker(target).addTo(map);
            currentMarker.bindPopup(`<b>${display_name}</b>`).openPopup();

            // Update sidebar
            document.getElementById('locName').innerText = display_name;
            document.getElementById('status').innerText = "Location found on map.";
          } else {
            alert("No matching location found in the Philippines.");
            document.getElementById('status').innerText = "No matching location.";
          }
        })
        .catch(err => {
          console.error(err);
          alert("Error fetching location data.");
          document.getElementById('status').innerText = "Error fetching data.";
        });
    }

    // Auto-search from URL parameter (optional)
    const params = new URLSearchParams(window.location.search);
    const q = params.get('location');
    if (q) {
      document.getElementById('searchInput').value = q;
      searchLocation();
    }
  </script>
</body>
</html>

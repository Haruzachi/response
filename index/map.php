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
      color: #fff;
      display: flex;
      flex-direction: row-reverse; /* ✅ Sidebar now on the right */
      overflow: hidden;
    }

    /* MAP */
    #map {
      flex: 1;
      height: 100%;
      width: 100%;
    }

    /* SIDEBAR */
    #sidebar {
      width: 350px;
      background: rgba(20, 20, 20, 0.95);
      border-left: 2px solid #00c8ff;
      padding: 20px;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
    }

    #sidebar h2 {
      color: #00c8ff;
      text-align: center;
      margin-bottom: 20px;
    }

    .info-box {
      background: #1a1a1a;
      border: 1px solid #333;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .info-box h3 {
      color: #ffcc00;
      margin: 0 0 10px;
    }

    .info-box p {
      margin: 6px 0;
      font-size: 14px;
      line-height: 1.4;
    }

    #search-bar {
      display: flex;
      gap: 8px;
      margin-bottom: 15px;
    }

    #search-input {
      flex: 1;
      padding: 8px;
      border: none;
      border-radius: 5px;
      outline: none;
    }

    #search-btn {
      background: #00c8ff;
      color: #000;
      font-weight: bold;
      border: none;
      border-radius: 5px;
      padding: 8px 12px;
      cursor: pointer;
    }

    #search-btn:hover {
      background: #00a6d6;
    }

    footer {
      font-size: 12px;
      color: #888;
      text-align: center;
      margin-top: auto;
      padding-top: 10px;
      border-top: 1px solid #333;
    }
  </style>
</head>
<body>

  <!-- MAP -->
  <div id="map"></div>

  <!-- SIDEBAR -->
  <div id="sidebar">
    <h2>QCProtektado</h2>

    <div id="search-bar">
      <input type="text" id="search-input" placeholder="Search location...">
      <button id="search-btn">Search</button>
    </div>

    <div class="info-box">
      <h3>Location Information</h3>
      <p><b>Location:</b> <span id="locName">No location yet</span></p>
      <p><b>Status:</b> <span id="status">Waiting for input...</span></p>
    </div>

    <div class="info-box">
      <h3>Hazard Overview</h3>
      <p>Flood Risk: <span style="color:#0f0;">Low</span></p>
      <p>Landslide Risk: <span style="color:#0f0;">Low</span></p>
      <p>Storm Surge: <span style="color:#0f0;">None</span></p>
    </div>

    <footer>© QCProtektado 2025</footer>
  </div>

  <script>
    // Define the Philippines bounds
    const philippinesBounds = L.latLngBounds(
      [4.2158, 116.1474],
      [21.3210, 126.8070]
    );

    // Initialize map centered on the Philippines
    const map = L.map('map', {
      zoomAnimation: true,
      fadeAnimation: true,
      maxBounds: philippinesBounds,
      maxBoundsViscosity: 1.0
    }).setView([12.8797, 121.7740], 6);

    // Add OpenStreetMap layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      minZoom: 5,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    // Function to search location
    function searchLocation(location) {
      if (!location) return alert("Please enter a location.");

      fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(data => {
          if (data.length > 0) {
            const { lat, lon, display_name } = data[0];
            const target = L.latLng(lat, lon);

            map.setView(target, 16, { animate: true });

            if (marker) map.removeLayer(marker);
            marker = L.marker(target).addTo(map);
            marker.bindPopup(`<b>${display_name}</b>`).openPopup();

            document.getElementById('locName').innerText = display_name;
            document.getElementById('status').innerText = "Location found and marked.";
          } else {
            alert("No matching location found in the Philippines for: " + location);
            document.getElementById('status').innerText = "No matching location found.";
          }
        })
        .catch(err => {
          console.error(err);
          alert("Error fetching location data.");
          document.getElementById('status').innerText = "Error fetching location data.";
        });
    }

    // Click search
    document.getElementById('search-btn').addEventListener('click', () => {
      const location = document.getElementById('search-input').value.trim();
      searchLocation(location);
    });

    // Auto-load search if URL parameter exists
    const params = new URLSearchParams(window.location.search);
    const q = params.get('location');
    if (q) {
      document.getElementById('search-input').value = q;
      searchLocation(q);
    }
  </script>
</body>
</html>

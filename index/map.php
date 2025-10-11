<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCprotektado - Emergency Response System</title>
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      overflow: hidden;
      background: #000;
    }

    #map {
      height: 100%;
      width: 100%;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 1;
    }

    /* Right Sidebar */
    #sidebar {
      position: absolute;
      top: 0;
      right: 0; /* RIGHT SIDE */
      width: 280px;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 20px;
      box-shadow: -3px 0 10px rgba(0, 0, 0, 0.5);
      z-index: 1000;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    #sidebar h2 {
      margin: 0 0 15px 0;
      font-size: 20px;
      color: #00c3ff;
      text-align: center;
    }

    #sidebar input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: none;
      border-radius: 5px;
      background: #222;
      color: #fff;
    }

    #sidebar button {
      padding: 10px;
      background: #00c3ff;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }

    #sidebar button:hover {
      background: #009edb;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <div id="sidebar">
    <div>
      <h2>Search Location</h2>
      <input type="text" id="searchBox" placeholder="Enter location..." />
      <button onclick="searchLocation()">Find</button>
    </div>
    <div style="text-align:center; font-size:12px; opacity:0.7;">
      QCProtektado © 2025
    </div>
  </div>

  <script>
    const philippinesBounds = L.latLngBounds(
      [4.2158, 116.1474],
      [21.3210, 126.8070]
    );

    const map = L.map('map', {
      zoomAnimation: true,
      fadeAnimation: true,
      maxBounds: philippinesBounds,
      maxBoundsViscosity: 1.0
    }).setView([12.8797, 121.7740], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      minZoom: 5,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    function searchLocation() {
      const q = document.getElementById('searchBox').value.trim();
      if (!q) {
        alert("Please enter a location.");
        return;
      }

      fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
          if (data.length > 0) {
            const { lat, lon, display_name } = data[0];
            const target = L.latLng(lat, lon);
            map.setView(target, 16, { animate: true });
            L.marker(target).addTo(map).bindPopup(`<b>${display_name}</b>`).openPopup();
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

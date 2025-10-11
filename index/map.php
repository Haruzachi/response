<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCProtektado - Hazard Assessment Map</title>
  <link rel="icon" type="image/x-icon" href="../img/Logocircle.png">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body { height: 100%; margin: 0; background: #000; font-family: Arial, sans-serif; }
    #map { height: 100%; width: 100%; }

    /* Sidebar Styling (Right) */
    #sidebar {
      position: absolute;
      top: 0;
      right: 0;
      width: 420px;
      height: 100%;
      background: rgba(0,0,0,0.88);
      color: #fff;
      z-index: 999;
      padding: 20px 25px;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      box-shadow: -3px 0 10px rgba(0,0,0,0.5);
    }

    #sidebar h1 {
      font-size: 18px;
      margin: 0 0 10px;
      text-align: center;
      color: #00c3ff;
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
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    #sidebar button:hover { background: #009edb; }

    .hazard-box {
      background: #111;
      padding: 10px;
      border-radius: 8px;
      margin-top: 15px;
    }

    .hazard-box h3 {
      margin: 0;
      font-size: 15px;
      color: #00c3ff;
    }

    .hazard-box p {
      margin: 5px 0 0;
      font-size: 13px;
    }

    .low { color: #1eff00; }
    .medium { color: #ffb300; }
    .high { color: #ff4242; }

    #footer {
      margin-top: auto;
      text-align: center;
      font-size: 12px;
      opacity: 0.6;
      padding-top: 15px;
    }
  </style>
</head>
<body>

<div id="map"></div>

<!-- Sidebar -->
<div id="sidebar">
  <div>
    <h1>QCProtektado Hazard Map</h1>
    <input type="text" id="searchBox" placeholder="Search location...">
    <button onclick="manualSearch()">Search</button>

    <p style="font-size:12px;opacity:0.7;margin-top:8px;">💡 Tip: Drag the pin to update hazard level in that area.</p>

    <div class="hazard-box">
      <h3>Flood Hazard Level</h3>
      <p id="floodLevel" class="low">LOW</p>
    </div>

    <div class="hazard-box">
      <h3>Landslide Hazard Level</h3>
      <p id="landslideLevel" class="low">LITTLE TO NONE</p>
    </div>

    <div class="hazard-box">
      <h3>Storm Surge Hazard Level</h3>
      <p id="stormLevel" class="low">LITTLE TO NONE</p>
    </div>
  </div>

  <div id="footer">
    QCProtektado © 2025<br>
    Simulated Hazard Map Prototype
  </div>
</div>

<script>
  // Map bounds limited to Philippines
  const phBounds = L.latLngBounds([4.2158, 116.1474], [21.3210, 126.8070]);

  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true,
    maxBounds: phBounds,
    maxBoundsViscosity: 1.0
  }).setView([12.8797, 121.7740], 6);

  // Base Map Layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    minZoom: 5,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Hazard level colors (demo overlay)
  const floodOverlay = L.tileLayer('https://tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {opacity: 0.3}).addTo(map);

  // Marker
  let marker = L.marker([12.8797, 121.7740], { draggable: true }).addTo(map);
  marker.bindPopup("Drag me to check hazard levels.").openPopup();

  // Random hazard generator (for demo)
  function randomHazard() {
    const levels = ["LOW", "MEDIUM", "HIGH"];
    return levels[Math.floor(Math.random() * levels.length)];
  }

  function updateHazardLevels() {
    const flood = randomHazard();
    const land = randomHazard();
    const storm = randomHazard();

    setHazard("floodLevel", flood);
    setHazard("landslideLevel", land);
    setHazard("stormLevel", storm);
  }

  function setHazard(id, level) {
    const el = document.getElementById(id);
    el.textContent = level;
    el.className = level === "HIGH" ? "high" : level === "MEDIUM" ? "medium" : "low";
  }

  // When marker is dragged
  marker.on('dragend', function(e) {
    const pos = e.target.getLatLng();
    map.panTo(pos);
    updateHazardLevels();
  });

  // Handle manual search
  function manualSearch() {
    const q = document.getElementById('searchBox').value.trim();
    if (!q) return alert("Please enter a location.");

    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(q)}`)
      .then(r => r.json())
      .then(data => {
        if (data.length === 0) return alert("No result found in the Philippines.");
        const { lat, lon, display_name } = data[0];
        const loc = [parseFloat(lat), parseFloat(lon)];
        map.flyTo(loc, 15, { animate: true, duration: 1.5 });
        marker.setLatLng(loc).bindPopup(`<b>${display_name}</b>`).openPopup();
        updateHazardLevels();
      })
      .catch(() => alert("Error fetching location."));
  }

  // Initial hazard data
  updateHazardLevels();
</script>

</body>
</html>

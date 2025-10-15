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

    /* Map Logo Button */
    .map-logo {
      position: absolute;
      top: 15px;
      left: 15px;
      z-index: 1000;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      cursor: pointer;
      padding: 5px 8px;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .map-logo img {
      width: 26px;
      height: 26px;
    }

    /* Map style menu */
    .map-style-menu {
      position: absolute;
      top: 55px;
      left: 15px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      padding: 8px;
      display: none;
      z-index: 1000;
    }

    .map-style-menu button {
      display: block;
      width: 120px;
      padding: 8px;
      margin-bottom: 5px;
      border: none;
      border-radius: 5px;
      background: #0077ff;
      color: white;
      cursor: pointer;
    }

    .map-style-menu button:hover {
      background: #005fcc;
    }

    /* Compass */
    .compass {
      position: absolute;
      bottom: 20px;
      left: 20px;
      width: 60px;
      height: 60px;
      background: white;
      border-radius: 50%;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: grab;
      transition: transform 0.3s ease;
    }

    .compass::after {
      content: 'N';
      position: absolute;
      top: 6px;
      font-weight: bold;
      color: red;
    }
  </style>
</head>
<body>
<div id="map"></div>

<!-- Map logo -->
<div class="map-logo" onclick="toggleMapMenu()">
  <img src="../img/Logocircle.png" alt="Map Logo">
  <span>Map Style</span>
</div>

<!-- Map style menu -->
<div class="map-style-menu" id="mapMenu">
  <button onclick="setBase('terrain')">Terrain</button>
  <button onclick="setBase('satellite')">Satellite</button>
</div>

<!-- Compass -->
<div class="compass" id="compass"></div>

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
  const philippinesBounds = L.latLngBounds([4.2158, 116.1474], [21.3210, 126.8070]);
  const map = L.map('map', { zoomAnimation: true, fadeAnimation: true, maxBounds: philippinesBounds, maxBoundsViscosity: 1.0 })
    .setView([12.8797, 121.7740], 6);

  // Default layer
  let currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, minZoom: 5, attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  let terrainLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png');
  let satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}');

  let currentMarker = null;

  function toggleMapMenu() {
    const menu = document.getElementById('mapMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  }

  function setBase(type) {
    map.removeLayer(currentLayer);
    if (type === 'terrain') currentLayer = terrainLayer;
    else if (type === 'satellite') currentLayer = satelliteLayer;
    else currentLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
    map.addLayer(currentLayer);
    document.getElementById('mapMenu').style.display = 'none';
  }

  const params = new URLSearchParams(window.location.search);
  const q = params.get('location');
  if (q) {
    fetchLocation(q);
    document.getElementById('searchBox').value = q;
  }

  function manualSearch() {
    const input = document.getElementById('searchBox').value.trim();
    if (input) fetchLocation(input);
    else alert("Please enter a location.");
  }

  function fetchLocation(query) {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          const { lat, lon } = data[0];
          const target = L.latLng(lat, lon);
          if (currentMarker) map.removeLayer(currentMarker);
          currentMarker = L.marker(target).addTo(map);
          map.setView(target, 16, { animate: true });
        } else alert("No matching location found in the Philippines for: " + query);
      })
      .catch(err => {
        console.error(err);
        alert("Error fetching location data.");
      });
  }

  // Compass rotation
  const compass = document.getElementById('compass');
  let angle = 0;
  let rotating = false;
  let startX = 0;

  compass.addEventListener('mousedown', e => {
    rotating = true;
    startX = e.clientX;
    compass.style.cursor = 'grabbing';
  });

  document.addEventListener('mouseup', () => {
    rotating = false;
    compass.style.cursor = 'grab';
  });

  document.addEventListener('mousemove', e => {
    if (rotating) {
      const delta = e.clientX - startX;
      angle += delta * 0.5;
      startX = e.clientX;
      map.rotate?.(angle);
      compass.style.transform = `rotate(${angle}deg)`;
    }
  });

  // Polyfill: If rotate not available, simulate via CSS
  if (!map.rotate) {
    let mapContainer = document.querySelector('#map');
    map.rotate = (deg) => {
      mapContainer.style.transform = `rotate(${deg}deg)`;
    };
  }
</script>
</body>
</html>

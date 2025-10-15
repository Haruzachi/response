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
      cursor: pointer;
      transition: background 0.3s;
    }

    .hazard-box:hover {
      background: #eef6ff;
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

    /* Modal */
    .modal {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 2000;
    }

    .modal-content {
      background: #fff;
      width: 400px;
      max-height: 80vh;
      overflow-y: auto;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.3);
    }

    .modal-content h2 {
      color: #0077ff;
      margin-top: 0;
    }

    .modal-content h3 {
      color: #333;
      margin-top: 15px;
    }

    .modal-content p, .modal-content ul {
      color: #444;
      font-size: 14px;
      line-height: 1.6;
    }

    .close-btn {
      background: #0077ff;
      color: white;
      border: none;
      border-radius: 6px;
      padding: 8px 14px;
      margin-top: 15px;
      cursor: pointer;
      font-size: 14px;
    }

    .close-btn:hover {
      background: #005fcc;
    }

    /* Map logo button */
    .map-logo {
      position: absolute;
      top: 12px;
      left: 50px;
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

    /* Map style dropdown menu */
    .map-style-menu {
      position: absolute;
      top: 55px;
      left: 85px;
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
      font-size: 13px;
    }

    .map-style-menu button:hover {
      background: #005fcc;
    }
  </style>
</head>
<body>
<div id="map"></div>

<!-- Map Logo Button -->
<div class="map-logo" onclick="toggleMapMenu()">
  <img src="../img/Logocircle.png" alt="Map Logo">
</div>

<!-- Map Style Menu -->
<div class="map-style-menu" id="mapMenu">
  <button onclick="setBase('default')">Default</button>
  <button onclick="setBase('satellite')">Satellite</button>
  <button onclick="setBase('terrain')">Terrain</button>
</div>

<!-- Sidebar -->
<div id="sidebar">
  <div>
    <h2>Search Location</h2>
    <input type="text" id="searchBox" placeholder="Search location...">
    <button onclick="manualSearch()">Find</button>

    <div class="info-section">
      <h3>Hazard Levels In Your Area</h3>
      <div class="hazard-box" onclick="openModal('flood')">
        <span>Flood Hazard Level</span>
        <i>LOW</i>
      </div>
      <div class="hazard-box" onclick="openModal('landslide')">
        <span>Landslide Hazard Level</span>
        <i>LOW</i>
      </div>
      <div class="hazard-box" onclick="openModal('storm')">
        <span>Storm Surge Hazard Level</span>
        <i>LOW</i>
      </div>
    </div>
  </div>
  <div id="footer">QCProtektado © 2025</div>
</div>

<!-- Hazard Modal -->
<div id="hazardModal" class="modal">
  <div class="modal-content" id="modalContent">
    <h2>Hazard Guide</h2>
    <p>Loading...</p>
    <button class="close-btn" onclick="closeModal()">Close</button>
  </div>
</div>

<script>
  const philippinesBounds = L.latLngBounds([4.2158, 116.1474], [21.3210, 126.8070]);

  // Map initialization
  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true,
    maxBounds: philippinesBounds,
    maxBoundsViscosity: 1.0
  }).setView([12.8797, 121.7740], 6);

  // Base layers
  const defaultLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19, attribution: '&copy; OpenStreetMap contributors'
  });

  const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
    maxZoom: 19, attribution: 'Tiles &copy; Esri'
  });

  const terrainLayer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    maxZoom: 17, attribution: '&copy; OpenTopoMap contributors'
  });

  let currentBase = defaultLayer.addTo(map);

  // Switch base maps
  function setBase(type) {
    map.removeLayer(currentBase);
    if (type === 'satellite') currentBase = satelliteLayer.addTo(map);
    else if (type === 'terrain') currentBase = terrainLayer.addTo(map);
    else currentBase = defaultLayer.addTo(map);
    document.getElementById('mapMenu').style.display = 'none';
  }

  // Toggle map style menu
  function toggleMapMenu() {
    const menu = document.getElementById('mapMenu');
    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
  }

  // Marker handler
  let currentMarker = null;

  // URL search query
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
        } else {
          alert("No matching location found in the Philippines for: " + query);
        }
      })
      .catch(err => {
        console.error(err);
        alert("Error fetching location data.");
      });
  }

  // Modal handler
  function openModal(type) {
    const modal = document.getElementById('hazardModal');
    const content = document.getElementById('modalContent');
    let html = '';

    if (type === 'flood') {
      html = `
        <h2>Know Your Hazard: Flooding</h2>
        <p><strong>Flooding</strong> is the overflow of water from rivers or seas due to heavy rainfall.</p>
        <h3>What To Do During Flood</h3>
        <ul>
          <li>Prepare to evacuate when alerts are issued.</li>
          <li>Don’t walk or drive through floodwater.</li>
          <li>Turn off power and LPG tanks if flooding occurs.</li>
        </ul>`;
    } else if (type === 'landslide') {
      html = `
        <h2>Know Your Hazard: Landslide</h2>
        <p><strong>Landslides</strong> involve soil and rock moving downhill due to rain or earthquakes.</p>
        <h3>What To Do During Landslide</h3>
        <ul>
          <li>Move away from steep slopes and cliffs.</li>
          <li>Listen for unusual rumbling sounds.</li>
          <li>Evacuate quickly during heavy rains.</li>
        </ul>`;
    } else if (type === 'storm') {
      html = `
        <h2>Know Your Hazard: Storm Surge</h2>
        <p><strong>Storm surges</strong> are abnormal sea rises caused by strong winds and low pressure.</p>
        <h3>What To Do During Storm Surge</h3>
        <ul>
          <li>Evacuate early if advised.</li>
          <li>Move to higher ground away from the coast.</li>
          <li>Disconnect appliances and wait until declared safe.</li>
        </ul>`;
    }

    content.innerHTML = html + '<button class="close-btn" onclick="closeModal()">Close</button>';
    modal.style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('hazardModal').style.display = 'none';
  }
</script>
</body>
</html>

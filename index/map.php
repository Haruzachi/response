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

    #map { z-index: 1; }

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
      background-color: #fafafa;
      outline: none;
      transition: all 0.3s ease;
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

    /* MODAL */
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
      background: white;
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
      margin-top: 15px;
      color: #333;
    }

    .modal-content p {
      color: #444;
      line-height: 1.5;
      font-size: 14px;
    }

    .close-btn {
      background: #0077ff;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 8px 14px;
      margin-top: 15px;
      cursor: pointer;
      font-size: 14px;
    }

    .close-btn:hover {
      background: #005fcc;
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

<!-- MODAL -->
<div id="hazardModal" class="modal">
  <div class="modal-content" id="modalContent">
    <h2>Hazard Guide</h2>
    <p>Loading...</p>
    <button class="close-btn" onclick="closeModal()">Close</button>
  </div>
</div>

<script>
  // Initialize map
  const map = L.map('map').setView([12.8797, 121.7740], 6);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  let currentMarker = null;

  function manualSearch() {
    const input = document.getElementById('searchBox').value.trim();
    if (!input) return alert("Please enter a location.");
    fetchLocation(input);
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
          alert("No matching location found.");
        }
      })
      .catch(err => alert("Error fetching location."));
  }

  // MODAL HANDLERS
  function openModal(type) {
    const modal = document.getElementById('hazardModal');
    const content = document.getElementById('modalContent');

    let html = "";

    if (type === "flood") {
      html = `
        <h2>Know Your Hazard: Flooding</h2>
        <p><strong>Flooding</strong> is the overflow of water from a river or another body of water due to heavy rainfall.</p>
        <h3>What To Do During Flood</h3>
        <ul>
          <li>Be prepared to evacuate immediately when there’s an alert for heavy rainfall.</li>
          <li>Refrain from walking through floodwater, especially without protective gear like boots.</li>
          <li>Don’t walk or drive through moving water—it can sweep you and your car away.</li>
          <li>Turn off all electrical appliances and LPG tanks; turn off the main power switch as needed.</li>
        </ul>
      `;
    } else if (type === "landslide") {
      html = `
        <h2>Know Your Hazard: Landslide</h2>
        <p><strong>Landslide</strong> is the downward movement of rock, soil, or debris caused by rain, earthquakes, or human activity.</p>
        <h3>What To Do During Landslide</h3>
        <ul>
          <li>Move away from steep slopes, cliffs, or areas prone to rockfall.</li>
          <li>Stay alert for unusual sounds like cracking or rumbling.</li>
          <li>Be ready to evacuate quickly when heavy rain persists.</li>
          <li>Stay indoors but avoid the side of the house facing the slope.</li>
        </ul>
      `;
    } else if (type === "storm") {
      html = `
        <h2>Know Your Hazard: Storm Surge</h2>
        <p><strong>Storm Surge</strong> is an abnormal rise in sea level caused by a storm’s strong winds and low pressure.</p>
        <h3>What To Do During Storm Surge</h3>
        <ul>
          <li>Evacuate early if authorities advise.</li>
          <li>Move to higher ground away from coastal areas.</li>
          <li>Secure your home and disconnect electrical appliances.</li>
          <li>Do not return until officials declare it safe.</li>
        </ul>
      `;
    }

    content.innerHTML = html + '<button class="close-btn" onclick="closeModal()">Close</button>';
    modal.style.display = "flex";
  }

  function closeModal() {
    document.getElementById('hazardModal').style.display = "none";
  }
</script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Leaflet Test</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body, #map { height:100%; margin:0; }
  </style>
</head>
<body>
  <div id="map"></div>
  <script>
    const map = L.map('map').setView([14.6,120.98],12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      {maxZoom:19}).addTo(map);
  </script>
</body>
</html>
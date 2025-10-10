<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Map Search</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body, #map { height: 100%; margin: 0; }
  </style>
</head>
<body>
<div id="map"></div>

<script>
  // Initialize map
  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true
  }).setView([10, 10], 2);

  // Add OpenStreetMap tiles
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // Get query parameter
  const params = new URLSearchParams(window.location.search);
  const q = params.get('location');

  if (!q) {
    alert("No search term.");
  } else {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`)
      .then(r => r.json())
      .then(data => {
        if (data.length) {
          const { lat, lon, display_name } = data[0];

          // Smooth zoom: first pan to location, then zoom in
          const targetLatLng = L.latLng(lat, lon);

          map.flyTo(targetLatLng, 17, {
            animate: true,
            duration: 2 // duration in seconds
          });

          // Add marker after a short delay to sync with zoom animation
          setTimeout(() => {
            L.marker(targetLatLng)
              .addTo(map)
              .bindPopup(`<b>${display_name}</b>`)
              .openPopup();
          }, 1500);
        } else {
          alert("Not found: " + q);
        }
      })
      .catch(e => alert("Fetch error: " + e));
  }
</script>
</body>
</html>

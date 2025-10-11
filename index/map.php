<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Map Search (Philippines)</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body, #map { height: 100%; margin: 0; }
    body { background: #000; }
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

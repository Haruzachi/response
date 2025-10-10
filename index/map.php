<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Map Search (Philippines)</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body, #map { height: 100%; margin: 0; }
  </style>
</head>
<body>
<div id="map"></div>

<script>
  // Define Philippines bounds (southwest and northeast corners)
  const philippinesBounds = L.latLngBounds(
    [4.2158, 116.1474], // Southwest
    [21.3210, 126.8070] // Northeast
  );

  // Initialize the map with bounds
  const map = L.map('map', {
    zoomAnimation: true,
    fadeAnimation: true,
    maxBounds: philippinesBounds, // restrict view to PH
    maxBoundsViscosity: 1.0, // prevent moving out of bounds
  }).setView([12.8797, 121.7740], 6); // Center of the Philippines

  // Add OpenStreetMap layer
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  // Get search term from URL
  const params = new URLSearchParams(window.location.search);
  const q = params.get('location');

  if (!q) {
    alert("No search term provided.");
  } else {
    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(q)}`)
      .then(r => r.json())
      .then(data => {
        if (data.length) {
          const { lat, lon, display_name } = data[0];
          const targetLatLng = L.latLng(lat, lon);

          // Smooth zoom to location
          map.flyTo(targetLatLng, 17, {
            animate: true,
            duration: 2
          });

          // Add marker after zoom animation
          setTimeout(() => {
            L.marker(targetLatLng)
              .addTo(map)
              .bindPopup(`<b>${display_name}</b>`)
              .openPopup();
          }, 1500);
        } else {
          alert("No matching location found in the Philippines for: " + q);
        }
      })
      .catch(e => alert("Error fetching location data: " + e));
  }
</script>
</body>
</html>

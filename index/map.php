<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Map Search Result</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html, body {
      height: 100%;
      margin: 0;
      background: #000;
    }
    #map {
      height: 100%;
      width: 100%;
    }
    .leaflet-popup-content {
      color: #000;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <script>
    // Initialize the map centered around Manila by default
    var map = L.map('map').setView([14.5995, 120.9842], 12);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Get the "location" parameter from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const location = urlParams.get('location');

    // If a location was searched from dashboard.php
    if (location) {
      // Use OpenStreetMap Nominatim to find the coordinates
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(data => {
          if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lon = parseFloat(data[0].lon);
            const name = data[0].display_name;

            // Center and zoom to the location
            map.setView([lat, lon], 17);

            // Add marker
            const marker = L.marker([lat, lon]).addTo(map);
            marker.bindPopup(`<b>${name}</b>`).openPopup();
          } else {
            alert("Location not found!");
          }
        })
        .catch(err => {
          console.error("Error fetching location:", err);
          alert("Error fetching location details.");
        });
    }
  </script>
</body>
</html>
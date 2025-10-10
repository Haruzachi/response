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
    // Initialize the map (start neutral)
    var map = L.map('map').setView([0, 0], 2);

    // Add OpenStreetMap layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Get the searched location from the URL
    const urlParams = new URLSearchParams(window.location.search);
    const location = urlParams.get('location');

    // If there’s a search query (from dashboard.php)
    if (location) {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(data => {
          if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lon = parseFloat(data[0].lon);
            const name = data[0].display_name;

            // Move the map to the searched location and zoom in
            map.setView([lat, lon], 17);

            // Add a marker to the exact location
            const marker = L.marker([lat, lon]).addTo(map);
            marker.bindPopup(`<b>${name}</b>`).openPopup();
          } else {
            alert("No matching location found for: " + location);
          }
        })
        .catch(err => {
          console.error("Error fetching location:", err);
          alert("Unable to fetch location data.");
        });
    } else {
      alert("No location provided in the search.");
    }
  </script>
</body>
</html>
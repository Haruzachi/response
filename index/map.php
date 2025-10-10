<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Map View</title>
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
  </style>
</head>
<body>
  <div id="map"></div>

  <script>
    // Initialize map
    var map = L.map('map').setView([14.5995, 120.9842], 12); // Default: Manila

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(map);

    // Get location from URL (e.g., ?location=Evergreen+Executive+Village)
    const urlParams = new URLSearchParams(window.location.search);
    const location = urlParams.get('location');

    if (location) {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(data => {
          if (data && data.length > 0) {
            const lat = data[0].lat;
            const lon = data[0].lon;
            const name = data[0].display_name;

            map.setView([lat, lon], 17); // zoom to location

            L.marker([lat, lon]).addTo(map)
              .bindPopup(`<b>${name}</b>`)
              .openPopup();
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
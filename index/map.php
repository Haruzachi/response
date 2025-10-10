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
    // Initialize map with neutral world view
    var map = L.map('map', {
      worldCopyJump: true
    }).setView([0, 0], 2);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Get the search query
    const urlParams = new URLSearchParams(window.location.search);
    const location = urlParams.get('location');

    if (location) {
      // Add small delay to make sure map container is ready
      setTimeout(() => {
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`, {
          headers: {
            'User-Agent': 'LeafletSearchDemo/1.0 (your-email@example.com)' // required by Nominatim
          }
        })
          .then(res => res.json())
          .then(data => {
            if (data && data.length > 0) {
              const lat = parseFloat(data[0].lat);
              const lon = parseFloat(data[0].lon);
              const name = data[0].display_name;

              // Ensure map refreshes properly before moving
              map.invalidateSize();
              map.setView([lat, lon], 17);

              // Add marker with popup
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
      }, 500);
    } else {
      alert("No location provided in the search.");
    }
  </script>
</body>
</html>
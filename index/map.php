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
    var map = L.map('map').setView([0, 0], 2);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Get search query from dashboard.php (e.g., ?location=Evergreen+Executive+Village)
    const urlParams = new URLSearchParams(window.location.search);
    const location = urlParams.get('location');

    // Only run if there's a search query
    if (location) {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(location)}`, {
        headers: {
          'User-Agent': 'LeafletSearchDemo/1.0 (your-email@example.com)' // required by Nominatim
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data && data.length > 0) {
          const lat = parseFloat(data[0].lat);
          const lon = parseFloat(data[0].lon);
          const name = data[0].display_name;

          // Zoom to location
          map.setView([lat, lon], 17);

          // Add marker and popup
          const marker = L.marker([lat, lon]).addTo(map);
          marker.bindPopup(`<b>${name}</b>`).openPopup();
        } else {
          alert("Location not found: " + location);
        }
      })
      .catch(error => {
        console.error("Error fetching location:", error);
        alert("There was a problem finding that location.");
      });
    } else {
      alert("No location provided in the search.");
    }
  </script>
</body>
</html>
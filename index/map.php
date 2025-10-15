<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QCprotektado - Emergency Response System</title>
  <link rel="icon" type="../image/x-icon" href="../img/Logocircle.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Mapbox GL -->
  <link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">
  <script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>

  <style>
    html, body { height: 100%; margin: 0; font-family: 'Segoe UI', sans-serif; }
    body { background: #f3f4f6; overflow: hidden; }

    #map {
      position: absolute;
      top: 0;
      left: 0;
      width: calc(100% - 340px);
      height: 100%;
    }

    /* Sidebar */
    #sidebar {
      position: absolute;
      top: 0;
      right: 0;
      width: 340px;
      height: 100%;
      background: #fff;
      color: #333;
      z-index: 10;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      box-shadow: -4px 0 15px rgba(0,0,0,0.15);
    }

    #sidebar h2 {
      font-size: 18px;
      font-weight: 600;
      color: #0077ff;
      text-align: left;
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
      outline: none;
      transition: all 0.3s ease;
      background-color: #fafafa;
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
  </style>
</head>
<body>
<div id="map"></div>

<div id="sidebar">
  <div>
    <h2>Search Location</h2>
    <input type="text" id="searchBox" placeholder="Search location...">
    <button onclick="manualSearch()">Find</button>

    <div class="info-section">
      <h3>Hazard Levels In Your Area</h3>
      <div class="hazard-box"><span>Flood Hazard Level</span><i>LOW</i></div>
      <div class="hazard-box"><span>Landslide Hazard Level</span><i>LOW</i></div>
      <div class="hazard-box"><span>Storm Surge Hazard Level</span><i>LOW</i></div>
    </div>
  </div>
  <div id="footer">QCProtektado © 2025</div>
</div>

<script>
  mapboxgl.accessToken = 'YOUR_MAPBOX_ACCESS_TOKEN'; // Replace with your Mapbox token

  const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/light-v11',
    center: [121.0583, 14.6760], // Example: Quezon City
    zoom: 14,
    pitch: 60,
    bearing: -17.6,
    antialias: true
  });

  // Add 3D buildings layer
  map.on('load', () => {
    // Terrain and sky
    map.addSource('mapbox-dem', {
      'type': 'raster-dem',
      'url': 'mapbox://mapbox.mapbox-terrain-dem-v1',
      'tileSize': 512,
      'maxzoom': 14
    });
    map.setTerrain({ 'source': 'mapbox-dem', 'exaggeration': 1.5 });

    map.addLayer({
      'id': 'sky',
      'type': 'sky',
      'paint': {
        'sky-type': 'atmosphere',
        'sky-atmosphere-sun': [0.0, 0.0],
        'sky-atmosphere-sun-intensity': 15
      }
    });

    // 3D building extrusion
    const layers = map.getStyle().layers;
    const labelLayerId = layers.find(
      (layer) => layer.type === 'symbol' && layer.layout['text-field']
    ).id;

    map.addLayer(
      {
        'id': '3d-buildings',
        'source': 'composite',
        'source-layer': 'building',
        'filter': ['==', 'extrude', 'true'],
        'type': 'fill-extrusion',
        'minzoom': 15,
        'paint': {
          'fill-extrusion-color': '#aaa',
          'fill-extrusion-height': [
            'interpolate', ['linear'], ['zoom'],
            15, 0,
            15.05, ['get', 'height']
          ],
          'fill-extrusion-base': ['get', 'min_height'],
          'fill-extrusion-opacity': 0.7
        }
      },
      labelLayerId
    );

    // Hazard simulation (heat layer)
    map.addSource('hazard-heat', {
      'type': 'geojson',
      'data': {
        'type': 'FeatureCollection',
        'features': [
          { 'type': 'Feature', 'geometry': { 'type': 'Point', 'coordinates': [121.057, 14.675] } },
          { 'type': 'Feature', 'geometry': { 'type': 'Point', 'coordinates': [121.06, 14.678] } },
          { 'type': 'Feature', 'geometry': { 'type': 'Point', 'coordinates': [121.054, 14.674] } }
        ]
      }
    });

    map.addLayer({
      'id': 'hazard-heat-layer',
      'type': 'heatmap',
      'source': 'hazard-heat',
      'maxzoom': 15,
      'paint': {
        'heatmap-weight': 1,
        'heatmap-intensity': 1,
        'heatmap-color': [
          'interpolate',
          ['linear'],
          ['heatmap-density'],
          0, 'rgba(33,102,172,0)',
          0.2, 'rgb(103,169,207)',
          0.4, 'rgb(209,229,240)',
          0.6, 'rgb(253,219,199)',
          0.8, 'rgb(239,138,98)',
          1, 'rgb(178,24,43)'
        ],
        'heatmap-radius': 25,
        'heatmap-opacity': 0.6
      }
    });
  });

  // Search feature (Nominatim)
  let marker = null;
  function manualSearch() {
    const q = document.getElementById('searchBox').value.trim();
    if (!q) return alert('Please enter a location.');

    fetch(`https://nominatim.openstreetmap.org/search?format=json&countrycodes=PH&q=${encodeURIComponent(q)}`)
      .then(res => res.json())
      .then(data => {
        if (data.length > 0) {
          const { lat, lon } = data[0];
          const coords = [parseFloat(lon), parseFloat(lat)];
          map.flyTo({ center: coords, zoom: 16 });

          if (marker) marker.remove();
          marker = new mapboxgl.Marker({ color: '#0077ff' })
            .setLngLat(coords)
            .addTo(map);
        } else {
          alert('No matching location found.');
        }
      })
      .catch(err => alert('Error fetching location.'));
  }
</script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Map Search</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <style>
    html,body,#map{height:100%;margin:0;}
  </style>
</head>
<body>
<div id="map"></div>

<script>
const map = L.map('map').setView([10,10],2);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
  {maxZoom:19, attribution:'&copy; OpenStreetMap'}).addTo(map);

const params = new URLSearchParams(window.location.search);
const q = params.get('location');
if(!q){ alert("No search term."); }

if(q){
  fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}`)
    .then(r=>r.json())
    .then(data=>{
      if(data.length){
        const {lat,lon,display_name} = data[0];
        map.setView([lat,lon],17);
        L.marker([lat,lon]).addTo(map).bindPopup(display_name).openPopup();
      } else alert("Not found: "+q);
    })
    .catch(e=>alert("Fetch error: "+e));
}
</script>
</body>
</html>
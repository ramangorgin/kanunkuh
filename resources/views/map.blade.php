<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>نقشه نشان با Leaflet</title>
    <link rel="stylesheet" href="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.css"/>
    <style>
        body {
            height: 100vh;
            width: 100vw;
            margin: 0;
        }
        #map {
            height: 50%;
            width: 50%;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <script src="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.js"></script>
    <script>
        const myMap = new L.Map("map", {
            key: "web.34d371d6df614e62afe2604d5ee25b1f",
            maptype: "neshan",
            poi: false,
            traffic: false,
            center: [35.699756, 51.338076],
            zoom: 14,
        });
    </script>
</body>
</html>
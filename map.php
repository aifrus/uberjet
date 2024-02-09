<!DOCTYPE html>
<html>

<head>
    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <script>
        function initMap() {
            const center = {
                lat: 28.4293889,
                lng: -81.3090000
            };
            const map = new google.maps.Map(document.getElementById("map"), {
                center: center,
                zoom: 10, // Adjust the zoom level as needed
            });
        }
    </script>
    <script src="js.php" async defer></script>
</body>

</html>
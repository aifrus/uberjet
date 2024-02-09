<?php
header('Content-Type: application/javascript');
echo file_get_contents("https://maps.googleapis.com/maps/api/js?key=" . getenv('GOOGLE_MAPS_API_KEY') . "&callback=initMap");

<?php
header('Content-Type: application/javascript');
?>
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
<?php
echo file_get_contents("https://maps.googleapis.com/maps/api/js?key=" . getenv('GOOGLE_MAPS_API_KEY') . "&callback=initMap");

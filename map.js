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
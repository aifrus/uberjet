let map;

async function initMap() {
    const { Map } = await google.maps.importLibrary("maps");

    map = new Map(document.getElementById("map"), {
        center: { lat: -34.397, lng: 150.644 },
        zoom: 8,
        disableDefaultUI: true,
        styles: [
            {
                featureType: 'all',
                elementType: 'labels',
                stylers: [{ visibility: 'off' }]
            },
            {
                featureType: 'transit.station.airport',
                elementType: 'labels',
                stylers: [{ visibility: 'on' }]
            },
            {
                featureType: 'road',
                stylers: [{ visibility: 'off' }]
            },
            {
                featureType: 'water',
                stylers: [{ color: '#000000' }] // black water
            },
            {
                featureType: 'landscape',
                stylers: [{ color: '#0c0c0c' }] // very dark gray land
            }
        ]
    });
}

initMap();
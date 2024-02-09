let map;

async function initMap() {
    const { Map } = await google.maps.importLibrary("maps");

    map = new Map(document.getElementById("map"), {
        center: { lat: 43.6456435, lng: -70.3086164 },
        zoom: 10,
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
                featureType: 'poi',
                stylers: [{ visibility: 'off' }]
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
                stylers: [{ color: '#111111' }] // very dark gray land
            }
        ]
    });
}

initMap();
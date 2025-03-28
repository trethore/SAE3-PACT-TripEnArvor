let allOffers = [];
let map;
let markerCluster;

function addMap() {
  const southWest = L.latLng(-85, -180);
  const northEast = L.latLng(85, 180);
  const bounds = L.latLngBounds(southWest, northEast);

  map = L.map('map', {
    minZoom: 5,
    maxBounds: bounds, 
    maxBoundsViscosity: 1.0 
  }).setView([48.52, -2.7], 8);

  markerCluster = L.markerClusterGroup({
    iconCreateFunction: function (cluster) {
      const count = cluster.getChildCount();
      let size = 'small';
      if (count >= 100) {
        size = 'large';
      } else if (count >= 10) {
        size = 'medium';
      }
      return L.divIcon({
        html: `<div class="cluster-icon ${size}"><span>${count}</span></div>`,
        className: 'custom-cluster',
        iconSize: L.point(40, 40)
      });
    }
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  map.addLayer(markerCluster);
}

function showOfferMap(lat, lon, categorie) {
  if (!lat || !lon) {
    console.error("Coordonnées non valides pour la carte.");
    return;
  }

  const mapContainer = document.getElementById("offer-map");
  if (!mapContainer) {
    console.error("Div 'offer-map' non trouvé.");
    return;
  }

  // Réinitialisation du div
  mapContainer.innerHTML = "";

  // Création de la carte
  const offerMap = L.map("offer-map").setView([lat, lon], 14);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(offerMap);

  // Ajout du marqueur
  const icon = getCustomIcon(categorie);
  L.marker([lat, lon], { icon }).addTo(offerMap)
    .bindPopup("Emplacement de l'offre")
    .openPopup();
}

document.addEventListener("DOMContentLoaded", function () {
  if (typeof currentOffer !== "undefined" && currentOffer.lat && currentOffer.lon) {
    showOfferMap(currentOffer.lat, currentOffer.lon, currentOffer.categorie);
  }
});

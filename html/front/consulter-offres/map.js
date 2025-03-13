let allOffers = [];
let map;
let markerCluster;

function addMap() {
  map = L.map('map', { minZoom: 3 }).setView([48.8566, 2.3522], 5);

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

function addOffreMarqueur(offer) {
  const marker = L.marker([offer.lat, offer.lon])
    .bindPopup(
      `<b>${offer.titre || 'Offer'}</b><br>${offer.ville || ''}, ${offer.pays || ''}`
    );
  markerCluster.addLayer(marker);
}

async function addOffersWithAddresses(offers) {
  for (const offer of offers) {
    if (offer.lat !== null && offer.lon !== null) {
      allOffers.push(offer);
      addOffreMarqueur(offer);
      continue;
    }

    const addressParts = [
      offer.num_et_nom_de_voie,
      offer.complement_adresse,
      offer.code_postal,
      offer.ville,
      offer.pays
    ];
    const address = addressParts.filter(Boolean).join(' ');

    try {
      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`,
        {
          headers: {
            'User-Agent': 'Redden/1.0 (redden@dbadmin-sae.com)'
          }
        }
      );
      const data = await response.json();

      if (data.length > 0) {
        offer.lat = parseFloat(data[0].lat);
        offer.lon = parseFloat(data[0].lon);
      } else {
        offer.lat = null;
        offer.lon = null;
      }
    } catch (error) {
      console.error('Error fetching coordinates:', error);
      offer.lat = null;
      offer.lon = null;
    }
    if (offer.lat !== null && offer.lon !== null) {
      allOffers.push(offer);
      addOffreMarqueur(offer);

      try {
        console.log("toot");
        const response = await fetch('update_coords.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id_offre: offer.id_offre,
            lat: offer.lat,
            lon: offer.lon
          })
        });
        const text = await response.text();
        console.log('Update response:', text);
      } catch (updateError) {
        console.error('Error updating coordinates in DB:', updateError);
      }
    }
  }

  if (allOffers.length > 0) {
    const bounds = L.latLngBounds(allOffers.map(o => [o.lat, o.lon]));
    map.fitBounds(bounds, { padding: [50, 50] });
  } else {
    console.error('No offers available to display on the map.');
  }
  
  map.addLayer(markerCluster);
}

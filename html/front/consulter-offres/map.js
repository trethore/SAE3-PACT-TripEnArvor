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
  }).setView([48.8566, 2.3522], 5);

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

function getCustomIcon(categorie) {
  switch (categorie) {
    case 'Restauration':
      return L.icon({
        iconUrl: '../../images/frontOffice/icones/map/restaurant_icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
      });
    case 'Parc attraction':
      return L.icon({
        iconUrl: '../../images/frontOffice/icones/map/attraction_icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
      });
    case 'Spectacle':
      return L.icon({
        iconUrl: '../../images/frontOffice/icones/map/spectacle_icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
      });
    case 'Visite':
      return L.icon({
        iconUrl: '../../images/frontOffice/icones/map/visite_icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
      });
    case 'Activité':
      return L.icon({
        iconUrl: '../../images/frontOffice/icones/map/activite_icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
      });
    default:
      return L.icon({
        iconUrl: 'images/default_icon.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
      });
  }
}

function getStars(rating) {
  const fullStarSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="gold" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
  </svg>`;
  
  const halfStarSvg = `<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <defs>
      <clipPath id="halfClip">
        <rect x="0" y="0" width="12" height="24"/>
      </clipPath>
    </defs>
    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="none" stroke="gold" stroke-width="2"/>
    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="gold" clip-path="url(#halfClip)"/>
  </svg>`;
  
  const emptyStarSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="gold" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
  </svg>`;

  const maxStars = 5;
  const clamped = Math.max(0, Math.min(rating, maxStars));
  const fullStars = Math.floor(clamped);
  const halfStars = (clamped % 1 !== 0) ? 1 : 0;
  const emptyStars = maxStars - fullStars - halfStars;

  return fullStarSvg.repeat(fullStars)
       + halfStarSvg.repeat(halfStars)
       + emptyStarSvg.repeat(emptyStars);
}

function addOffreMarqueur(offer) {
  const note = isNaN(parseFloat(offer.note))
    ? 'Note Indisponible'
    : getStars(parseFloat(offer.note));

  const addressParts = [
    offer.num_et_nom_de_voie,
    offer.complement_adresse,
    offer.code_postal,
    offer.ville,
    offer.pays,
  ];
  const address = addressParts.filter(part => part && part.trim() !== '').join(', ');

  const priceInfo = offer.gammedeprix
    ? `Gamme de prix: ${offer.gammedeprix}`
    : (offer.prix && offer.prix > 0 ? `Prix: ${offer.prix} €` : 'Gratuit');

  const itineraryLink = `https://www.google.com/maps/dir/?api=1&destination=${offer.lat},${offer.lon}`;

  const popupContent = `
    <b>${offer.titre || 'Offer'}${offer.categorie ? ` - ${offer.categorie}` : ''}</b><br>
    ${address ? `${address}<br>` : ''}
    ${note}<br>
    ${priceInfo}<br>
    <a href="/front/consulter-offre/index.php?id=${offer.id_offre}" target="_blank">Voir plus</a><br>
    <a href="${itineraryLink}" target="_blank">Itinéraire</a>
  `;

  const customIcon = getCustomIcon(offer.categorie);

  offer.marker = L.marker([offer.lat, offer.lon], { icon: customIcon })
                 .bindPopup(popupContent);
  markerCluster.addLayer(offer.marker);
}

async function addOfferWithAddress(offer) {
  if (offer.lat !== null && offer.lon !== null) {
    addOffreMarqueur(offer);
    return offer; // Return the offer with lat/lon
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
        headers: { 'User-Agent': 'Redden/1.0 (redden@dbadmin-sae.com)' }
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
    addOffreMarqueur(offer);
    try {
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

  return offer; // Return the updated offer
}

async function addOffersWithAddresses(offers) {
  for (const offer of offers) {
    const updatedOffer = await addOfferWithAddress(offer);
    allOffers.push(updatedOffer); // Push only in addOffersWithAddresses
  }

  const validOffers = allOffers.filter(o => o.lat !== null && o.lon !== null);
  if (validOffers.length > 0) {
    const bounds = L.latLngBounds(validOffers.map(o => [o.lat, o.lon]));
    map.fitBounds(bounds, { padding: [50, 50] });
  } else {
    console.error('No offers available to display on the map.');
  }

  map.addLayer(markerCluster);
}



function applyMapFilters() {
  let offers = [...allOffers];

  // Filter by Category
  const categoryCheckboxes = document.querySelectorAll(".categorie input[type='checkbox']:checked");
  const selectedCategories = Array.from(categoryCheckboxes).map(cb => cb.parentElement.textContent.trim());
  if (selectedCategories.length > 0) {
    offers = offers.filter(offer => selectedCategories.includes(offer.categorie));
  }
  
  // Filter by Availability
  const availabilityInput = document.querySelector(".disponibilite input[type='checkbox']:checked");
  if (availabilityInput) {
    const availability = availabilityInput.parentElement.textContent.trim().toLowerCase();
    offers = offers.filter(offer => {
      const offerAvailability = offer.ouverture ? offer.ouverture : "";
      return offerAvailability.toLowerCase() === availability || (availability === "ouvert" && offerAvailability === "ferme bnt.");
    });
  }

  // Filter by Note (minimum star rating)
  const minNoteSelect = document.querySelector(".note");
  const selectedNote = minNoteSelect.value ? minNoteSelect.selectedIndex : null;
  if (selectedNote) {
    offers = offers.filter(offer => offer.note >= selectedNote);
  }

  const minPrice = parseFloat(document.querySelector(".min").value || "0");
  const maxPrice = parseFloat(document.querySelector(".max").value || "Infinity");
  if (minPrice > 0 || maxPrice !== Infinity) {
    offers = offers.filter(offer => {
      if (offer.categorie.trim() === "Restauration") {
        return false;
      } else {
        return offer.prix >= minPrice && offer.prix <= maxPrice;
      }
    });  
  }
  
  const locationInput = document.querySelector("#search-location");
  const searchLocation = locationInput ? locationInput.value.trim().toLowerCase() : "";
  if (searchLocation) {
    offers = offers.filter(offer =>{
        const addressParts = [
          offer.num_et_nom_de_voie,
          offer.complement_adresse,
          offer.code_postal,
          offer.ville,
          offer.pays
        ];
        const address = addressParts.filter(Boolean).join(' ');
        return address.trim().toLowerCase().includes(searchLocation);
      });
  }

  const avisInput = document.querySelector(".oui_avis input[type='checkbox']:checked");
  if (avisInput) {
      const contientAvis = avisInput.parentElement.textContent.trim().toLowerCase();
      offers = offers.filter(offer => {
          if (typeof offer.avis === 'undefined') {
            return false;
          }
          return contientAvis.toLowerCase() === offer.avis.toLowerCase();
      });
  }

  updateMap(offers);
}


function updateMap(offers) {
  markerCluster.clearLayers();
  offers.forEach(offer => {
    addOfferWithAddress(offer);
  });
}
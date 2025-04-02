let map;

/**
 * Initializes the Leaflet map centered on the offer's location.
 * @param {Array<number>} centerCoords - Array containing [latitude, longitude].
 * @param {number} zoomLevel - Initial zoom level for the map.
 */
function addMap(centerCoords, zoomLevel = 13) {
  
  const southWest = L.latLng(-85, -180);
  const northEast = L.latLng(85, 180);
  const bounds = L.latLngBounds(southWest, northEast);
  
  if (map) {
    map.remove();
    map = null; 
  }

  map = L.map('map', { 
    minZoom: 5,
    maxBounds: bounds,
    maxBoundsViscosity: 1.0
  }).setView(centerCoords, zoomLevel);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  console.log("Map initialized at:", centerCoords);
}

/**
 * Returns a custom Leaflet icon based on the offer category.
 * @param {string} categorie - The category name.
 * @returns {L.Icon} - The Leaflet icon object.
 */
function getCustomIcon(categorie) {
  
  const basePath = '../../images/frontOffice/icones/map/';

  switch (categorie) {
    case 'Restauration':
      return L.icon({
        iconUrl: `${basePath}restaurant_icon.png`,
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
      });
    case 'Parc attraction':
      return L.icon({
        iconUrl: `${basePath}attraction_icon.png`,
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
      });
    case 'Spectacle':
      return L.icon({
        iconUrl: `${basePath}spectacle_icon.png`,
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
      });
    case 'Visite':
      return L.icon({
        iconUrl: `${basePath}visite_icon.png`,
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
      });
    case 'Activité':
      return L.icon({
        iconUrl: `${basePath}activite_icon.png`,
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
      });
    default:
      
      
      return L.icon({
        iconUrl: '../../images/default_icon.png', 
        iconSize: [40, 40], iconAnchor: [20, 40], popupAnchor: [0, -40]
      });
  }
}

/**
 * Generates HTML string for star ratings based on a numerical rating.
 * @param {number} rating - The numerical rating (e.g., 0 to 5).
 * @returns {string} - HTML string containing SVG stars.
 */
function getStars(rating) {
    
    const fullStarSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="gold" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
    const halfStarSvg = `<svg width="16" height="16" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><defs><clipPath id="halfClip"><rect x="0" y="0" width="12" height="24"/></clipPath></defs><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="none" stroke="gold" stroke-width="2"/><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="gold" clip-path="url(#halfClip)"/></svg>`;
    const emptyStarSvg = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="gold" stroke-width="2" xmlns="http://www.w3.org/2000/svg"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;

    const maxStars = 5;
    const clamped = Math.max(0, Math.min(rating, maxStars));
    const fullStars = Math.floor(clamped);
    const halfStars = (clamped % 1 !== 0) ? 1 : 0;
    const emptyStars = maxStars - fullStars - halfStars;

    return fullStarSvg.repeat(fullStars)
         + halfStarSvg.repeat(halfStars)
         + emptyStarSvg.repeat(emptyStars);
}

/**
 * Adds a single marker for the offer to the map.
 * @param {object} offer - The offer data object. Must have lat, lon, titre, etc.
 */
function addSingleOfferMarker(offer) {
  if (!map) {
      console.error("Map is not initialized. Cannot add marker.");
      return;
  }
  if (offer.lat === null || offer.lon === null || isNaN(offer.lat) || isNaN(offer.lon)) {
      console.error("Invalid coordinates for offer:", offer.titre);
      
      const mapDiv = document.getElementById('map');
      if(mapDiv) {
          mapDiv.innerHTML = '<p class="error-message">Impossible d\'afficher cette offre sur la carte car son adresse n\'a pas pu être localisée.</p>';
          mapDiv.style.height = 'auto'; 
      }
      return;
  }

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
    <b>${offer.titre || 'Offre'}${offer.categorie ? ` - ${offer.categorie}` : ''}</b><br>
    ${address ? `${address}<br>` : ''}
    ${note}<br>
    ${priceInfo}<br>
    ${offer.id_offre ? `<a href="/front/consulter-offre/index.php?id=${offer.id_offre}" target="_blank">Voir les détails</a><br>` : ''}
    <a href="${itineraryLink}" target="_blank">Itinéraire Google Maps</a>
  `;

  const customIcon = getCustomIcon(offer.categorie);

  
  const marker = L.marker([offer.lat, offer.lon], { icon: customIcon })
                 .bindPopup(popupContent)
                 .addTo(map);

  console.log("Marker added for:", offer.titre);

  
  
}

/**
 * Attempts to geocode the offer's address using Nominatim if lat/lon are missing.
 * Updates the offer object and potentially the database.
 * @param {object} offer - The offer data object.
 * @returns {Promise<object>} - A promise that resolves with the updated offer object.
 */
async function geocodeOfferAddress(offer) {
  
   if (offer.lat !== null && offer.lon !== null && !isNaN(parseFloat(offer.lat)) && !isNaN(parseFloat(offer.lon))) {
    console.log("Coordinates already exist for:", offer.titre);
    return offer; 
  }

  console.log("Attempting to geocode address for:", offer.titre);
  const addressParts = [
    offer.num_et_nom_de_voie,
    
    offer.code_postal,
    offer.ville,
    offer.pays
  ];
  const address = addressParts.filter(part => part && part.trim() !== '').join(', ');

  if (!address) {
      console.warn("Cannot geocode: Address is empty for offer:", offer.titre);
      offer.lat = null; 
      offer.lon = null;
      return offer;
  }

  try {
    
    const response = await fetch(
      `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`,
      {
        
        headers: { 'User-Agent': 'Redden/1.0 (redden@dbadmin-sae.com)' }
      }
    );
    if (!response.ok) {
        throw new Error(`Nominatim API request failed with status: ${response.status}`);
    }
    const data = await response.json();

    if (data && data.length > 0) {
      offer.lat = parseFloat(data[0].lat);
      offer.lon = parseFloat(data[0].lon);
      console.log("Geocoding successful:", offer.titre, "->", offer.lat, offer.lon);

      
      await updateCoordsInDB(offer.id_offre, offer.lat, offer.lon);

    } else {
      console.warn("Geocoding failed: No results found for address:", address);
      offer.lat = null; 
      offer.lon = null;
    }
  } catch (error) {
    console.error('Error during geocoding or DB update:', error);
    offer.lat = null; 
    offer.lon = null;
  }

  return offer; 
}

/**
 * Sends the updated coordinates to the backend PHP script.
 * @param {number|string} offerId - The ID of the offer.
 * @param {number} lat - The latitude.
 * @param {number} lon - The longitude.
 * @returns {Promise<void>}
 */
async function updateCoordsInDB(offerId, lat, lon) {
  if (!offerId || lat === null || lon === null) {
    console.warn("Skipping DB update due to invalid data:", { offerId, lat, lon });
    return;
  }

  console.log(`Updating coordinates in DB for offer ID ${offerId}:`, { lat, lon });
  try {
    
    const response = await fetch('../php/update_coords.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        id_offre: offerId,
        lat: lat,
        lon: lon
      })
    });

    const responseText = await response.text(); 
    if (!response.ok) {
        
        console.error(`Failed to update coordinates in DB. Status: ${response.status}. Response: ${responseText}`);
        throw new Error(`Server responded with status ${response.status}`);
    }
    console.log('DB update response:', responseText); 

  } catch (updateError) {
    console.error('Error sending coordinate update to DB:', updateError);
    
  }
}

/**
 * Main function to display a single offer on the map.
 * It handles geocoding if necessary, initializes the map, and adds the marker.
 * @param {object} offerData - The offer data object fetched from your backend.
 */
async function displayOfferOnMap(offerData) {
    if (!offerData || typeof offerData !== 'object') {
        console.error("Invalid offer data provided.");
        
        const mapDiv = document.getElementById('map');
        if(mapDiv) {
            mapDiv.innerHTML = '<p class="error-message">Impossible de charger les données de l\'offre.</p>';
            mapDiv.style.height = 'auto';
        }
        return;
    }

    console.log("Processing offer:", offerData.titre);

    
    const processedOffer = await geocodeOfferAddress(offerData);

    
    if (processedOffer.lat === null || processedOffer.lon === null || isNaN(processedOffer.lat) || isNaN(processedOffer.lon)) {
        console.error("Unable to display offer on map due to missing or invalid coordinates after geocoding attempt:", processedOffer.titre);
        
        const mapDiv = document.getElementById('map');
        if(mapDiv) {
            mapDiv.innerHTML = '<p class="error-message">L\'adresse de cette offre n\'a pas pu être localisée précisément pour l\'afficher sur la carte.</p>';
            mapDiv.style.height = 'auto'; 
        }
        return; 
    }

    
    addMap([processedOffer.lat, processedOffer.lon]); 

    
    addSingleOfferMarker(processedOffer);
}

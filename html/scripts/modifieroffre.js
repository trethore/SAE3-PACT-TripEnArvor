async function geolocaliseOffer($id) {
    const numEtNomDeVoie = document.getElementById('num_et_nom_de_voie').value;
    const cp = document.getElementById('cp').value;
    const ville = document.getElementById('ville').value;
    const pays = 'France';

    const addressParts = [numEtNomDeVoie, cp, ville, pays];
    const address = addressParts.filter(Boolean).join(' ');

    try {
      const response = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`, {
        headers: { 'User-Agent': 'Redden/1.0 (redden@dbadmin-sae.com)' }
      });
      const data = await response.json();

      if (data.length > 0) {
        const lat = parseFloat(data[0].lat);
        const lon = parseFloat(data[0].lon);

        const update = await fetch('/back/update_coords.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            id_offre: id,
            lat: lat,
            lon: lon
          })
        });
        console.log('Coords update response:', await update.text());
      } else {
        console.warn('Adresse non localisée');
      }
    } catch (error) {
      console.error('Erreur de géolocalisation:', error);
    }
  }
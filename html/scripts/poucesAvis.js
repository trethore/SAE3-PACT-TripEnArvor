document.addEventListener("DOMContentLoaded", function () {
    const pouceHauts = document.querySelectorAll('.pouceHaut');
    const pouceBas = document.querySelectorAll('.pouceBas');

    const images = {
        haut: {
            actif: '/images/universel/icones/pouce-up-hover.png',
            inactif: '/images/universel/icones/pouce-up.png',
        },
        bas: {
            actif: '/images/universel/icones/pouce-down-hover.png',
            inactif: '/images/universel/icones/pouce-down.png',
        },
    };

    // Fonction qui gère les clics sur les pouces
    function handleThumbClick(thumb, otherThumb, countElement, otherCountElement, activeImage, inactiveImage, otherActiveImage, otherInactiveImage) {
        const id_membre = thumb.getAttribute('data-id-membre');
        const id_offre = thumb.getAttribute('data-id-offre');
        var action = thumb.src.includes(activeImage) ? 'remove' : 'add';
        var otherIsActive = otherThumb.src.includes(otherActiveImage);
        var change = otherIsActive ? 'true' : 'false';

        // Modification de l'image du pouce
        thumb.src = action === 'add' ? activeImage : inactiveImage;
        
        // Si l'autre pouce est actif, on le rend inactif et on décrémente son compteur
        if (otherIsActive) {
            otherThumb.src = otherInactiveImage;
            if (otherCountElement) {
                otherCountElement.textContent = Math.max(0, parseInt(otherCountElement.textContent) - 1);
            }
        }

        // Mise à jour du compteur pour le pouce actuel
        if (countElement) {
            countElement.textContent = Math.max(0, parseInt(countElement.textContent) + (action === 'add' ? 1 : -1));
        }
        
        // Envoi des données au serveur via AJAX
        fetch('/utils/pouces.php', {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_membre=${id_membre}&id_offre=${id_offre}&type=${thumb.classList.contains('pouceHaut') ? 'like' : 'dislike'}&action=${action}&change=${change}`
        })
        .then(response => response.text())
        .then(data => {
            location.reload();  // Recharge la page pour mettre à jour l'affichage
        })
        .catch(error => console.error("Erreur AJAX:", error));
    }

    // Événements pour les pouces hauts
    pouceHauts.forEach(pouceHaut => {
        pouceHaut.addEventListener('click', function () {
            const nbPouceHaut = pouceHaut.previousElementSibling; // Compteur du pouce haut
            const pouceBas = pouceHaut.nextElementSibling.nextElementSibling; // Pouce bas
            const nbPouceBas = pouceBas.previousElementSibling; // Compteur du pouce bas

            handleThumbClick(
                pouceHaut,
                pouceBas,
                nbPouceHaut,
                nbPouceBas,
                images.haut.actif,
                images.haut.inactif,
                images.bas.actif,
                images.bas.inactif
            );
        });
    });

    // Événements pour les pouces bas
    pouceBas.forEach(pouceBas => {
        pouceBas.addEventListener('click', function () {
            const nbPouceBas = pouceBas.previousElementSibling; // Compteur du pouce bas
            const pouceHaut = pouceBas.previousElementSibling.previousElementSibling; // Pouce haut
            const nbPouceHaut = pouceHaut.previousElementSibling; // Compteur du pouce haut

            handleThumbClick(
                pouceBas,
                pouceHaut,
                nbPouceBas,
                nbPouceHaut,
                images.bas.actif,
                images.bas.inactif,
                images.haut.actif,
                images.haut.inactif
            );
        });
    });
});

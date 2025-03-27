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

    // Function to handle thumb clicks
    function handleThumbClick(thumb, otherThumb, countElement, otherCountElement, activeImage, inactiveImage, otherActiveImage, otherInactiveImage) {
        const id_membre = thumb.getAttribute('data-id-membre');
        const id_offre = thumb.getAttribute('data-id-offre');
        var action = thumb.src.includes(activeImage) ? 'remove' : 'add';
        var otherIsActive = otherThumb.src.includes(otherActiveImage);
        var change = otherIsActive ? 'true' : 'false'; 

        thumb.src = action === 'add' ? activeImage : inactiveImage;
        if (otherIsActive) otherThumb.src = otherInactiveImage;
        if (countElement) countElement.textContent = Math.max(0, parseInt(countElement.textContent) + (action === 'add' ? 1 : -1));
        if (otherIsActive && otherCountElement) otherCountElement.textContent = Math.max(0, parseInt(otherCountElement.textContent) - 1);
        
        fetch('/utils/pouces.php', {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_membre=${id_membre}&id_offre=${id_offre}&type=${thumb.classList.contains('pouceHaut') ? 'like' : 'dislike'}&action=${action}&change=${change}`
        })
        .then(response => response.text()) 
        .then(data => {
            location.reload(); 
        })
        .catch(error => console.error("Erreur AJAX:", error));
    }

    // Add event listeners for thumbs up
    pouceHauts.forEach(pouceHaut => {
        pouceHaut.addEventListener('click', function () {
            const id = pouceHaut.getAttribute('data-id');
            const nbPouceHaut = pouceHaut.previousElementSibling; // Count for thumbs up
            const pouceBas = pouceHaut.nextElementSibling.nextElementSibling; // Thumbs down button
            const nbPouceBas = pouceBas.previousElementSibling; // Count for thumbs down

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

    // Add event listeners for thumbs down
    pouceBas.forEach(pouceBas => {
        pouceBas.addEventListener('click', function () {
            const id = pouceBas.getAttribute('data-id');
            const nbPouceBas = pouceBas.previousElementSibling; // Count for thumbs down
            const pouceHaut = pouceBas.previousElementSibling.previousElementSibling; // Thumbs up button
            const nbPouceHaut = pouceHaut.previousElementSibling; // Count for thumbs up

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
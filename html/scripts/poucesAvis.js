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

    function handleThumbClick(thumb, otherThumb, countElement, otherCountElement, activeImage, inactiveImage, otherActiveImage, otherInactiveImage) {
        const id_membre = thumb.getAttribute('data-id-membre');
        const id_offre = thumb.getAttribute('data-id-offre');
        var action = thumb.src.includes(activeImage) ? 'remove' : 'add';
        var otherIsActive = otherThumb.src.includes(otherActiveImage);
        var change = otherIsActive ? 'true' : 'false';

        thumb.src = action === 'add' ? activeImage : inactiveImage;
        
        if (otherIsActive) {
            otherThumb.src = otherInactiveImage;
            if (otherCountElement) {
                otherCountElement.textContent = Math.max(0, parseInt(otherCountElement.textContent) - 1);
            }
        }

        if (countElement) {
            countElement.textContent = Math.max(0, parseInt(countElement.textContent) + (action === 'add' ? 1 : -1));
        }
        
        fetch('/utils/pouces.php', {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_membre=${id_membre}&id_offre=${id_offre}&type=${thumb.classList.contains('pouceHaut') ? 'like' : 'dislike'}&action=${action}&change=${change}`
        })
        .then(response => response.text())
        .then(data => {})
        .catch(error => console.error("Erreur AJAX:", error));
    }

    pouceHauts.forEach(pouceHaut => {
        pouceHaut.addEventListener('click', function () {
            const nbPouceHaut = pouceHaut.previousElementSibling; 
            const pouceBas = pouceHaut.nextElementSibling.nextElementSibling; 
            const nbPouceBas = pouceBas.previousElementSibling;
            handleThumbClick(pouceHaut, pouceBas, nbPouceHaut, nbPouceBas, images.haut.actif, images.haut.inactif, images.bas.actif, images.bas.inactif);
        });
    });

    pouceBas.forEach(pouceBas => {
        pouceBas.addEventListener('click', function () {
            const nbPouceBas = pouceBas.previousElementSibling; 
            const pouceHaut = pouceBas.previousElementSibling.previousElementSibling; 
            const nbPouceHaut = pouceHaut.previousElementSibling; 
            handleThumbClick(pouceBas, pouceHaut, nbPouceBas, nbPouceHaut, images.bas.actif, images.bas.inactif, images.haut.actif, images.haut.inactif);
        });
    });
});

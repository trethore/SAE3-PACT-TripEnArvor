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
        const id = thumb.getAttribute('data-id');

        // If the thumb is already active, deactivate it
        if (thumb.src.includes(activeImage)) {
            thumb.src = inactiveImage;
            countElement.textContent = parseInt(countElement.textContent) - 1;
        } else {
            // Activate the clicked thumb
            thumb.src = activeImage;
            countElement.textContent = parseInt(countElement.textContent) + 1;

            // Deactivate the other thumb if it's active
            if (otherThumb.src.includes(otherActiveImage)) {
                otherThumb.src = otherInactiveImage;
                otherCountElement.textContent = parseInt(otherCountElement.textContent) - 1;
            }
        }

        // Optionally send an AJAX request to update the server
        // updateThumbCount(id, thumb.classList.contains('pouceHaut') ? 'haut' : 'bas');
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
            const pouceHaut = pouceBas.previousElementSibling.previousElementSibling.previousElementSibling; // Thumbs up button
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
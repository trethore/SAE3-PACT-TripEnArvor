document.addEventListener("DOMContentLoaded", function() {
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

    // Function to handle the thumb up click
    pouceHauts.forEach(pouceHaut => {
        pouceHaut.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nbPouceHaut = this.previousElementSibling; // Assuming the count is in the previous sibling element

            // Toggle the active state
            if (this.src === images.haut.actif) {
                this.src = images.haut.inactif;
                nbPouceHaut.textContent = parseInt(nbPouceHaut.textContent) - 1;
            } else {
                this.src = images.haut.actif;
                nbPouceHaut.textContent = parseInt(nbPouceHaut.textContent) + 1;
            }

            // You can also send an AJAX request here to update the count on the server
            // updateThumbCount(id, 'haut');
        });
    });

    // Function to handle the thumb down click
    pouceBas.forEach(pouceBas => {
        pouceBas.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nbPouceBas = this.previousElementSibling; // Assuming the count is in the previous sibling element

            // Toggle the active state
            if (this.src === images.bas.actif) {
                this.src = images.bas.inactif;
                nbPouceBas.textContent = parseInt(nbPouceBas.textContent) - 1;
            } else {
                this.src = images.bas.actif;
                nbPouceBas.textContent = parseInt(nbPouceBas.textContent) + 1;
            }

            // You can also send an AJAX request here to update the count on the server
            // updateThumbCount(id, 'bas');
        });
    });
});
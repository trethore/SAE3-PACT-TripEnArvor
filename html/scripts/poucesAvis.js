document.addEventListener("DOMContentLoaded", function () {
    const pouceHauts = document.querySelectorAll('.pouceHaut');
    const pouceBas = document.querySelectorAll('.pouceBas');

    function handleThumbClick(thumb, otherThumb, countElement, otherCountElement, type) {
        const id_offre = thumb.getAttribute('data-id-offre');
        const id_membre_avis = thumb.getAttribute('data-id-membre-avis');
        const id_membre_reaction = thumb.getAttribute('data-id-membre-reaction');

        const action = thumb.src.includes("hover") ? "remove" : "add";

        fetch('/utils/pouces.php', {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_offre=${id_offre}&id_membre_avis=${id_membre_avis}&id_membre_reaction=${id_membre_reaction}&type=${type}&action=${action}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                countElement.textContent = data.nb_pouce_haut;
                otherCountElement.textContent = data.nb_pouce_bas;

                thumb.src = data.nb_pouce_haut == 1 ? "/images/universel/icones/pouce-up-hover.png" : "/images/universel/icones/pouce-up.png";
                otherThumb.src = data.nb_pouce_bas == 1 ? "/images/universel/icones/pouce-down-hover.png" : "/images/universel/icones/pouce-down.png";
            }
        })
        .catch(error => console.error("Erreur AJAX:", error));
    }

    pouceHauts.forEach(pouceHaut => {
        pouceHaut.addEventListener('click', function () {
            const nbPouceHaut = pouceHaut.previousElementSibling; 
            const pouceBas = pouceHaut.nextElementSibling.nextElementSibling; 
            const nbPouceBas = pouceBas.previousElementSibling;
            handleThumbClick(pouceHaut, pouceBas, nbPouceHaut, nbPouceBas, "like");
        });
    });

    pouceBas.forEach(pouceBas => {
        pouceBas.addEventListener('click', function () {
            const nbPouceBas = pouceBas.previousElementSibling; 
            const pouceHaut = pouceBas.previousElementSibling.previousElementSibling; 
            const nbPouceHaut = pouceHaut.previousElementSibling; 
            handleThumbClick(pouceBas, pouceHaut, nbPouceBas, nbPouceHaut, "dislike");
        });
    });
});

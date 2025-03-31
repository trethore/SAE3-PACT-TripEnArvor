document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".pouce").forEach(pouce => {
        pouce.addEventListener("click", function(event) {
            event.preventDefault();
            
            const idOffre = this.getAttribute("data-id-offre");
            const idMembreAvis = this.getAttribute("data-id-membre-avis");
            const idMembreReaction = this.getAttribute("data-id-membre-reaction");
            const type = this.classList.contains("pouceHaut") ? "like" : "dislike";
            
            // Vérifier l'état actuel du bouton cliqué
            const nbPouceHaut = this.closest(".display-ligne").querySelector(".nbPouceHaut");
            const nbPouceBas = this.closest(".display-ligne").querySelector(".nbPouceBas");
            const action = parseInt(type === "like" ? nbPouceHaut.textContent : nbPouceBas.textContent) === 1 ? "remove" : "add";

            fetch("/utils/pouces.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id_offre=${idOffre}&id_membre_avis=${idMembreAvis}&id_membre_reaction=${idMembreReaction}&type=${type}&action=${action}`
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.success) {
                    if (type === "like") {
                        nbPouceHaut.textContent = action === "add" ? 1 : 0;
                        nbPouceBas.textContent = 0; // On enlève le dislike si présent
                    } else {
                        nbPouceBas.textContent = action === "add" ? 1 : 0;
                        nbPouceHaut.textContent = 0; // On enlève le like si présent
                    }
                } else {
                    console.error("Erreur serveur :", data.error);
                }
            })
            .catch(error => console.error("Erreur :", error));
        });
    });
});

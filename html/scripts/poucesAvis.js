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
            .then(response => response.text()) // Traitement de la réponse en texte brut
            .then(data => {
                console.log("Réponse serveur :", data);
                location.reload();
            })
            .catch(error => console.error("Erreur :", error));
        });
    });
});
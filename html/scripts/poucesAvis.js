document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".pouce").forEach(pouce => {
        pouce.addEventListener("click", function(event) {
            event.preventDefault();

            if (!this.hasAttribute("data-id-offre")) return;

            const idOffre = this.getAttribute("data-id-offre");
            const idMembreAvis = this.getAttribute("data-id-membre-avis");
            const idMembreReaction = this.getAttribute("data-id-membre-reaction");
            const isLike = this.classList.contains("pouceHaut");
            
            const container = this.closest(".display-ligne");
            const nbPouceHaut = container.querySelector(".nbPouceHaut");
            const nbPouceBas = container.querySelector(".nbPouceBas");
            const likeImg = container.querySelector(".pouceHaut");
            const dislikeImg = container.querySelector(".pouceBas");

            // Determine current state
            const currentLikeState = likeImg.src.includes("-hover");
            const currentDislikeState = dislikeImg.src.includes("-hover");
            
            let action;
            if (isLike) {
                action = currentLikeState ? "remove" : "add";
            } else {
                action = currentDislikeState ? "remove" : "add";
            }
            
            // If clicking opposite of current selection, switch
            if ((isLike && currentDislikeState) || (!isLike && currentLikeState)) {
                action = "switch";
            }

            fetch("/utils/pouces.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id_offre=${idOffre}&id_membre_avis=${idMembreAvis}&id_membre_reaction=${idMembreReaction}&type=${isLike ? 'like' : 'dislike'}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI based on server response
                    nbPouceHaut.textContent = data.nb_pouce_haut;
                    nbPouceBas.textContent = data.nb_pouce_bas;
                    
                    // Update images
                    likeImg.src = data.has_liked ? "/images/universel/icones/pouce-up-hover.png" 
                                               : "/images/universel/icones/pouce-up.png";
                    dislikeImg.src = data.has_disliked ? "/images/universel/icones/pouce-down-hover.png" 
                                                      : "/images/universel/icones/pouce-down.png";
                }
            })
            .catch(error => console.error("Erreur :", error));
        });
    });
});
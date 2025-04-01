document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".pouce").forEach(pouce => {
        pouce.addEventListener("click", function(event) {
            event.preventDefault();

            const idOffre = this.getAttribute("data-id-offre");
            const idMembreAvis = this.getAttribute("data-id-membre-avis");
            const idMembreReaction = this.getAttribute("data-id-membre-reaction");
            const isLike = this.classList.contains("pouceHaut");
            const container = this.closest(".display-ligne");
            
            // Determine action based on current image state
            const isActive = this.src.includes('-hover');
            const action = isActive ? "remove" : "add";
            const type = isLike ? "like" : "dislike";

            fetch("/utils/pouces.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id_offre=${idOffre}&id_membre_avis=${idMembreAvis}&id_membre_reaction=${idMembreReaction}&type=${type}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update counts
                    container.querySelector(".nbPouceHaut").textContent = data.nb_pouce_haut;
                    container.querySelector(".nbPouceBas").textContent = data.nb_pouce_bas;
                    
                    // Update images
                    const likeImg = container.querySelector(".pouceHaut");
                    const dislikeImg = container.querySelector(".pouceBas");
                    
                    if (isLike) {
                        likeImg.src = isActive 
                            ? "/images/universel/icones/pouce-up.png" 
                            : "/images/universel/icones/pouce-up-hover.png";
                        // Ensure dislike is reset if like is clicked
                        if (!isActive) {
                            dislikeImg.src = "/images/universel/icones/pouce-down.png";
                        }
                    } else {
                        dislikeImg.src = isActive 
                            ? "/images/universel/icones/pouce-down.png" 
                            : "/images/universel/icones/pouce-down-hover.png";
                        // Ensure like is reset if dislike is clicked
                        if (!isActive) {
                            likeImg.src = "/images/universel/icones/pouce-up.png";
                        }
                    }
                } else {
                    console.error("Error:", data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });
    });
});
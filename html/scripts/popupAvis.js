document.addEventListener("DOMContentLoaded", () => {

    const popupSupprimerAvis = document.getElementById("popup-supprimer-avis");
    const boutonOuvrirPopup = document.getElementById("bouton-supprimer-avis");
    const boutonFermerPopup = document.getElementById("bouton-fermer-popup");

    boutonOuvrirPopup.addEventListener("click", () => {
        popupSupprimerAvis.style.display = "block";
    });

    boutonFermerPopup.addEventListener("click", () => {
        popupSupprimerAvis.style.display = "none";
    });
});
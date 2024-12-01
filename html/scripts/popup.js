const form = document.getElementById("myForm");
const popupOverlay = document.getElementById("popupOverlay");
const popup = document.getElementById("validerModifCompte");
const boutonAnnuler = document.getElementById("boutonAnnuler");
const boutonValider = document.getElementById("boutonValider");

// Affiche la popup à la soumission du formulaire
form.addEventListener("submit", function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire
    popupOverlay.style.display = "block";
    popup.style.display = "flex";
});

// Ferme la popup sans valider
boutonAnnuler.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popup.style.display = "none";
});

// Valide les modifications et soumet le formulaire
boutonValider.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popup.style.display = "none";
    form.submit();
});
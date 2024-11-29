const form = document.getElementById("myForm");
const popupOverlay = document.getElementById("popupOverlay");
const popup = document.getElementById("popup");
const annulerButton = document.getElementById("annuler");
const validerButton = document.getElementById("valider");

// Affiche la popup à la soumission du formulaire
myForm.addEventListener("submit", function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire
    popupOverlay.style.display = "block";
    popup.style.display = "block";
});

// Ferme la popup sans valider
annulerButton.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popup.style.display = "none";
});

// Valide les modifications et soumet le formulaire
validerButton.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popup.style.display = "none";
    form.submit();
});
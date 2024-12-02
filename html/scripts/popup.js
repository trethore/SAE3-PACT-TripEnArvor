const form = document.getElementById("myForm");
const popupOverlay = document.getElementById("popupOverlay");
const popupValider = document.getElementById("validerModifCompte");
const popupAnnuler = document.getElementById("annulerModifCompte");
const popupQuitter = document.getElementById("quitterModifCompte");
const boutonAnnuler = document.getElementById("boutonAnnuler");
const boutonValider = document.getElementById("boutonValider");

const retourCompte = document.getElementsByClassName("retourCompte");
const retourMenu = document.getElementsByClassName("retourAccueil");
const boutonReprendre = document.getElementById("boutonReprendre");
const boutonQuitter = document.getElementById("boutonQuitter");

/**************************
    POP-UP DE VALIDATION 
**************************/

// Affiche la popup à la soumission du formulaire
form.addEventListener("submit", function(event) {
    event.preventDefault(); // Empêche l'envoi du formulaire
    popupOverlay.style.display = "block";
    popupValider.style.display = "flex";
});

// Ferme la popup sans valider
boutonAnnuler.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popupValider.style.display = "none";
});

// Valide les modifications et soumet le formulaire
boutonValider.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popupValider.style.display = "none";
    form.submit();
});

/***********************************
    POP-UP DE RETOUR A L'ACCUEIL 
************************************/

// Ajout d'un écouteur sur chaque élément avec la classe "retourAccueil"
Array.from(retourMenu).forEach((element) => {
    element.addEventListener("click", function(event) {
        event.preventDefault(); // Empêche le changement de page
        popupOverlay.style.display = "block";
        popupQuitter.style.display = "flex";
    });
});


/***********************************
    POP-UP DE RETOUR AU COMPTE 
************************************/

// Ajout d'un écouteur sur chaque élément avec la classe "retourCompte"
Array.from(retourCompte).forEach((element) => {
    element.addEventListener("click", function(event) {
        event.preventDefault(); // Empêche le changement de page
        popupOverlay.style.display = "block";
        popupQuitter.style.display = "flex";
    });
});


// Reprends les modifications
boutonReprendre.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popupQuitter.style.display = "none";
})

// Quitte la page sans enregistrer les modification
boutonQuitter.addEventListener("click", function() {
    // Redirige vers l'URL initiale
    window.location.href = retourMenu.href;
})
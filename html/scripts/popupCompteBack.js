const form = document.getElementById("myForm");
const popupOverlay = document.getElementById("popupOverlay");

// constante pour valider les modifications du compte
const popupValider = document.getElementById("validerModifCompte");
const boutonAnnuler = document.getElementById("boutonAnnuler");
const boutonValider = document.getElementById("boutonValider");

// constante pour annuler les modifications du compte
const declencherPopupAnnuler = document.getElementById("retour");
const popupAnnuler = document.getElementById("annulerModifCompte");
const boutonReprendre = document.getElementById("boutonReprendre");
const boutonRetour = document.getElementById("boutonQuitter");

// constante de la popup pour retourner à l'accueil
const declencherPopupAccueil = document.querySelectorAll(".retourAccueil")
const popupAccueil = document.getElementById("popupRetourAccueil");
const boutonReprendreAccueil = document.getElementById("boutonReprendreAccueil");
const boutonRetourAccueil = document.getElementById("boutonRetourAccueil");

// constante de la popup pour retourner au compte
const declencherPopupCompte = document.getElementById("retourCompte");
const popupCompte = document.getElementById("popupRetourCompte");
const boutonReprendreCompte = document.getElementById("boutonReprendreCompte");
const boutonRetourCompte = document.getElementById("boutonRetourCompte");



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

    if (form.checkValidity()) { // Vérifie si le formulaire est valide
        form.submit();
    } else {
        console.error("Le formulaire contient des erreurs !");
    }
});

/***********************************
    POP-UP DE RETOUR A L'ACCUEIL 
************************************/

// Ajout d'un écouteur sur chaque élément avec la classe "retourAccueil"
Array.from(declencherPopupAccueil).forEach((element) => {
    element.addEventListener("click", function(event) {
        event.preventDefault(); // Empêche le changement de page
        popupOverlay.style.display = "block";
        popupAccueil.style.display = "flex";
    });
});

// Redirection vers la page d'accueil sans enregistrer les modification
boutonRetourAccueil.addEventListener("click", function() {
    // Redirige vers l'URL initiale
    window.location.href = '/back/liste-back';
});

boutonReprendreAccueil.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popupAccueil.style.display = "none";
});

/***********************************
    POP-UP DE RETOUR AU COMPTE 
************************************/

// Affiche la popup pour retourner sur le compte
declencherPopupCompte.addEventListener("click", function(event) {
    event.preventDefault(); // Empêche le changement de page
    popupOverlay.style.display = "block";
    popupCompte.style.display = "flex";
});

// Redirection vers la page du compte sans enregistrer les modification
boutonRetourCompte.addEventListener("click", function() {
    // Redirige vers l'URL initiale
    window.location.href = '/back/mon-compte';
});

boutonReprendreCompte.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popupCompte.style.display = "none";
});

declencherPopupAnnuler.addEventListener("click", function(event) {
    event.preventDefault(); // Empêche le changement de page
    popupOverlay.style.display = "block";
    popupCompte.style.display = "flex";
});

// Redirection vers la page du compte sans enregistrer les modification
boutonRetour.addEventListener("click", function() {
    // Redirige vers l'URL initiale
    window.location.href = '/back/mon-compte';
});

boutonReprendre.addEventListener("click", function() {
    popupOverlay.style.display = "none";
    popupCompte.style.display = "none";
});
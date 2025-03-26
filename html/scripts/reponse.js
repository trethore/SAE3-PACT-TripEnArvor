function afficherFormReponse(event, bouton, identifiant) {
    event.preventDefault();
    document.getElementById(`reponse-form-${identifiant}`).style.display = "block";
    bouton.style.display = "none"; // Cacher le bouton "Répondre" après affichage du formulaire
}

function validerReponse(event, identifiant, idOffre, idMembre) {
    event.preventDefault(); 
    const texteReponse = document.getElementById(`texte-reponse-${identifiant}`).value.trim();
    const reponseURL = "/utils/reponse.php";
    fetch(reponseURL, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_offre=${idOffre}&id_membre=${idMembre}&reponse=${texteReponse}`
    })
    .then(response => response.text())
    .then(data => {
        console.log("Réponse envoyée :", data);
        location.reload();
    })
    .catch(error => console.error("Erreur :", error));
}

function annulerSignaler(identifiant) {
    document.getElementById(`reponse-form-${identifiant}`).style.display = "none";
    document.querySelector(`button[onclick="afficherFormReponse(event, this, ${identifiant})"]`).style.display = "block";
}
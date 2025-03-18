function validerReponse(event, compteur, idOffre, idMembre) {
    event.preventDefault(); // Empêche la redirection
    const texteReponse = document.getElementById(`texte-reponse-${compteur}`).value.trim();
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
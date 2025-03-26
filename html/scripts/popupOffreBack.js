function validerDate(event, idOffre) {
    event.preventDefault(); 
    const reponseURL = "/utils/date.php";
    fetch(reponseURL, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_offre=${idOffre}`
    })
    .then(response => response.text())
    .then(data => {
        location.reload();  
    })
    .catch(error => console.error("Erreur :", error));
}

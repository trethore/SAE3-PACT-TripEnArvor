function afficherMenu(event, button, compteur) {
    event.stopPropagation();
    const menu = document.getElementById(`popup-menu-${compteur}`);
    document.querySelectorAll(".popup-menu").forEach(m => {
        if (m !== menu) m.style.display = "none";
    });

    if (menu.style.display === "block") {
        menu.style.display = "none";
        return;
    }

    const rect = button.getBoundingClientRect();
    menu.style.top = `${rect.top + window.scrollY - 2}px`;
    menu.style.left = `${rect.left + window.scrollX - 100}px`;
    menu.style.display = "block";
}

function confirmerBlacklister(element, compteur) {
    const idOffre = element.getAttribute("data-id-offre");
    const idMembre = element.getAttribute("data-id-membre");
    document.getElementById("confirmation-popup").style.display = "block";
    document.getElementById("confirmer-blacklister").onclick = function() {
        validerBlacklister(compteur, idOffre, idMembre);
    };
}

function validerBlacklister(compteur, idOffre, idMembre) {
    const blacklistUrl = "/utils/blacklist.php";
    fetch(blacklistUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_offre=${idOffre}&id_membre=${idMembre}`
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("confirmation-popup").style.display = "none";
        location.reload();
    })
    .catch(error => console.error("Erreur :", error));
}

function annulerBlacklister() {
    document.getElementById("confirmation-popup").style.display = "none";
}

document.addEventListener("click", function() {
    document.querySelectorAll(".popup-menu").forEach(menu => {
        menu.style.display = "none";
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const id_offre = document.querySelector("#header").getAttribute("data-id-offre");
    setInterval(() => {
        console.log("setInterval a été déclenché");
        console.log(id_offre);
        fetch('/utils/checkJetons.php', {
            method: "POST", 
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `id_offre=${id_offre}`
        })
        .then(response => response.text())
        .then(data => {
            location.reload();
        })
        .catch(error => console.error("Erreur :", error));
    }, 30000); // Toutes les minutes
});

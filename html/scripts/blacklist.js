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

// BLACKLISTAGE
function confirmerBlacklister(element, identifiant) {
    const idOffre = element.getAttribute("data-id-offre");
    const idMembre = element.getAttribute("data-id-membre");
    document.getElementById(`confirmation-popup-${identifiant}`).style.display = "block";
    document.getElementById(`confirmer-blacklister-${identifiant}`).onclick = function() {
        validerBlacklister(identifiant, idOffre, idMembre);
    };
}

function validerBlacklister(identifiant, idOffre, idMembre) {
    const blacklistUrl = "/utils/blacklist.php";
    fetch(blacklistUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_offre=${idOffre}&id_membre=${idMembre}`
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById(`confirmation-popup-${identifiant}`).style.display = "none";
        location.reload();
    })
    .catch(error => console.error("Erreur :", error));
}

function annulerBlacklister(identifiant) {
    document.getElementById(`confirmation-popup-${identifiant}`).style.display = "none";
}

document.addEventListener("click", function() {
    document.querySelectorAll(".popup-menu").forEach(menu => {
        menu.style.display = "none";
    });
});

// SIGNALEMENT
function confirmerSignaler(element, identifiant) {
    const idOffre = element.getAttribute("data-id-offre");
    const idSignale = element.getAttribute("data-id-signale");
    const idSignalant = element.getAttribute("data-id-signalant");
    document.getElementById(`confirmation-popup-signaler-${identifiant}`).style.display = "block";
    document.getElementById(`confirmer-signaler-${identifiant}`).onclick = function() {
        validerSignaler(identifiant, idOffre, idSignale, idSignalant);
    };
}

function validerSignaler(identifiant, idOffre, idSignale, idSignalant) {
    var selectedRadio = document.querySelector('input[name="motif"]:checked');
    var motif = selectedRadio.value;
    const signalerUrl = "/utils/signaler.php";
    fetch(signalerUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_offre=${idOffre}&id_signale=${idSignale}&id_signalant=${idSignalant}&motif=${motif}`
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById(`confirmation-popup-signaler-${identifiant}`).style.display = "none";
        location.reload();
    })
    .catch(error => console.error("Erreur :", error));
}

function annulerSignaler(identifiant) {
    document.getElementById(`confirmation-popup-signaler-${identifiant}`).style.display = "none";
}

// UPDATE JETONS SI DATE PASSÉE
document.addEventListener("DOMContentLoaded", function () {
    const id_offre = document.querySelector("#header").getAttribute("data-id-offre");
    fetch('/utils/checkJetons.php', {
        method: "POST", 
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `id_offre=${id_offre}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && !isJetonUpdated) {
            location.reload();
        }
    })
    .catch(error => console.error("Erreur :", error));
});

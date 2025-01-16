function toggleButtonText() {
    const button = document.getElementById('boutonMHL-MEL');
    if (button.textContent === "Mettre hors ligne") {
        button.textContent = "Mettre en ligne";
        alert("L'offre a été mise hors ligne.");
    } else {
        button.textContent = "Mettre hors ligne";
        alert("L'offre a été mise en ligne.");
    }
}
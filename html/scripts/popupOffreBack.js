document.addEventListener("DOMContentLoaded", function() {

    let confirmDiv = document.getElementById("confirm");
    let finalDiv = document.getElementById("final");

    function showConfirm() {
        confirmDiv.style.display = "block";
        let header = document.getElementById('header');
        header.style.filter = "blur(10px)";
        let body = document.getElementById('body');
        body.style.filter = "blur(10px)";
        let footer = document.getElementById('footer');
        footer.style.filter = "blur(10px)";
        let bouton1 = document.getElementById('bouton1');
        bouton1.style.filter = "blur(10px)";
        let bouton2 = document.getElementById('bouton2');
        bouton2.style.filter = "blur(10px)";
        let popup = document.getElementById('confirm');
        popup.style.filter = "none";
    }

    function showFinal() {
        finalDiv.style.display = "block";
        confirmDiv.style.display = "none";
        popup.style.filter = "none";
    }

    function btnAnnuler() {
        confirmDiv.style.display = "none";
        finalDiv.style.display = "none";
        let header = document.getElementById('header');
        header.style.filter = "blur(0px)";
        let body = document.getElementById('body');
        body.style.filter = "blur(0px)";
        let footer = document.getElementById('footer');
        footer.style.filter = "blur(0px)";
        let bouton1 = document.getElementById('bouton1');
        bouton1.style.filter = "blur(0px)";
        let bouton2 = document.getElementById('bouton2');
        bouton2.style.filter = "blur(0px)";
    }
});
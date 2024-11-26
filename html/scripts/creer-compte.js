document.addEventListener("DOMContentLoaded", function() {
    
    const selectTypeCompte = document.getElementById("type-compte");
    const divEmail = document.getElementById("div-email");
    const divPassword = document.getElementById("div-password");
    const divConfirmPassword = document.getElementById("div-confirm-password");
    const divNameAndFirstName = document.getElementById("div-name-and-first-name");
    const divName = document.getElementById("div-name");
    const divFirstName = document.getElementById("div-first-name");
    const divTel = document.getElementById("div-tel");
    const divPseudo = document.getElementById("div-pseudo");
    const divDenomination = document.getElementById("div-denomination");
    const divAPropos = document.getElementById("div-a-propos");
    const divSiteWeb = document.getElementById("div-site-web");
    const divSiren = document.getElementById("div-siren");
    const divAddress = document.getElementById("div-adresse");
    const divStreet = document.getElementById("div-street");
    const divAddressComplement = document.getElementById("div-address-complement");
    const divCodePostal = document.getElementById("div-code-postal");
    const divCity = document.getElementById("div-city");
    const divCountry = document.getElementById("div-country");
    const submitInput = document.querySelector("input[type=\"submit\"]");

    function setObligatoire(element, obligatoire) {
        element.querySelector("label span").style.display = obligatoire ? "inline" : "none";
        element.querySelector("input, textarea").required = obligatoire;
    }

    selectTypeCompte.addEventListener("input", function() {
        switch (selectTypeCompte.value) {
            case "membre":
                divEmail.style.display = "flex";
                setObligatoire(divEmail, true);
                divPassword.style.display = "flex";
                setObligatoire(divPassword, true);
                divConfirmPassword.style.display = "flex";
                setObligatoire(divConfirmPassword, true);
                divNameAndFirstName.style.display = "flex";
                divName.style.display = "flex";
                setObligatoire(divName, false);
                divFirstName.style.display = "flex";
                setObligatoire(divFirstName, false);
                divTel.style.display = "flex";
                setObligatoire(divTel, false);
                divPseudo.style.display = "flex";
                setObligatoire(divPseudo, true);
                divDenomination.style.display = "none";
                setObligatoire(divDenomination, false);
                divAPropos.style.display = "none";
                setObligatoire(divAPropos, false);
                divSiteWeb.style.display = "none";
                setObligatoire(divSiteWeb, false);
                divSiren.style.display = "none";
                setObligatoire(divSiren, false);
                divAddress.style.display = "none";
                setObligatoire(divAddress, false);
                setObligatoire(divStreet, false);
                setObligatoire(divAddressComplement, false);
                setObligatoire(divCodePostal, false);
                setObligatoire(divCity, false);
                setObligatoire(divCountry, false);
                submitInput.disabled = false;
                submitInput.style = "--couleur-bouton-creer-compte: var(--violet);"
                break;
            case "pro-publique":
                divEmail.style.display = "flex";
                setObligatoire(divEmail, true);
                divPassword.style.display = "flex";
                setObligatoire(divPassword, true);
                divConfirmPassword.style.display = "flex";
                setObligatoire(divConfirmPassword, true);
                divNameAndFirstName.style.display = "flex";
                divName.style.display = "flex";
                setObligatoire(divName, true);
                divFirstName.style.display = "flex";
                setObligatoire(divFirstName, true);
                divTel.style.display = "flex";
                setObligatoire(divTel, true);
                divPseudo.style.display = "none";
                setObligatoire(divPseudo, false);
                divDenomination.style.display = "flex";
                setObligatoire(divDenomination, true);
                divAPropos.style.display = "flex";
                setObligatoire(divAPropos, true);
                divSiteWeb.style.display = "flex";
                setObligatoire(divSiteWeb, true);
                divSiren.style.display = "none";
                setObligatoire(divSiren, false);
                divAddress.style.display = "flex";
                setObligatoire(divAddress, true);
                setObligatoire(divStreet, true);
                setObligatoire(divAddressComplement, false);
                setObligatoire(divCodePostal, true);
                setObligatoire(divCity, true);
                setObligatoire(divCountry, true);
                submitInput.disabled = false;
                submitInput.style = "--couleur-bouton-creer-compte: var(--orange-principale);"
                break;
            case "pro-privée":
                divEmail.style.display = "flex";
                setObligatoire(divEmail, true);
                divPassword.style.display = "flex";
                setObligatoire(divPassword, true);
                divConfirmPassword.style.display = "flex";
                setObligatoire(divConfirmPassword, true);
                divNameAndFirstName.style.display = "flex";
                divName.style.display = "flex";
                setObligatoire(divName, true);
                divFirstName.style.display = "flex";
                setObligatoire(divFirstName, true);
                divTel.style.display = "flex";
                setObligatoire(divTel, true);
                divPseudo.style.display = "none";
                setObligatoire(divPseudo, false);
                divDenomination.style.display = "flex";
                setObligatoire(divDenomination, true);
                divAPropos.style.display = "flex";
                setObligatoire(divAPropos, true);
                divSiteWeb.style.display = "flex";
                setObligatoire(divSiteWeb, true);
                divSiren.style.display = "flex";
                setObligatoire(divSiren, true);
                divAddress.style.display = "flex";
                setObligatoire(divAddress, true);
                setObligatoire(divStreet, true);
                setObligatoire(divAddressComplement, false);
                setObligatoire(divCodePostal, true);
                setObligatoire(divCity, true);
                setObligatoire(divCountry, true);
                submitInput.disabled = false;
                submitInput.style = "--couleur-bouton-creer-compte: var(--orange-principale);"
                break;
            case "":
            default:
                divEmail.style.display = "none";
                divPassword.style.display = "none";
                divConfirmPassword.style.display = "none";
                divNameAndFirstName.style.display = "none";
                divName.style.display = "none";
                divFirstName.style.display = "none";
                divTel.style.display = "none";
                divPseudo.style.display = "none";
                divDenomination.style.display = "none";
                divAPropos.style.display = "none";
                divSiteWeb.style.display = "none";
                divSiren.style.display = "none";
                divAddress.style.display = "none";
                submitInput.disabled = true;
                break;
        }
    });
});

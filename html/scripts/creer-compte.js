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

    const tousLesElements = [
        divEmail,            divPassword, divConfirmPassword,
        divNameAndFirstName, divName,     divFirstName,
        divTel,              divPseudo,   divDenomination,
        divAPropos,          divSiteWeb,  divSiren,
        divAddress,          divStreet,   divAddressComplement,
        divCodePostal,       divCity,     divCountry
    ];

    const elementMembre = [
        divEmail,  divPassword, divConfirmPassword,
        divPseudo, divName,     divFirstName,
        divTel
    ];
    const elementObligatoireMembre = [
        divEmail, divPassword, divConfirmPassword,
        divPseudo
    ];

    const elementProPublique = [
        divEmail,             divPassword,     divConfirmPassword,
        divNameAndFirstName,  divName,         divFirstName,
        divTel,               divDenomination, divAPropos,
        divSiteWeb,           divAddress,      divStreet,
        divAddressComplement, divCodePostal,  divCity,
        divCountry
    ];
    const elementObligatoireProPublique = [
        divEmail,        divPassword,   divConfirmPassword,
        divName,         divFirstName,  divTel,
        divDenomination, divAPropos,    divSiteWeb,
        divStreet,       divCodePostal, divCity,
        divCountry
    ];

    const elementProPrive = [
        divEmail,             divPassword,          divConfirmPassword,
        divNameAndFirstName,  divName,              divFirstName,
        divTel,               divDenomination,      divAPropos,
        divSiteWeb,           divSiren,             divAddress,
        divStreet,            divAddressComplement, divCodePostal,
        divCity,divCountry
    ];
    const elementObligatoireProPrive = [
        divEmail,           divPassword,    divConfirmPassword,
        divName,            divFirstName,   divTel,
        divDenomination,    divAPropos,     divSiteWeb,
        divSiren,           divStreet,      divCodePostal,
        divCity,            divCountry
    ];

    function setObligatoire(element, obligatoire) {
        element.querySelector("label span").style.display = obligatoire ? "inline" : "none";
        element.querySelector("input, textarea").required = obligatoire;
    }

    function showFieldsAndMakeItRequiredIfNecessary(listOfAllElements, listOfElementToShow, listOfRequiredElements) {
        for (const element of listOfAllElements) {
            if (listOfElementToShow.includes(element)) {
                element.style.display = "flex";
                if (listOfRequiredElements.includes(element)) {
                    setObligatoire(element, true);
                } else {
                    setObligatoire(element, false);
                }
            } else {
                element.style.display = "none";
                setObligatoire(element, false);
            }
        }
    }

    selectTypeCompte.addEventListener("input", function() {
        switch (selectTypeCompte.value) {
            case "membre":
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, elementMembre, elementObligatoireMembre);
                submitInput.disabled = false;
                submitInput.style = "--couleur-bouton-creer-compte: var(--violet);"
                break;
            case "pro-publique":
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, elementProPublique, elementObligatoireProPublique);
                submitInput.disabled = false;
                submitInput.style = "--couleur-bouton-creer-compte: var(--orange-principale);"
                break;
            case "pro-privé":
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, elementProPrive, elementObligatoireProPrive);
                submitInput.disabled = false;
                submitInput.style = "--couleur-bouton-creer-compte: var(--orange-principale);"
                break;
            case "":
            default:
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, [], []);
                submitInput.disabled = true;
                break;
        }
    });
});

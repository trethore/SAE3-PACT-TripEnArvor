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
    const inputCgu = document.getElementById("cgu");
    const submitInput = document.querySelector("input[type=\"submit\"]");

    const tousLesElements = [
        divEmail,   divPassword,          divConfirmPassword,
        divName,    divFirstName,         divTel,
        divPseudo,  divDenomination,      divAPropos,
        divSiteWeb, divSiren,             divAddress,
        divStreet,  divAddressComplement, divCodePostal,
        divCity,    divCountry
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

    function setRequired(element, required) {
        element.querySelector("label span").style.display = required ? "inline" : "none";
        element.querySelector("input, textarea").required = required;
        if (!required) {
            showRequiredMessage(element, false);
        }
    }

    function showFieldsAndMakeItRequiredIfNecessary(listOfAllElements, listOfElementToShow, listOfRequiredElements) {
        for (const element of listOfAllElements) {
            if (listOfElementToShow.includes(element)) {
                element.style.display = "flex";
                if (listOfRequiredElements.includes(element)) {
                    setRequired(element, true);
                } else {
                    setRequired(element, false);
                }
            } else {
                element.style.display = "none";
                setRequired(element, false);
            }
        }
    }

    function showRequiredMessage(divElement, isRequired) {
        if (isRequired) {
            divElement.querySelector(".required-message").style.display = "inline";
            divElement.querySelector("input, textarea").style.border = "1px solid red";
        } else {
            divElement.querySelector(".required-message").style.display = "none";
            divElement.querySelector("input, textarea").style.border = "";
        }
    }

    function isRequired(element) {
        return element.querySelector("input, textarea").required
    }

    selectTypeCompte.addEventListener("input", function() {
        switch (selectTypeCompte.value) {
            case "membre":
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, elementMembre, elementObligatoireMembre);
                if (inputCgu.checked)
                    submitInput.disabled = false;
                else
                    submitInput.disabled = true;
                submitInput.style = "--couleur-bouton-creer-compte: var(--violet);"
                break;
            case "pro-publique":
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, elementProPublique, elementObligatoireProPublique);
                if (inputCgu.checked)
                    submitInput.disabled = false;
                else
                    submitInput.disabled = true;
                submitInput.style = "--couleur-bouton-creer-compte: var(--orange-principale);"
                break;
            case "pro-privé":
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, elementProPrive, elementObligatoireProPrive);
                if (inputCgu.checked)
                    submitInput.disabled = false;
                else
                    submitInput.disabled = true;
                submitInput.style = "--couleur-bouton-creer-compte: var(--orange-principale);"
                break;
            case "":
            default:
                showFieldsAndMakeItRequiredIfNecessary(tousLesElements, [], []);
                submitInput.disabled = true;
                break;
        }
    });

    inputCgu.addEventListener("change", function() {
        if (inputCgu.checked && (selectTypeCompte.value !== "")) {
            submitInput.disabled = false;
        } else {
            submitInput.disabled = true;
        }
    });

    for (const divElement of tousLesElements) {
        const element = divElement.querySelector("input, textarea");
        element.addEventListener("blur", function() {
            if (element.validity.valueMissing) {
                showRequiredMessage(divElement, isRequired(divElement));
            } else {
                showRequiredMessage(divElement, false);
            }
        })
    }
});

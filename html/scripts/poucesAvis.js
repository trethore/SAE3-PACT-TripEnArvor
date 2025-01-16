const pouceHauts = document.querySelectorAll('.pouceHaut');
const pouceBas = document.querySelectorAll('.pouceBas');

const images = {
    haut: {
        actif: '/images/universel/icones/pouce-up-hover.png',
        inactif: '/images/universel/icones/pouce-up.png',
    },
    bas: {
        actif: '/images/universel/icones/pouce-down-hover.png',
        inactif: '/images/universel/icones/pouce-down.png',
    },
};
    
let etatsPouces = {};

pouceHauts.forEach((pouceHaut) => {

    pouceHaut.addEventListener('click', () => {

        const avisId = pouceHaut.getAttribute('data-id');
        const nbPouceHaut = pouceHaut.previousElementSibling;
        const nbPouceBas = pouceHaut.nextElementSibling.nextElementSibling;

        if (!etatsPouces[avisId]) etatsPouces[avisId] = null;

        let currentHaut = parseInt(nbPouceHaut.textContent);
        let currentBas = parseInt(nbPouceBas.textContent);

        if (etatsPouces[avisId] === "haut") {
            nbPouceHaut.textContent = currentHaut - 1;
            pouceHaut.src = images.haut.inactif;
            etatsPouces[avisId] = null;
        } else {
            nbPouceHaut.textContent = currentHaut + 1;
            pouceHaut.src = images.haut.actif;
            if (etatsPouces[avisId] === "bas") {
                nbPouceBas.textContent = currentBas - 1;
                pouceBas.src = images.bas.inactif;
            }
            etatsPouces[avisId] = "haut";
        }
    });
});

pouceBas.forEach((pouceBas) => {

    pouceBas.addEventListener('click', () => {

        const avisId = pouceBas.getAttribute('data-id');
        const nbPouceHaut = pouceBas.previousElementSibling.previousElementSibling;
        const nbPouceBas = pouceBas.previousElementSibling;

        if (!etatsPouces[avisId]) etatsPouces[avisId] = null;

        let currentHaut = parseInt(nbPouceHaut.textContent);
        let currentBas = parseInt(nbPouceBas.textContent);

        if (etatsPouces[avisId] === "bas") {
            nbPouceBas.textContent = currentBas - 1;
            pouceBas.src = images.bas.inactif;
            etatsPouces[avisId] = null;
        } else {
            nbPouceBas.textContent = currentBas + 1;
            pouceBas.src = images.bas.actif;
            if (etatsPouces[avisId] === "haut") {
                nbPouceHaut.textContent = currentHaut - 1;
                pouceHaut.src = images.haut.inactif;
            }
            etatsPouces[avisId] = "bas";
        }
    });
});
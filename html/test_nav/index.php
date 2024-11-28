<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/styleguide.css" />
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="/style/style_HFF.css">
    <link rel="stylesheet" href="/style/style_navPhone.css" />
    <title>Liste de vos offres</title>
</head>

<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search">
                <img class="cherchero" src="/images/universel/icones/chercher.png" />
            </button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche..." />
            <datalist id="cont">
                <?php foreach ($offres as $offre): ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <a href="/back/liste-back">
            <img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" />
        </a>
        <a href="/back/se-connecter">
            <img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" />
        </a>
    </header>

    <main></main>

    <footer>
        <div class="footer-top">
            <div class="footer-top-left">
                <span class="footer-subtitle">P.A.C.T</span>
                <span class="footer-title">TripEnArmor</span>
            </div>
            <div class="footer-top-right">
                <span class="footer-connect">Restons connectés !</span>
                <div class="social-icons">
                    <a href="https://x.com/?locale=fr">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/x.png');"></div>
                    </a>
                    <a href="https://www.facebook.com/?locale=fr_FR">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/facebook.png');"></div>
                    </a>
                    <a href="https://www.youtube.com/">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/youtube.png');"></div>
                    </a>
                    <a href="https://www.instagram.com/">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/instagram.png');"></div>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            Politique de confidentialité - Politique RGPD - 
            <a href="mention_legal.html">Mentions légales</a> - Plan du site -
            Conditions générales - © Redden's, Inc.
        </div>
    </footer>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const inputSearch = document.querySelector(".input-search");
            const datalist = document.querySelector("#cont");

            // Test 1: Vérifier si le champ input et datalist existent
            if (!inputSearch) {
                console.error("ERREUR : L'élément input-search est introuvable !");
                return;
            }
            console.log("Test 1 OK : L'élément input-search a été trouvé.");

            if (!datalist) {
                console.error("ERREUR : L'élément datalist est introuvable !");
                return;
            }
            console.log("Test 2 OK : L'élément datalist a été trouvé.");

            // Test 2: Vérifier si des options existent dans le datalist
            const options = Array.from(datalist.options);
            if (options.length === 0) {
                console.warn("ALERTE : Aucune option trouvée dans le datalist !");
            } else {
                console.log(`Test 3 OK : ${options.length} option(s) trouvée(s) dans le datalist.`);
            }

            // Ajouter un écouteur sur l'input
            inputSearch.addEventListener("change", () => {
                console.log("Événement 'change' déclenché sur le champ input.");

                // Test 3: Vérifier si une valeur correspondante a été sélectionnée
                const selectedOption = options.find(option => option.value === inputSearch.value);

                if (!selectedOption) {
                    console.warn("Aucune option ne correspond à la saisie :", inputSearch.value);
                    return;
                }
                console.log("Test 4 OK : Une option correspondante a été trouvée :", selectedOption.value);

                // Test 4: Vérifier si l'attribut data-id est défini
                const idOffre = selectedOption.getAttribute("data-id");
                if (!idOffre) {
                    console.error("ERREUR : Aucun ID trouvé pour l'option sélectionnée !");
                    return;
                }
                console.log("Test 5 OK : ID trouvé pour l'option :", idOffre);

                // Redirection (Test final)
                console.log("Redirection vers :", `/back/consulter-offre/index.php?id=${idOffre}`);
                window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
            });
        });
    </script>
</body>
</html>

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

            inputSearch.addEventListener("change", () => {
                const selectedOption = Array.from(datalist.options).find(
                    option => option.value === inputSearch.value
                );

                if (selectedOption) {
                    const idOffre = selectedOption.getAttribute("data-id");
                    if (idOffre) {
                        window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
                    } else {
                        console.log("Aucune correspondance trouvée.");
                    }
                }
            });
        });
    </script>
</body>
</html>

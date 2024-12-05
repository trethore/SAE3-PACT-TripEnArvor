<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/offres-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/auth-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style-accueil-front.css">
    <link rel="stylesheet" href="/style/style_HFF.css">
    <link rel="stylesheet" href="/style/styleguide.css">
    <title>Accueil</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/front/consulter-offres"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>

    <!-- Conteneur principal -->
    <main>
        <h2>Offres à la une</h2>

        <?php 

        $ids = getIdALaUne();
        foreach ($ids as $key => $offre) {
            $ids[$key]['titre'] = getOffre($offre["id_offre"])["titre"];
            $ids[$key]['note'] = getNoteMoyenne($offre["id_offre"]);
        }
        echo "<pre>";
        print_r($ids);
        echo "</pre>";
        ?>

        <section>
            <div class="carousel">
                <div class="carousel-images">
                    <?php foreach ($ids as $offre) { 
                        echo "<pre>";
                        echo $offre["id_offre"];
                        echo $offre["titre"];
                        echo $offre["note"];
                        echo "</pre>"; ?>
                        <a href="/front/consulter-offre/index.php?id=<?php echo $offre["id_offre"]; ?>">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre["id_offre"])) ?>" alt="Image" data-titre="<?php echo htmlentities($offre['titre']); ?>" data-note="<?php echo htmlentities($offre["note"]); ?>">
                        </a>
                    <?php } ?>
                </div>
                <div>
                    <div class="arrow-left">
                        <img src="/images/universel/icones/fleche-gauche.png" alt="Flèche navigation" class="prev">
                    </div>
                    <div class="arrow-right">
                        <img src="/images/universel/icones/fleche-droite.png" alt="Flèche navigation" class="next">
                    </div>
                </div>
                <p class="titre" id="carousel-titre"></p>
            </div>
        </section>

        <h1>Découvrir la Liste des Offres Disponibles</h1>

        <!--
        <h2>Nouveautés</h2>
        <article></article>

        <h2>Consultés Récemment</h2>
        <article></article>
        -->
    </main>

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


        <!-- Barre en bas du footer incluse ici -->

        </div>
        <div class="footer-bottom">
        Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        Conditions générales - ©
        Redden's, Inc.
        </div>
    </footer>

    <script>
        const images = document.querySelector('.carousel-images');
        const prevButton = document.querySelector('.prev');
        const nextButton = document.querySelector('.next');
        const titreElement = document.querySelector('#carousel-titre');

        let currentIndex = 0;

        updateCarousel();

        // Gestion du clic sur le bouton "Suivant"
        nextButton.addEventListener('click', () => {
            currentIndex++;
            if (currentIndex >= images.children.length) {
                currentIndex = 0;
            }
            updateCarousel();
        });

        // Gestion du clic sur le bouton "Précédent"
        prevButton.addEventListener('click', () => {
            currentIndex--;
            if (currentIndex < 0) {
                currentIndex = images.children.length - 1;
            }
            updateCarousel();
        });

        function updateCarousel() {
            const width = images.clientWidth;
            images.style.transform = `translateX(-${currentIndex * width}px)`;

            const currentImage = images.children[currentIndex];
            const titre = currentImage.dataset.titre;
            const note = parseFloat(currentImage.dataset.note);

            let starsHTML = '';
            if (note == NaN) {
                starsHTML = "Pas d'avis disponibles.";
            } else {
                for (let i = 1; i <= 5; i++) {
                    if (i <= note) {
                        starsHTML += '<img src="/images/frontOffice/etoile-pleine.png" alt="Star pleine">';
                    } else {
                        starsHTML += '<img src="/images/frontOffice/etoile-vide.png" alt="Star vide">';
                    }
                }
            }

            titreElement.innerHTML = `
                ${titre}
                ${starsHTML}
            `;
        }
    </script>
</body>
</html>

<?php $dbh = null; ?>
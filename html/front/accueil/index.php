<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

if (!isset($_SESSION)) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="/style/style.css">
    <title>Accueil</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>

<body class="front accueil">
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');

    try {
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();
        $stmt = $dbh->prepare('SELECT titre, id_offre FROM sae._offre');
        $stmt->execute();
        $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
        $dbh = null;
    } catch (PDOException $e) {
        echo "Erreur lors de la récupération des titres : " . $e->getMessage();
    }
    ?>

    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>

        </div>
        <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const inputSearch = document.querySelector(".input-search");
                const datalist = document.querySelector("#cont");

                // Événement sur le champ de recherche
                inputSearch.addEventListener("input", () => {
                    // Rechercher l'option correspondante dans le datalist
                    const selectedOption = Array.from(datalist.options).find(
                        option => option.value === inputSearch.value
                    );

                    if (selectedOption) {
                        const idOffre = selectedOption.getAttribute("data-id");

                        //console.log("Option sélectionnée :", selectedOption.value, "ID:", idOffre);

                        // Rediriger si un ID valide est trouvé
                        if (idOffre) {
                            window.location.href = `/front/consulter-offre/index.php?id=${idOffre}`;
                        }
                    }
                });

                // Debugging pour vérifier les options disponibles
                const options = Array.from(datalist.options).map(option => ({
                    value: option.value,
                    id: option.getAttribute("data-id")
                }));
                //console.log("Options disponibles dans le datalist :", options);
            });
        </script>
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

        $ids_nouv = getIdOffresRecentes();
        foreach ($ids_nouv as $key => $offre_nouv) {
            $ids_nouv[$key]['titre'] = getOffre($offre_nouv["id_offre"])["titre"];
            $ids_nouv[$key]['note'] = getNoteMoyenne($offre_nouv["id_offre"]);
        }

        $ids_consulte = getConsultedOffers();
        foreach ($ids_consulte as $key => $offre_consulte) {
            $ids_consulte[$key]['titre'] = getOffre($offre_consulte["id_offre"])["titre"];
            $ids_consulte[$key]['note'] = getNoteMoyenne($offre_consulte["id_offre"]);
        }

        ?>

        <section>
            <div class="carousel">
                <div class="carousel-images">
                    <?php foreach ($ids as $offre) {
                        $id = $offre["id_offre"];
                        $titre = $offre["titre"];
                        $note = $offre["note"]; ?>
                        <a href="/front/consulter-offre/index.php?id=<?php echo $offre["id_offre"]; ?>">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre["id_offre"])) ?>" alt="Image" data-titre="<?php echo htmlentities($titre); ?>" data-note="<?php echo htmlentities($note); ?>">
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

        <h1><a href="/front/consulter-offres">Découvrir la Liste des Offres Disponibles</a></h1>

        <h2>Nouveautés</h2>

        <section>
            <div class="carousel">
                <div class="carousel-images">
                    <?php foreach ($ids_nouv as $offre_nouv) {
                        $id = $offre_nouv["id_offre"];
                        $titre = $offre_nouv["titre"];
                        $note = $offre_nouv["note"]; ?>

                        <a href="/front/consulter-offre/index.php?id=<?php echo $offre_nouv["id_offre"]; ?>">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre_nouv["id_offre"])) ?>" alt="Image" data-titre="<?php echo htmlentities($titre); ?>" data-note="<?php echo htmlentities($note); ?>">
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

        <h2>Consultés Récemment</h2>

        <section>
            <div class="carousel">
                <div class="carousel-images">
                <?php foreach ($ids_consulte as $offre_consulte) {
                        $id = $offre_consulte["id_offre"];
                        $titre = $offre_consulte["titre"];
                        $note = $offre_consulte["note"]; ?>

                        <a href="/front/consulter-offre/index.php?id=<?php echo $offre_consulte["id_offre"]; ?>">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre_consulte["id_offre"])) ?>" alt="Image" data-titre="<?php echo htmlentities($titre); ?>" data-note="<?php echo htmlentities($note); ?>">
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
            <a href="/confidentialité/" target="_blank">Politique de confidentialité</a> - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
            <a href="/cgu/" target="_blank">Conditions générales</a> - ©
            Redden's, Inc.
        </div>
    </footer>

    <script>
        document.querySelectorAll('.carousel').forEach(carousel => {
            const images = carousel.querySelector('.carousel-images');
            const prevButton = carousel.querySelector('.prev');
            const nextButton = carousel.querySelector('.next');
            const titreElement = carousel.querySelector('.titre');

            let currentIndex = 0;

            function updateCarousel() {
                const width = images.clientWidth;
                images.style.transform = `translateX(-${currentIndex * width}px)`;

                const currentAnchor = images.children[currentIndex];
                const imgElement = currentAnchor.querySelector('img');
                const titre = imgElement.dataset.titre || "Titre indisponible";
                const note = parseFloat(imgElement.dataset.note) || 0;

                let starsHTML = '';
                if (note === 0) {
                    starsHTML = "Pas d'avis disponibles.";
                } else {
                    etoilesPleines = Math.floor(note);
                    demiEtoile = (note - etoilesPleines) == 0.5 ? 1 : 0;
                    etoilesVides = 5 - etoilesPleines - demiEtoile;

                    for (i = 0; i < etoilesPleines; i++) {
                        starsHTML += '<img src="/images/frontOffice/etoile-pleine.png" alt="Star pleine">';
                    }

                    if (demiEtoile) {
                        starsHTML += '<img src="/images/frontOffice/etoile-moitie.png" alt="Star moitie">';
                    }

                    for (i = 0; i < etoilesVides; i++) {
                        starsHTML += '<img src="/images/frontOffice/etoile-vide.png" alt="Star vide">';
                    }
                }

                titreElement.innerHTML = `${titre} ${starsHTML}`;
            }

            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex > 0) ? currentIndex - 1 : images.children.length - 1;
                updateCarousel();
            });

            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % images.children.length;
                updateCarousel();
            });

            updateCarousel();
        });
    </script>
    <div class="telephone-nav">
        <div class="nav-content">
            <a href="/front/accueil">
                <div class="btOn">
                    <img width="400" height="400" src="/images/frontOffice/icones/accueil.png">
                </div>
            </a>
            <a href="/front/consulter-offres">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/chercher.png">
                </div>
            </a>
            <a href="/front/mon-compte">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/utilisateur.png">
                </div>
            </a>
        </div>
    </div>
</body>


</html>

<?php $dbh = null; ?>
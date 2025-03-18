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
    <script src="/scripts/carousel.js"></script>
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
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" alt="Logo de la PACT">
        <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" alt="Rechercher"></button>
            <input type="text" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>

        </div>
        <a href="/front/accueil"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" alt="Accueil"></a>
        <a href="/front/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" alt="Mon compte"></a>
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
            <div class="carousel les_carousels">
                <div class="carousel-slides">
                    <?php
                    foreach ($ids as $offre) {
                    ?>
                        <a href="/front/consulter-offre/index.php?id=<?php echo ($offre['id_offre']); ?>" class="slide">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre["id_offre"])) ?>" alt="Photo de l'offre">
                            <div>
                                <span><?php echo ($offre['titre']); ?></span>
                                <div>
                                    <?php
                                    $noteMoyenne = getNoteMoyenne($offre["id_offre"]);

                                    if ($noteMoyenne !== null) {

                                        $etoilesPleines = floor($noteMoyenne);
                                        $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                                        $etoilesVides = 5 - $etoilesPleines - $demiEtoile;

                                        for ($i = 0; $i < $etoilesPleines; $i++) {
                                    ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-pleine.png" alt="Étoile jaune">
                                        <?php
                                        }
                                        if ($demiEtoile) {
                                        ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-moitie.png" alt="Demi étoile">
                                        <?php
                                        }
                                        for ($i = 0; $i < $etoilesVides; $i++) {
                                        ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-vide.png" alt="Étoile grise">
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <span>Pas d'avis</span>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    <?php
                    }
                    ?>
                </div>
                <button type="button" class="prev-slide"><img src="/images/universel/icones/fleche-gauche.png" alt="←"></button>
                <button type="button" class="next-slide"><img src="/images/universel/icones/fleche-droite.png" alt="→"></button>
            </div>
        </section>

        <h1><a href="/front/consulter-offres">Découvrir la Liste des Offres Disponibles</a></h1>

        <h2>Nouveautés</h2>

        <section>
            <div class="carousel">
                <div class="carousel-slides">
                    <?php
                    foreach ($ids_nouv as $offre) {
                    ?>
                        <a href="/front/consulter-offre/index.php?id=<?php echo ($offre['id_offre']); ?>" class="slide">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre["id_offre"])) ?>" alt="Photo de l'offre">
                            <div>
                                <span><?php echo ($offre['titre']); ?></span>
                                <div>
                                    <?php
                                    $noteMoyenne = getNoteMoyenne($offre["id_offre"]);

                                    if ($noteMoyenne !== null) {

                                        $etoilesPleines = floor($noteMoyenne);
                                        $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                                        $etoilesVides = 5 - $etoilesPleines - $demiEtoile;

                                        for ($i = 0; $i < $etoilesPleines; $i++) {
                                    ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-pleine.png" alt="Étoile jaune">
                                        <?php
                                        }
                                        if ($demiEtoile) {
                                        ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-moitie.png" alt="Demi étoile">
                                        <?php
                                        }
                                        for ($i = 0; $i < $etoilesVides; $i++) {
                                        ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-vide.png" alt="Étoile grise">
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <span>Pas d'avis</span>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    <?php
                    }
                    ?>
                </div>
                <button type="button" class="prev-slide"><img src="/images/universel/icones/fleche-gauche.png" alt="←"></button>
                <button type="button" class="next-slide"><img src="/images/universel/icones/fleche-droite.png" alt="→"></button>
            </div>

        </section>

        <?php
            if (count($ids_consulte) != 0) {
        ?>
        <h2>Consultés Récemment</h2>

        <section>
            <div class="carousel">
                <div class="carousel-slides">
                    <?php
                    foreach ($ids_consulte as $offre) {
                    ?>
                        <a href="/front/consulter-offre/index.php?id=<?php echo ($offre['id_offre']); ?>" class="slide">
                            <img src="/images/universel/photos/<?php echo htmlentities(getFirstIMG($offre["id_offre"])) ?>" alt="Photo de l'offre">
                            <div>
                                <span><?php echo ($offre['titre']); ?></span>
                                <div>
                                    <?php
                                    $noteMoyenne = getNoteMoyenne($offre["id_offre"]);

                                    if ($noteMoyenne !== null) {

                                        $etoilesPleines = floor($noteMoyenne);
                                        $demiEtoile = ($noteMoyenne - $etoilesPleines) == 0.5 ? 1 : 0;
                                        $etoilesVides = 5 - $etoilesPleines - $demiEtoile;

                                        for ($i = 0; $i < $etoilesPleines; $i++) {
                                    ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-pleine.png" alt="Étoile jaune">
                                        <?php
                                        }
                                        if ($demiEtoile) {
                                        ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-moitie.png" alt="Demi étoile">
                                        <?php
                                        }
                                        for ($i = 0; $i < $etoilesVides; $i++) {
                                        ?>
                                            <img class="etoile" src="/images/frontOffice/etoile-vide.png" alt="Étoile grise">
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <span>Pas d'avis</span>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    <?php
                    }
                    ?>
                </div>
                <button type="button" class="prev-slide"><img src="/images/universel/icones/fleche-gauche.png" alt="←"></button>
                <button type="button" class="next-slide"><img src="/images/universel/icones/fleche-droite.png" alt="→"></button>
            </div>
        </section>
        <?php
            }
        ?>
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
            <a href="../../droit/CGU-1.pdf">Conditions Générales d'Utilisation</a> - <a href="../../droit/CGV.pdf">Conditions Générales de Vente</a> - <a href="../../droit/Mentions legales.pdf">Mentions légales</a> - ©Redden's, Inc.
        </div>
    </footer>
    <div class="telephone-nav">
        <div class="nav-content">
            <a href="/front/accueil">
                <div class="btOn">
                    <img width="400" height="400" src="/images/frontOffice/icones/accueil.png" alt="Accueil">
                </div>
            </a>
            <a href="/front/consulter-offres">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/chercher.png" alt="Rechercher">
                </div>
            </a>
            <a href="/front/mon-compte">
                <div class="btOff">
                    <img width="400" height="400" src="/images/frontOffice/icones/utilisateur.png" alt="Mon compte">
                </div>
            </a>
        </div>
    </div>
</body>


</html>

<?php $dbh = null; ?>
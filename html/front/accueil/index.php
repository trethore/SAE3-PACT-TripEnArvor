<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
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
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">
</head>
<body>
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
    <div class="text-wrapper-17"><a href="/front/consulter-offres">PACT Pro</a></div>
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
    <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
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
                        // TD passer du back au front quand fini
                        window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
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

            // Get the current `<a>` element
            const currentAnchor = images.children[currentIndex];
            // Find the `<img>` inside the `<a>`
            const imgElement = currentAnchor.querySelector('img');

            // Extract data attributes from the `<img>`
            const titre = imgElement.dataset.titre || "Titre indisponible";
            const note = parseFloat(imgElement.dataset.note) || 0;

            console.log(`Titre : ${titre}, Note : ${note}`);

            // Generate the stars HTML
            let starsHTML = '';
            if (note === 0) {
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

            // Update the carousel title and stars
            titreElement.innerHTML = `
                ${titre}
                ${starsHTML}
            `;
        }
    </script>
</body>
</html>

<?php $dbh = null; ?>
<?php

include('../../php/connect_params.php');
include('../../utils/offres-utils.php');

// Connexion à la base de données
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    
    $id_offre_cible = isset($_GET['id_offre']) ? intval($_GET['id_offre']) : 1;  // Utilisation de l'ID dans l'URL ou défaut à 1

    // Requête SQL pour récupérer les informations de l'offre
    $reqOffre = "SELECT * FROM _offre";
    $stmt = $dbh->prepare($reqOffre);
    $stmt->execute();
    $offre = $stmt->fetch(PDO::FETCH_ASSOC);

    // Requête SQL pour récupérer les informations de l'adresse de l'offre
    $reqAdresse = "SELECT * FROM _offre NATURAL JOIN _adresse";
    $stmtAdresse = $dbh->prepare($reqAdresse);
    $stmtAdresse->execute();
    $adresse = $stmtAdresse->fetch(PDO::FETCH_ASSOC);

    // Requête SQL pour récupérer les informations du compte du propriétaire de l'offre
    $reqCompte = "SELECT * FROM _offre NATURAL JOIN _compte";
    $stmtCompte = $dbh->prepare($reqCompte);
    $stmtCompte->execute();
    $compte = $stmtCompte->fetch(PDO::FETCH_ASSOC);

    // Requête SQL pour récupérer les informations des jours et horaires d'ouverture de l'offre
    $reqJour = "SELECT * FROM _offre NATURAL JOIN _horaires_du_jour";
    $stmtJour = $dbh->prepare($reqJour);
    $stmtJour->execute();
    $jour = $stmtJour->fetch(PDO::FETCH_ASSOC);

    // Requête SQL pour récupérer les tags de l'offre
    $reqTags = "SELECT nom_tag FROM _offre_possede_tag NATURAL JOIN _tag WHERE id_offre = :id_offre";
    $stmtTags = $dbh->prepare($reqTags);
    $stmtTags->bindParam(':id_offre', $id_offre_cible, PDO::PARAM_INT);
    $stmtTags->execute();
    $tags = $stmtTags->fetchAll(PDO::FETCH_ASSOC);

    // Requête SQL pour récupérer le type de l'offre
    $categorie = getTypeOffre($id_offre_cible);

    //Requête SQL pour récuéprer les images de l'offre
    $reqImages = "SELECT img.lien_fichier FROM sae._image imgJOIN sae._offre_contient_image oci ON img.lien_fichier = oci.id_image WHERE oci.id_offre = :id_offre;";
    $stmtImages = $dbh->prepare($reqImages);
    $stmtIMG->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
    $stmtImages->execute();
    $images = $stmtImages->fetch(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8" />
    <link rel="stylesheet" href="/style/styleguide.css"/>
    <link rel="stylesheet" href="/style/styleHFB.css"/>
    <link rel="stylesheet" href="/style/style-details-offre-pro.css"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>

<body>

    <header id="header">

        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT Pro</div>
        <div class="search-box">
        <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
        <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="index.html"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="index.html"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>

    </header>

    <!-- Pop-up pour la mise hors ligne ou la modification de l'offre -->
    <div class="display-ligne-espace bouton-modifier"> 
        <div>
            <div id="confirm">
                <p>Voulez-vous mettre votre offre hors ligne ?</p>
                <div class="close">
                    <button onclick="showFinal()">Mettre hors ligne</button>
                    <button onclick="btnAnnuler()">Annuler</button>
                </div>
            </div>
            <div id="final">
                <p>Offre hors ligne !<br>Cette offre n'apparait plus</p>
                <button onclick="btnAnnuler()">Fermer</button>
            </div> 
            <button id="bouton1" onclick="showConfirm()">Mettre hors ligne</button>
            <button id="bouton2">Modifier l'offre</button>
        </div>
        <p class="transparent">.</p>
    </div>

    <main id="body">

        <section class="fond-blocs bordure">
            <!-- Affichage du titre de l'offre -->
            <h1><?php echo htmlentities($offre['titre'] ?? 'Titre inconnu'); ?></h1>
            <div class="carousel">
                <div class="carousel-images">
                    <?php foreach ($images as $image) { ?>
                    <img src="/images/universel/photos/<?php echo htmlentities($image['id_image']) ?>" alt="Image">
                    <?php } ?>
                </div>
                <div class="display-ligne-espace">
                    <img src="/images/universel/icones/fleche-gauche-orange.png" alt="Flèche navigation" class="prev">
                    <img src="/images/universel/icones/fleche-droite-orange.png" alt="Flèche navigation" class="next">
                </div>
            </div>


            <div class="display-ligne-espace information-offre">
                <!-- Affichage de la catégorie de l'offre et si cette offre est ouverte ou fermée -->
                <p><em><?php echo htmlentities($categorie ?? 'Catégorie inconnue') . ' - ' . (($offre['ouvert'] ?? 0) ? 'Ouvert' : 'Fermé'); ?></em></p>
                <!-- Affichage de l'adresse de l'offre -->
                <p><?php echo htmlentities($adresse['num_et_nom_de_voie'] . $adresse['complement_adresse'] . ', ' . $adresse['code_postal'] . $adresse['ville']); ?></p>
            </div>
                
            <div class="display-ligne">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <!-- Affichage du nombre d'avis de l'offre -->
                <!-- <p> <//?php echo htmlentities($offre['nombre_avis']) . ' avis'; ?></p> -->
                <a href="#avis">Voir les avis</a>
            </div>

            <div class="display-ligne-espace">
                <!-- Affichage du nom et du prénom du propriétaire de l'offre -->
                <p class="information-offre">Proposée par : <?php echo htmlentities($compte['nom_compte'] . " " . $compte['prenom']); ?></p> 
                <!-- Affichage du prix de l'offre -->
                <button>Voir les tarifs</button> 
            </div>

        </section>

        <section class="double-blocs">

            <div id="caracteristiques" class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">
                    <?php foreach ($tags as $tag) { ?>
                        <li><?php echo htmlentities($tag['nom_tag']); ?></li>
                    <?php } ?>
                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">
                <h2>À propos de : <?php echo htmlentities($offre['titre']); ?></h2> 
                <!-- Affichage du résumé de l'offre -->
                <p><?php echo htmlentities($offre['resume']); ?></p>
                <div class="display-ligne-espace">
                    <!-- Affichage du numéro de téléphone du propriétaire de l'offre -->
                    <p>Numéro : <?php echo htmlentities($compte['tel']); ?></p>
                    <!-- Affichage du lien du site du propriétaire de l'offre -->
                    <a href="<?php echo htmlentities($offre['site_web']); ?>">Lien vers le site</a>
                </div>
            </div>
    
        </section>

        <section class="fond-blocs bordure">

            <h2>Description détaillée de l'offre :</h2>
            <!-- Affichage de la description détaillée de l'offre -->
            <p><?php echo nl2br(htmlentities($offre['description_detaille'])); ?></p>

        </section>

        <section class="double-blocs bordure">

            <div class="fond-blocs bloc-tarif">
                <div>
                    <h2>Tarifs :</h2>
                    <?php if (!empty($offre['tarifs'])): ?>
                    <table>
                        <?php foreach (explode(',', $offre['tarifs']) as $tarif) {
                            echo '<tr><td>' . htmlentities(trim($tarif)) . '</td></tr>';
                        } ?>
                    </table>
                <?php else: ?>
                    <p>Tarifs non disponibles.</p>
                <?php endif; ?>
                </div>
                <button>Voir les tarifs supplémentaires</button>
            </div>

            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture :</h2>
                <!-- Affichage des horaires d'ouverture de l'offre -->
                <p><?php echo nl2br(htmlentities($jour['nom_jour'] . " : ")); ?></p>
            </div> 
    
        </section>

        <section id="carte" class="fond-blocs bordure">

            <h1>Localisation</h1>
            <div id="map" class="carte"></div>

        </section>

        <section id="avis" class="fond-blocs avis">

            <div class="display-ligne">
                <h2>Note moyenne :</h2>
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <p>49 avis</p>
            </div>

            <div class="fond-blocs-avis">

                <div class="display-ligne-espace">

                    <div class="display-ligne">
                        <img src="/images/universel/icones/avatar-homme-1.png" class="avatar">
                        <p><strong>Stanislas</strong></p>
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                        <p><em>14/08/2023</em></p>
                    </div>

                    <p><strong>⁝</strong></p>
                </div>

                <p>Restaurant très bon avec des ingrédients de qualité</p>

                <div class="display-ligne-espace">
                    <p class="transparent">.</p>
                    <div class="display-notation">
                        <a href="#"><strong>Répondre</strong></a>
                        <p>0</p><img src="/images/universel/icones/pouce-up.png" class="pouce">
                        <p>0</p><img src="/images/universel/icones/pouce-down.png" class="pouce">
                    </div>
                </div>

            </div>        

        </section>        
         
        <div class="navigation display-ligne-espace">
            <button onclick="location.href='liste-back'">Retour à la liste des offres</button>
            <button><img src="/images/universel/icones/fleche-haut.png"></button>
        </div>

    </main>

    <footer id="footer">

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
        Redden’s, Inc.
        </div>
        
    </footer>

    <script>

        let map = L.map('map').setView([47.497745757735, -2.772722737126], 13); 
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        L.marker([47.497745757735, -2.772722737126]).addTo(map)
            .bindPopup('Côté Plage<br>Sarzeau')
            .openPopup();

        let confirmDiv = document.getElementById("confirm");
        let finalDiv = document.getElementById("final");

        function showConfirm() {
            confirmDiv.style.display = "block";
            let header = document.getElementById('header');
            header.style.filter = "blur(10px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(10px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(10px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(10px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(10px)";
            let popup = document.getElementById('confirm');
            popup.style.filter = "none";
        }

        function showFinal() {
            finalDiv.style.display = "block";
            confirmDiv.style.display = "none";
            popup.style.filter = "none";
        }

        function btnAnnuler() {
            confirmDiv.style.display = "none";
            finalDiv.style.display = "none";
            let header = document.getElementById('header');
            header.style.filter = "blur(0px)";
            let body = document.getElementById('body');
            body.style.filter = "blur(0px)";
            let footer = document.getElementById('footer');
            footer.style.filter = "blur(0px)";
            let bouton1 = document.getElementById('bouton1');
            bouton1.style.filter = "blur(0px)";
            let bouton2 = document.getElementById('bouton2');
            bouton2.style.filter = "blur(0px)";
        }

        const images = document.querySelector('.carousel-images');
        const prevButton = document.querySelector('.prev');
        const nextButton = document.querySelector('.next');

        let currentIndex = 0;

        // Gestion du clic sur le bouton "Suivant"
        nextButton.addEventListener('click', () => {
        currentIndex++;
        if (currentIndex >= images.children.length) {
            currentIndex = 0; // Revenir au début
        }
        updateCarousel();
        });

        // Gestion du clic sur le bouton "Précédent"
        prevButton.addEventListener('click', () => {
        currentIndex--;
        if (currentIndex < 0) {
            currentIndex = images.children.length - 1; // Revenir à la fin
        }
        updateCarousel();
        });

        // Met à jour l'affichage du carrousel
        function updateCarousel() {
        const width = images.clientWidth;
        images.style.transform = `translateX(-${currentIndex * width}px)`;
        }

    </script>

</body>

</html>
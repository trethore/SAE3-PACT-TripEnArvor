<?php
// Démarrer la session
session_start(); 

include('../../php/connect_params.php');

/*
$server = 'postgresdb';
$driver = 'pgsql';
$dbname = 'sae';
$user   = 'sae';
$pass	= 'naviguer-vag1n-eNTendes';*/

// Connexion à la base de données
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    
    $id_offre_cible = isset($_GET['id_offre']) ? intval($_GET['id_offre']) : 1;  // Utilisation de l'ID dans l'URL ou défaut à 1

    // Requête SQL pour récupérer le titre de l'offre
    $reqOffre = "SELECT *
                FROM _offre WHERE id_offre = ?";
    $stmt = $dbh->prepare($reqOffre);
    $stmt->execute([$id_offre_cible]);
    $offre = $stmt->fetch(PDO::FETCH_ASSOC);

    // Stocker certaines données dans la session
    // $_SESSION['offre_titre'] = $offre['titre'];
    // $_SESSION['offre_proprietaire'] = $offre['nom_pro'];

    // Requête SQL pour le type d'offre
    $reqTypeOffre = "SELECT 
                        CASE
                            WHEN EXISTS (SELECT 1 FROM _offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                            WHEN EXISTS (SELECT 1 FROM _offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc d''attraction'
                            WHEN EXISTS (SELECT 1 FROM _offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                            WHEN EXISTS (SELECT 1 FROM _offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                            WHEN EXISTS (SELECT 1 FROM _offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                            ELSE 'Inconnu'
                        END AS type_offre
                    FROM _offre o
                    WHERE o.id_offre = ?";
    $stmt2 = $dbh->prepare($reqTypeOffre);
    $stmt2->execute([$id_offre_cible]);
    $offreSpe = 'Inconnu';
    if ($row_type = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $offreSpe = $row_type['type_offre'];
    }

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

    <div class="display-ligne-espace bouton-modifier">
        <p class="transparent">.</p> 
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
    </div>

    <main id="body">
        <section class="fond-blocs">

            <h1><?php echo htmlentities($titre ?? 'Type d\'offre inconnu'); ?></h1>
            <div class="galerie-images-presentation"> 
                <img src="/images/universel/photos/hotel_2.png" alt="Image 1">
                <img src="/images/universel/photos/hotel_2_2.png" alt="Image 2">
                <img src="/images/universel/photos/hotel_2_3.png" alt="Image 3">
                <img src="/images/universel/photos/hotel_2_4.png" alt="Image 4">
                <img src="/images/universel/photos/hotel_2_5.png" alt="Image 5">
            </div>

            <div class="display-ligne-espace">
                <!-- Afficher la catégorie de l'offre et si cette offre est ouverte -->
                <p><em><?php echo htmlentities($reqTypeOffre['type_offre'] ?? 'Catégorie inconnue') . ' - ' . (($offre['ouvert'] ?? 0) ? 'Ouvert' : 'Fermé'); ?></em></p>
                <!-- Afficher l'adresse de l'offre et sa ville -->
                <p><?php echo htmlentities($offre['adresse'] . ', ' . $offre['ville']); ?></p>
            </div>
                
            <div class="display-ligne">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="/images/universel/icones/etoile-jaune.png" class="etoile">
                <!-- Afficher le nombre d'avis de l'offre -->
                <p><?php echo htmlentities($offre['nombre_avis']) . ' avis'; ?></p>
                <a href="#avis">Voir les avis</a>
            </div>

            <div class="display-ligne-espace">
                <!-- Afficher le nom du propriétaire de l'offre -->
                <p>Proposée par : <?php echo htmlentities($offre['nom_pro']); ?></p> 
                <!-- Afficher le prix de l'offre -->
                <button>À partir de <?php echo htmlentities($offre['prix_offre']); ?> €</button> 
            </div>

        </section>

        <section class="double-blocs">

            <div id="caracteristiques" class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">
                    <li><img src="/images/universel/icones/cuisine.png"><h2>Cuisine traditionnelle</h2></li>
                    <li><img src="/images/universel/icones/mer.png"><h2>Vue sur mer</h2></li>
                    <li><img src="/images/universel/icones/coeur.png"><h2>Service attentionné</h2></li>
                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">
                <h2>À propos de : <?php echo htmlentities($titre); ?></h2> 
                <!-- Afficher le bloc résumant l'offre -->
                <p><?php echo nl2br(htmlentities($offre['resume'])); ?></p>
                <!-- Afficher le lien du site internet de l'entreprise -->
                <a href="<?php echo htmlentities($offre['site_web']); ?>"><img src="/images/universel/icones/lien.png" alt="epingle" class="epingle"><?php echo htmlentities($offre['site']); ?></a>
                <!-- Afficher le numéro de téléphone du propriétaire de l'offre -->
                <p>Numéro : <?php echo htmlentities($offre['tel']); ?></p>
            </div>
    
        </section>

        <section class="fond-blocs">

            <h2>Description détaillée de l'offre :</h2>
            <!-- Afficher la description détaillée de l'offre -->
            <p><?php echo nl2br(htmlentities($offre['description_detaille'])); ?></p>

        </section>

        <section class="double-blocs">

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
                <button>Accéder à la carte complète</button>
            </div>

            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture :</h2>
                <!-- Afficher les horaires de l'offre -->
                <p><?php echo nl2br(htmlentities($offre['horaires'])); ?></p>
            </div> 
    
        </section>

        <section id="carte" class="fond-blocs">

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

    </script>

    <script>

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

    </script>

</body>

</html>
<?php
/*include('php/connect_params.php');*/
/*try {*/
    /*$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare('SELECT email from compte');
    $stmt->execute();
    $result = $stmt->fetchAll();
    echo "<pre>";
    print_r($result);
    echo "</pre>";*/
    $offres = [
        [
            "titre" => "Titre BOB",
            "ville" => "Ville BOB",
            "categorie" => "Categorie BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro BOB",
        ],
        [
            "titre" => "Titre 2 BOB",
            "ville" => "Ville 2 BOB",
            "categorie" => "Categorie 2 BOb",
            "ouvert" => "Ouvert",
            "desc" => "Desc 2 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 2 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre 3 BOB",
            "ville" => "Ville 3 BOB",
            "categorie" => "Categorie 3 BOb",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 BBO BOB OBOO BOB OBO O BO B O B O BO B OBOOB OBO B O B OB  OOBO  BO B O BOB OB",
            "nom_pro" => "Nom pro 3 BOB",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
        [
            "titre" => "Titre page suivante",
            "ville" => "Ville page suivante",
            "categorie" => "Categorie page suivante",
            "ouvert" => "Fermé",
            "desc" => "Desc 3 page suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivantepage suivante",
            "nom_pro" => "Nom pro page suivante",
        ],
    ]
    /*$dbh = null;*/
/*} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter vos offres</title>
    <link rel="stylesheet" href="/style/style-consulter-offres-front.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <header>
        <img class="logo" src="../../images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT</div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="../../images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="index.html"><img class="ICON-accueil" src="../../images/universel/icones/icon_accueil.png" /></a>
        <a href="index.html"><img class="ICON-utilisateur" src="../../images/universel/icones/icon_utilisateur.png" /></a>
    </header>

    <h1 class="titre-liste-offres">Liste des Offres Disponibles</h1>

    <!-- Conteneur principal -->
    <div class="conteneur">
        <!-- Conteneur des filtres -->
        <article class="filtre-tri">
            <h2>Une Recherche en Particulier ? Filtrez !</h2>
            <div class="fond-filtres">
                <div>
                    <!-- Catégorie -->
                    <div class="categorie">
                        <h3>Catégorie</h3>
                        <div>
                            <label><input type="checkbox"> Parc d'Attraction</label>
                            <label><input type="checkbox"> Restaurant</label>
                            <label><input type="checkbox"> Visite</label>
                            <label><input type="checkbox"> Spectacle</label>
                            <label><input type="checkbox"> Activité</label>
                        </div>
                    </div>

                    <!-- Disponibilité -->
                    <div class="disponibilite">
                        <h3>Disponibilité</h3>
                        <div>
                            <label><input type="radio" name="disponibilite"> Ouvert</label>
                            <label><input type="radio" name="disponibilite"> Fermé</label>
                        </div>
                    </div>
                        
                    <!-- Trier -->
                    <div class="trier">
                        <h3>Trier</h3>
                        <div>
                            <div>
                                <label>Note minimum :</label>
                                <select>
                                    <option></option>
                                    <option>★★★★★</option>
                                    <option>★★★★</option>
                                    <option>★★★</option>
                                    <option>★★</option>
                                    <option>★</option>
                                </select>
                            </div>
                            
                            <div>
                                <div>
                                    <div>
                                        <label>Prix minimum &nbsp;:</label>
                                        <input type="number" min="0">
                                    </div>
                                    <div>
                                        <label>Prix maximum :</label>
                                        <input type="number" min="0">
                                    </div>
                                </div>
                                <div>
                                    <select>
                                        <option>Trier par :</option>
                                        <option>Date</option>
                                        <option>Prix</option>
                                        <option>Popularité</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div>
                    <!-- Localisation -->
                    <div class="localisation">
                        <h3>Localisation</h3>
                        <div>
                            <label><input type="radio" name="localisation"> Autour de moi</label>
                            <div>
                                <label><input type="radio" name="localisation"> Rechercher</label>
                                <input type="text" placeholder="Rechercher...">
                            </div>
                        </div>
                    </div>

                    <!-- Type d'offre -->
                    <div class="typeOffre"></div>
        
                    <!-- Date -->
                    <div class="date">
                        <h3>Date</h3>
                        <div>
                            <div>
                                <label>Date de début &nbsp;:</label>
                                <input type="date">
                            </div>
                            <div>
                                <label>Date de fin &emsp;&emsp;:</label>
                                <input type="date">
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </article>

        <!-- Carte -->
        <div class="conteneur-carte">
            <div class="carte" style="width: 100%; height: 400px;"></div>
        </div>

        <!-- Offres -->
        <section class="section-offres">
            <?php
            $offers_per_page = 9;

            $total_offers = count($offres);
            $total_pages = ceil($total_offers / $offers_per_page);

            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            $offset = ($current_page - 1) * $offers_per_page;

            $offres_for_page = array_slice($offres, $offset, $offers_per_page);

            foreach ($offres_for_page as $tab) {
            ?>
                <div class="offre">
                <div class="sous-offre">
                    <div class="lieu-offre"><?php echo $tab["ville"] ?></div>
                    <div class="ouverture-offre"><?php echo $tab["ouvert"] ?></div>
                    <img class="carte-offre">
                    <p class="titre-offre"><?php echo $tab["titre"] ?></p>
                    <p class="categorie-offre"><?php echo $tab["categorie"] ?></p>
                    <p class="description-offre"><?php echo $tab["desc"] . " " ?><span>En savoir plus</span></p>
                    <p class="nom-offre"><?php echo $tab["nom_pro"] ?></p>
                    <div class="bas-offre">
                        <div class="etoiles">
                            <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                            <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                            <img class="etoile" src="/images/frontOffice/etoile-pleine.png">
                            <img class="etoile" src="/images/frontOffice/etoile-vide.png">
                            <img class="etoile" src="/images/frontOffice/etoile-vide.png">
                            <p class="nombre-notes">(120)</p>
                        </div>
                        <p class="prix">A partir de <span>80€</span></p>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </section>
        <div class="pagination">
            <?php if ($current_page > 1) { ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-btn">Page Précédente</a>
            <?php } ?>
            
            <?php if ($current_page < $total_pages) { ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-btn">Page suivante</a>
            <?php } ?>
        </div>
    </div>
    <footer>
        <div class="footer-top">
          <div class="footer-top-left">
            <span class="footer-subtitle">P.A.C.T</span>
            <span class="footer-title">TripEnArmor</span>
          </div>
          <div class="footer-top-right">
            <span class="footer-connect">Restons connectés !</span>
            <div class="social-icons">
              <a href="https://x.com/?locale=fr"><div class="social-icon" style="background-image: url('/images/universel/icones/x.png');"></div></a>
              <a href="https://www.facebook.com/?locale=fr_FR"><div class="social-icon" style="background-image: url('/images/universel/icones/facebook.png');"></div></a>
              <a href="https://www.youtube.com/"><div class="social-icon" style="background-image: url('/images/universel/icones/youtube.png');"></div></a>
              <a href="https://www.instagram.com/"><div class="social-icon" style="background-image: url('/images/universel/icones/instagram.png');"></div></a>
            </div>
          </div>
    
    
          <!-- Barre en bas du footer incluse ici -->
    
        </div>
        <div class="footer-bottom">
          Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site - Conditions générales - ©
          Redden’s, Inc.
        </div>
      </footer>

      <script>
        const map = L.map('map').setView([48.6493, -2.0257], 13); // Coordonnées pour Saint-Malo, France
    
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    
        L.marker([48.6493, -2.0257]).addTo(map)
            .bindPopup('Les Embruns du Phare<br>Saint-Malo')
            .openPopup();
    </script>
</body>
</html>
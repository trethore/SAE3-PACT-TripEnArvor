<?php
include('../../php/connect_params.php');

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare('SELECT * from _offre NATURAL JOIN _compte WHERE id_compte_professionnel = id_compte');
    $stmt->execute();
    $offres = $stmt->fetchAll();
    echo "<pre>";
    print_r($offres);
    echo "</pre>";

    $reqTypeOffre = "SELECT 
                        CASE
                            WHEN EXISTS (SELECT 1 FROM _offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                            WHEN EXISTS (SELECT 1 FROM _offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc d''attraction'
                            WHEN EXISTS (SELECT 1 FROM _offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                            WHEN EXISTS (SELECT 1 FROM _offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                            WHEN EXISTS (SELECT 1 FROM _offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                            ELSE 'Inconnu'
                        END AS offreSpe
                        FROM _offre o
                        WHERE o.id_offre = ?";

    $stmtCategory = $dbh->prepare($reqTypeOffre);

    foreach ($offres as &$offre) {
        $stmtCategory->execute([$offre['id_offre']]);
        $categoryResult = $stmtCategory->fetch();
        $offre['categorie'] = $categoryResult['offreSpe'] ?? 'Inconnu';
    }
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulter vos offres</title>
    <link rel="stylesheet" href="../../style/style-consulter-offres-front.css">
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

        <!-- Carte 
        <div class="conteneur-carte">
            <div class="carte" style="width: 100%; height: 400px;"></div>
        </div> -->

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
                    <div class="ouverture-offre"><?php /*echo $tab["ouvert"]*/ ?>Ouvert</div>
                    <img class="carte-offre" style="background: url(../images/universel/photos/hotel_2.png) center;">
                    <p class="titre-offre"><?php echo $tab["titre"] ?></p>
                    <p class="categorie-offre"><?php echo $tab["categorie"]; ?></p>
                    <p class="description-offre"><?php echo $tab["resume"] . " " ?><span>En savoir plus</span></p>
                    <p class="nom-offre"><?php echo $tab["nom_compte"] . " " . $tab["prenom"] ?></p>
                    <div class="bas-offre">
                        <div class="etoiles">
                            <img class="etoile" src="../images/frontOffice/etoile-pleine.png">
                            <img class="etoile" src="../images/frontOffice/etoile-pleine.png">
                            <img class="etoile" src="../images/frontOffice/etoile-pleine.png">
                            <img class="etoile" src="../images/frontOffice/etoile-vide.png">
                            <img class="etoile" src="../images/frontOffice/etoile-vide.png">
                            <p class="nombre-notes">(120)</p>
                        </div>
                        <p class="prix">A partir de <span><?php echo $tab["prix_offre"] ?>€</span></p>
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
              <a href="https://x.com/?locale=fr"><div class="social-icon" style="background-image: url('/html/images/universel/icones/x.png');"></div></a>
              <a href="https://www.facebook.com/?locale=fr_FR"><div class="social-icon" style="background-image: url('/html/images/universel/icones/facebook.png');"></div></a>
              <a href="https://www.youtube.com/"><div class="social-icon" style="background-image: url('/html/images/universel/icones/youtube.png');"></div></a>
              <a href="https://www.instagram.com/"><div class="social-icon" style="background-image: url('/html/images/universel/icones/instagram.png');"></div></a>
            </div>
          </div>
    
    
          <!-- Barre en bas du footer incluse ici -->
    
        </div>
        <div class="footer-bottom">
          Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site - Conditions générales - ©
          Redden’s, Inc.
        </div>
      </footer>
</body>
</html>

<?php $dbh = null; ?>
<?php
include('../../php/connect_params.php');
try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

/*******************
Requete SQL préfaite
********************/
$reqOffre = "SELECT * from sae._offre;";
$reqIMG = "SELECT img.lien_fichier 
            FROM sae._image img
            JOIN sae._offre_contient_image oci 
            ON img.lien_fichier = oci.id_image
            WHERE oci.id_offre = :id_offre
            LIMIT 1;";

$reqTypeOffre = "SELECT 
                        CASE
                            WHEN EXISTS (SELECT 1 FROM sae._offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                            WHEN EXISTS (SELECT 1 FROM sae._offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc attraction'
                            WHEN EXISTS (SELECT 1 FROM sae._offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                            WHEN EXISTS (SELECT 1 FROM sae._offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                            WHEN EXISTS (SELECT 1 FROM sae._offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                            ELSE 'Inconnu'
                        END AS offreSpe
                        FROM sae._offre o
                        WHERE o.id_offre = :id_offre;";

$reqPrix = "SELECT prix_offre from sae._offre where id_offre = :id_offre;";

$result = $conn->query($reqOffre); 

function checkCompteProfessionnel($conn, $id_compte) {
    $sql = "SELECT 1 FROM _compte_professionnel WHERE id_compte = :id_offre";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id_compte]);
    return $stmt->fetch() ? true : false;
}


if (isset($_SESSION['id'])) {
    $id_compte = $_SESSION['id'];
    
    if (checkCompteProfessionnel($conn, $id_compte)) {
        echo "L'id_compte $id_compte est un compte professionnel.";
    } else {
        echo "L'id_compte $id_compte n'est pas un compte professionnel.";
    }
} else {
    checkCompteProfessionnel($conn, 1);
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backListe.css">
    <link rel="stylesheet" href="/style/styles.css">
    <link rel="stylesheet" href="/style/style_HFB.css">
    <title>Liste de vos offres</title>
</head>
<body>
    <header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <a href="/front/consulter-offres"><div class="text-wrapper-17">PACT Pro</div></a>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/se-connecter"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    <main>
        <h1>Liste de vos Offres</h1>
        <!--------------- 
        Filtrer et trier
        ----------------->
        <article class="filtre-tri">
            <h2>Une Recherche en Particulier ? Filtrez !</h2>
            <div>
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
                    <div class="typeOffre">
                        <h3>Type d'offre</h3>
                        <div>
                            <label><input type="radio" name="typeOffre"> Payante</label>
                            <label><input type="radio" name="typeOffre"> Premium</label>
                        </div>
                    </div>
        
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
        <section class="lesOffres">
            <?php while($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
            <article>
                <div onclick="location.href='/back/consulter-offre/index.php?id=<?php echo urlencode($row['id_offre']); ?>'">
                    <div class="lieu-offre"><?php echo htmlentities($row["ville"]) ?></div>
                    <div class="ouverture-offre"><?php  echo htmlentities($row["type_offre"])?></div>
                    <!--------------------------------------- 
                    Récuperer la premère image liée à l'offre 
                    ---------------------------------------->
                    <img src="<?php
                        // Préparer et exécuter la requête
                        $stmtIMG = $conn->prepare($reqIMG);
                        $stmtIMG->bindParam(':id_offre', $id_row['id_offre'], PDO::PARAM_INT);
                        $stmtIMG->execute();

                        // Récupérer la première image
                        $image = $stmtIMG->fetch(PDO::FETCH_ASSOC);

                        if ($image && !empty($image['lien_fichier'])) {
                            // Afficher l'image si elle existe
                            echo htmlentities($image['lien_fichier']);
                        } else {
                            // Afficher une image par défaut
                            echo htmlentities('/images/universel/photos/default-image.jpg');
                        }
                    ?>" alt="image offre">
                    <p><?php echo htmlentities($row["titre"]) ?></p>
                    <!---------------------------------------------------------------------------- 
                    Choix du type de l'activité (Restaurant, parc, etc...)
                    ------------------------------------------------------------------------------>
                    <?php 
                    try {
                        // Préparer la requête SQL
                        $reqTypeOffre = "
                            SELECT 
                                CASE
                                    WHEN EXISTS (SELECT 1 FROM sae._offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                                    WHEN EXISTS (SELECT 1 FROM sae._offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc attraction'
                                    WHEN EXISTS (SELECT 1 FROM sae._offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                                    WHEN EXISTS (SELECT 1 FROM sae._offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                                    WHEN EXISTS (SELECT 1 FROM sae._offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                                    ELSE 'Inconnu'
                                END AS offreSpe
                            FROM sae._offre o
                            WHERE o.id_offre = :id_offre;
                        ";
                    
                        // Préparation et exécution
                        $stmt2 = $conn->prepare($reqTypeOffre);
                        $stmt2->bindParam(':id_offre', $id_offre, PDO::PARAM_INT); // Lier l'ID de l'offre
                        $stmt2->execute();
                    
                        // Récupérer le résultat
                        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                    
                        // Vérifier et afficher
                        if ($row && isset($row['offreSpe'])) {
                            echo htmlentities($row['offreSpe']); // Afficher le type d'offre
                        } else {
                            echo htmlentities('Inconnu'); // Valeur par défaut si aucun résultat
                        }
                    } catch (Exception $e) {
                        // Gestion des erreurs
                        error_log("Erreur lors de la récupération du type d'offre : " . $e->getMessage());
                        echo htmlentities('Erreur lors de la récupération du type d\'offre');
                    }
                    ?><!--
                    <p> <?php /*
                    // Préparation et exécution de la requête
                    $stmt2 = $conn->prepare($reqTypeOffre);
                    $stmt2->bindParam(':id_offre', $id_offre, PDO::PARAM_INT); // Lié à l'ID de l'offre
                    $stmt2->execute();
                    $row_type = $stmt2->fetch(PDO::FETCH_ASSOC);

                    // Vérification et récupération du résultat
                    $offreSpe = "Inconnu"; // Valeur par défault
                    if ($row_type && isset($row_type['type_offre'])) {
                        $offreSpe = $row_type['type_offre'];
                    }
                    echo htmlentities($offreSpe); */?> </p>-->

                    <!---------------------------------------------------------------------- 
                    Choix de l'icone pour reconnaitre une offre gratuite, payante ou premium 
                    ------------------------------------------------------------------------>
                    <img src="
                    <?php
                    switch ($row["type_offre"]) {
                        case 'gratuit':
                            echo htmlentities("/images/backOffice/icones/gratuit.png");
                            break;
                        
                        case 'payant':
                            echo htmlentities("/images/backffice/icones/payant.png");
                            break;
                            
                        case 'premium':
                            echo htmlentities("/images/backOffice/icones/premium.png");
                            break;
                    }
                    ?>">
                    <!-------------------------------------- 
                    Affichage de la note globale de l'offre 
                    ---------------------------------------->
                    <div class="etoiles">
                        <img src="/images/universel/icones/etoile-pleine.png">
                        <img src="/images/universel/icones/etoile-pleine.png">
                        <img src="/images/universel/icones/etoile-pleine.png">
                        <img src="/images/universel/icones/etoile-pleine.png">
                        <img src="/images/universel/icones/etoile-pleine.png">
                        <p>49</p>
                    </div>
                    <div>
                        <p>Avis non lues : <span><b>4</b></span></p>
                        <p>Avis non répondues : <span><b>1</b></span></p>
                        <p>Avis blacklistés : <span><b>0</b></span></p>
                    </div>
                    <p>A partir de <span><?php echo htmlentities($row["prix_offre"]) ?>€</span></p>
                </div>
            </article>
            <?php } ?>
            <!-------------------------------------- 
            Pagination
            ---------------------------------------->
            <div class="pagination">
            <?php if ($current_page > 1) { ?>
                <a href="?page=<?php echo $current_page - 1; ?>" class="pagination-btn">Page Précédente</a>
            <?php } ?>
            
            <?php if ($current_page < $total_pages) { ?>
                <a href="?page=<?php echo $current_page + 1; ?>" class="pagination-btn">Page suivante</a>
            <?php } ?>
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
        Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du site -
        Conditions générales - ©
        Redden's, Inc.
        </div>
    </footer>
</body>
</html>
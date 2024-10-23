<?php

include('/connect_params.php');
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", 
            $user, $pass);
    foreach($dbh->query('SELECT * from forum1._user', 
                        PDO::FETCH_ASSOC) 
                as $row) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

session_start(); // Démarrer la session
function checkCompteProfessionnel($conn, $id_compte) {
    // Vérifier si l'id_compte est un entier
    if (!is_int($id_compte)) {
        return false; // ou lever une exception selon votre logique d'erreur
    }

    // Préparer la requête pour éviter les injections SQL
    $sql = "SELECT 1 FROM _compte_professionnel WHERE id_compte = ?";
    $stmt = $conn->prepare($sql);

    // Lier le paramètre
    $stmt->bind_param('i', $id_compte);

    // Exécuter la requête
    $stmt->execute();

    // Obtenir le résultat
    $result = $stmt->get_result();

    // Vérifier si une ligne a été trouvée
    if ($result->num_rows > 0) {
        return true; // L'id_compte est présent dans _compte_professionnel
    } else {
        return false; // L'id_compte n'est pas présent
    }
}

// Exemple d'utilisation
session_start();
if (isset($_SESSION['id_compte'])) {
    $id_compte = $_SESSION['id_compte'];
    
    if (checkCompteProfessionnel($conn, $id_compte)) {
        echo "L'id_compte $id_compte est un compte professionnel.";
    } else {
        echo "L'id_compte $id_compte n'est pas un compte professionnel.";
    }
} else {
    echo "Aucun id_compte trouvé dans la session.";
}

/*******************
Requete SQL préfaite
********************/
$reqOffre = "SELECT * FROM _offre";
$reqIMG = "SELECT img.lien_fichier 
            FROM _image img
            JOIN _offre_contient_image oci 
            ON img.lien_fichier = oci.id_image
            WHERE oci.id_offre = $id_offre_cible
            LIMIT 1;";
$reqTypeOffre = $sql = "SELECT 
                        CASE
                            WHEN EXISTS (SELECT 1 FROM _offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                            WHEN EXISTS (SELECT 1 FROM _offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc d\'attraction'
                            WHEN EXISTS (SELECT 1 FROM _offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                            WHEN EXISTS (SELECT 1 FROM _offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                            WHEN EXISTS (SELECT 1 FROM _offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                            ELSE 'Inconnu'
                        END AS offreSpe
                        FROM _offre o
                        WHERE o.id_offre = ?";

$result = $conn->query($reqOffre); 

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style_backListe.css">
    <title>Liste de vos offres</title>
</head>
<body>
    <main>
        <h1>Liste de vos offre</h1>
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
            <?php
            /* -----------------Gestion de la pagination -----------------------*/
            $offers_per_page = 9;
            $total_offers = count($offres);
            $total_pages = ceil($total_offers / $offers_per_page);
            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($current_page - 1) * $offers_per_page;
            $offres_for_page = array_slice($offres, $offset, $offers_per_page);
            /*------------------------------------------------------------------ */
            
            while($row = $result->fetch_assoc()) {
            ?>
            <article>
                <div>
                    <div class="lieu-offre"><?php echo htmlentities($row["ville"]) ?></div>
                    <div class="ouverture-offre"><?php  echo htmlentities($row["type_offre"])?></div>
                    <!--------------------------------------- 
                    Récuperer la premère image liée à l'offre 
                    ----------------------------------------->
                    <img src="
                    <?php
                        // ID de l'offre pour récupérer la première image
                        $id_offre_cible = $row["id_offre"];

                        // Exécuter la requête
                        $resIMG = $conn->query($reqIMG);

                        // Récupérer la première image et l'afficher
                        if ($resIMG->num_rows > 0) {
                            $image = $resIMG->fetch_assoc();
                            echo htmlentities($image['lien_fichier']);
                        } else {
                            echo htmlentities('/images/universel/photos/default-image.jpg'); // une image par défaut si aucune n'est trouvée
                        }
                    ?>
                    ">
                    <p><?php echo htmlentities($row["titre"]) ?></p>
                    <!---------------------------------------------------------------------------- 
                    Choix de l'icone pour ecrire le type de l'activité (Restaurant, parc, etc...)
                    ------------------------------------------------------------------------------>
                    <p><?php 
                    // Préparation et exécution de la requête
                    $stmt2 = $con->prepare($sql);
                    $stmt2->bind_param('i', $id_offre); // Lié à l'ID de l'offre
                    $stmt2->execute();
                    $res2 = $stmt2->get_result();

                    // Vérification et récupération du résultat
                    $offreSpe = 'Inconnu'; // Valeur par défaut si aucun résultat n'est trouvé
                    if ($row_type = $res2->fetch_assoc()) {
                        $offreSpe = $row_type['type_offre'];
                    }
                    echo htmlentities($type_offre); ?></p>

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
                    ?>" alt="">
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
                    <p>A partir de <span><?php echo htmlentities($row["prix_offre"]) ?></span></p>
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
</body>
</html>
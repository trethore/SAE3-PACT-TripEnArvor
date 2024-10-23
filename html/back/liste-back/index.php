<?php

include('connect_params.php');
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

$reqOffre = "SELECT * FROM _offre";
$reqIMG = "SELECT img.lien_fichier, oci.id_offre FROM _image img
            JOIN _offre_contient_image oci 
            ON img.lien_fichier = oci.id_image;"
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
            $offers_per_page = 9;

            $total_offers = count($offres);
            $total_pages = ceil($total_offers / $offers_per_page);

            $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

            $offset = ($current_page - 1) * $offers_per_page;

            $offres_for_page = array_slice($offres, $offset, $offers_per_page);
            
            while($row = $result->fetch_assoc()) {
            ?>
            <article>
                <div>
                    <div class="lieu-offre"><?php echo htmlentities($row["ville"]) ?></div>
                    <div class="ouverture-offre"><?php  ?></div>
                    <img src="
                    <?php
                        $resIMG = $conn->query($reqIMG);
                        while($images = $resIMG->fetch_assoc()) {

                        }
                    ?>
                    ">
                    <p><?php echo htmlentities($row["titre"]) ?></p>
                    <p><?php echo htmlentities() ?></p>
                    <img src="<?php?>" alt="">
                    <div class="etoiles">
                        <img src="images/universel/icones/etoile-pleine.png">
                        <img src="images/universel/icones/etoile-pleine.png">
                        <img src="images/universel/icones/etoile-pleine.png">
                        <img src="images/universel/icones/etoile-pleine.png">
                        <img src="images/universel/icones/etoile-pleine.png">
                        <p><?php echo htmlentities() ?></p>
                    </div>
                    <div>
                        <p>Avis non lues : <span><b>4</b></span></p>
                        <p>Avis non répondues : <span><b>1</b></span></p>
                        <p>Avis blacklistés : <span><b>0</b></span></p>
                    </div>
                    <p>A partir de <span><?php echo htmlentities() ?></span></p>
                </div>
            </article>
            <?php } ?>
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
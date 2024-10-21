<?php

// Connexion à la BDD
$conn = new mysqli('localhost', 'username', 'password', 'database_name');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT ville, titre, typeOffre, nombreAvis, prix FROM Offre";
$result = $conn->query($sql); 

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style_backListe.css">
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
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
            ?>
            <article>
                <div>
                    <div class="lieu-offre"><?php echo htmlentities($row["ville"]) ?></div>
                    <div class="ouverture-offre"><?php  ?></div>
                    <img src="images/universel/photos/coteplage_facade.jpg">
                    <p><?php echo htmlentities($row["titre"]) ?></p>
                    <p><?php echo htmlentities() ?></p>
                    <img src="images/backOffice/icones/payante.png" alt="">
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
            <?php
                    }
                }
                if ($pageNumber > 1) { ?>
                    <a href="http://localhost:8888/index.php?page=<?php echo $pageNumber - 1?>">Page précédente</a>
                <?php } ?>
                <?php
                if ($pageNumber < ceil(count($regions)/LIG)) { ?>
                    <a href="http://localhost:8888/index.php?page=<?php echo $pageNumber + 1?>">Page suivante</a>
                <?php 
                }
                $conn->close();
                ?>
        </section>
    </main>
</body>
</html>
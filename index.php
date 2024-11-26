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
    $offre = [
        "id" => "0",
        "titre" => "Titre BOB",
        "ville" => "BOB Land",
        "adresse" => "20 rue des Bobs",
        "categorie" => "Categorie BOb",
        "ouvert" => "Fermé",
        "desc" => "L'offre Bob est Bob pour ceux qui Bob un Bob de Bob et de Bob. Situé en Bob de Bob, cet Bob Bob offre une Bob sur Bob et les Bob. Les Bob, décorées avec Bob, sont Bob de tout le Bob nécessaire pour un Bob Bob : Bob de Bob, Bob Bob, et Bob à une Bob Wi-Fi Bob.",
        "desc2" => "En Bob de l'hébergement, cette Bob inclut des Bob Bob préparés par notre Bob Bob. Vous pourrez Bob des Bob Bob et Bob, Bob de Bob. L'hôtel Bob également un Bob avec des Bob de Bob, une Bob Bob et un Bob pour vous Bob après une Bob de Bob.",
        "nom_pro" => "Nom pro BOB",
        "nombre_avis" => "12",
        "a_propos" => "Bob en 2010, Bob est une Bob familiale dédiée à Bob et au Bob en Bob. Notre Bob est de Bob des Bob pour nos Bob en leur Bob des Bob dans des Bob enchanteurs.",
        "tarifs" => ["80€" => "Bob simple", "140€" => "Bob lit double", "160€" => "Bob deux lits", "300€" => "Bob premium"],
        "horaires" => ["Lundi" => "09h - 23h", "Mardi" => "09h - 23h", "Mercredi" => "09h - 23h", "Jeudi" => "09h - 23h", "Vendredi" => "09h - 23h", "Samedi" => "09h - 23h", "Dimanche" => "09h - 20h"],
        "site" => "les-bobs-du-bob.fr",
        "tel" => "02 02 02 02 02",
    ]
    /*$dbh = null;*/
/*} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}*/
?>

<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8" />
    <link rel="stylesheet" href="style/styleguide.css"/>
    <link rel="stylesheet" href="style/styleHFF.css"/>
    <link rel="stylesheet" href="style/style-details-offre-visiteur.css"/>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>

<body>

    <header>
        <img class="logo" src="images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT</div>
        <div class="search-box">
        <button class="btn-search"><img class="cherchero" src="images/universel/icones/chercher.png" /></button>
        <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="index.html"><img class="ICON-accueil" src="images/universel/icones/icon_accueil.png" /></a>
        <a href="index.html"><img class="ICON-utilisateur" src="images/universel/icones/icon_utilisateur.png" /></a>
    </header>

    <main>

        <section class="fond-blocs"><!---->

            <h1><?php echo $offre["titre"] ?></h1>
            <div class="galerie-images-presentation"> 
                <img src="images/universel/photos/hotel_2.png" alt="Image 1">
                <img src="images/universel/photos/hotel_2_2.png" alt="Image 2">
                <img src="images/universel/photos/hotel_2_3.png" alt="Image 3">
                <img src="images/universel/photos/hotel_2_4.png" alt="Image 4">
                <img src="images/universel/photos/hotel_2_5.png" alt="Image 5">
            </div>

            <div class="display-ligne-espace">
                <p><em><?php echo $offre["categorie"] . ", " . $offre["ouvert"] ?></em></p>
                <p><?php echo $offre["adresse"] . ", " . $offre["ville"] ?></p>
            </div>
                
            <div class="display-ligne">
                <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="images/universel/icones/etoile-grise.png" class="etoile">
                <img src="images/universel/icones/etoile-grise.png" class="etoile">
                <p>(<?php echo $offre["nombre_avis"] ?>)</p>
                <a href="#avis">Voir les avis</a>
            </div>

            <div class="display-ligne-espace">
                <p>Proposée par : <?php echo $offre["nom_pro"] ?></p>
                <button>À partir de 80€ la nuit</button>
            </div>

        </section>

        <section class="double-blocs"><!---->

            <div class="fond-blocs bloc-caracteristique">
                <ul class="liste-caracteristique">
                    <li><img src="images/universel/icones/hotel.png"><h2>Hôtel charmant</h2></li>
                    <li><img src="images/universel/icones/mer.png"><h2>Vue sur mer</h2></li>
                    <li><img src="images/universel/icones/coeur.png"><h2>Service attentionné</h2></li>
                    <li><img src="images/universel/icones/dej.png"><h2>Petit déjeuner maison</h2></li>
                </ul>
            </div> 

            <div class="fond-blocs bloc-a-propos">
                <h2>À propos de : <?php echo $offre["titre"] ?></h2>
                <p><?php echo $offre["a_propos"] ?>​</p>
                <a href="<?php echo $offre["site"] ?>"><img src="images/universel/icones/lien.png" alt="epingle" class="epingle"><?php echo $offre["site"] ?></a>
                <p>Numéro : <?php echo $offre["tel"] ?></p>
            </div>
    
        </section>

        <section class="fond-blocs"><!---->

            <h2>Description détaillée de l'offre :</h2>
            <p><?php echo $offre["desc"] ?></p>
            <p><?php echo $offre["desc2"] ?></p>

        </section>

        <section class="double-blocs"><!---->

            <div class="fond-blocs bloc-tarif">
            <div>
                <h2>Tarifs :</h2>
                <table>
                    <?php
                    $counter = 0;
                    echo "<tr>";
                    foreach ($offre["tarifs"] as $price => $description) {
                        echo "<td>$price $description</td>";
                        $counter++;

                        if ($counter % 2 == 0) {
                            echo "</tr><tr>";
                        }
                    }
                    if ($counter % 2 != 0) {
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>

                <button>Télécharger la grille des tarifs</button>
            </div>

            <div class="fond-blocs bloc-ouverture">
                <h2>Ouverture :</h2>
                <ul>
                    <li><em>Lundi : <?php echo $offre["horaires"]["Lundi"] ?></em></li>
                    <li><em>Mardi : <?php echo $offre["horaires"]["Mardi"] ?></em></li>
                    <li><em>Mercredi : <?php echo $offre["horaires"]["Mercredi"] ?></em></li>
                    <li><em>Jeudi : <?php echo $offre["horaires"]["Jeudi"] ?></em></li>
                    <li><em>Vendredi : <?php echo $offre["horaires"]["Vendredi"] ?></em></li>
                    <li><em>Samedi : <?php echo $offre["horaires"]["Samedi"] ?></em></li>
                    <li><em>Dimanche : <?php echo $offre["horaires"]["Dimanche"] ?></em></li>
                </ul>
            </div> 
    
        </section>

        <section class="fond-blocs">

            <h1>Localisation</h1>
            <div id="map" style="width: 1000px; height: 750px;" class="carte"></div>

        </section>

        <section class="fond-blocs avis">

            <div class="display-ligne">
                <h2>Note moyenne :</h2>
                <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                <img src="images/universel/icones/etoile-grise.png" class="etoile">
                <img src="images/universel/icones/etoile-grise.png" class="etoile">
                <p>49 avis</p>
            </div>

            <div class="fond-blocs-avis">

                <div class="display-ligne-espace">

                    <div class="display-ligne">
                        <img src="images/universel/icones/avatar-femme-1.png" class="avatar">
                        <p><strong>Titouan</strong></p>
                        <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="images/universel/icones/etoile-grise.png" class="etoile">
                        <img src="images/universel/icones/etoile-grise.png" class="etoile">
                        <img src="images/universel/icones/etoile-grise.png" class="etoile">
                        <img src="images/universel/icones/etoile-grise.png" class="etoile">
                        <p><em>24/11/2023</em></p>
                    </div>

                    <p><strong>⁝</strong></p>
                </div>

                <p>Personnel qui ne se soucie pas de ses clients, personne pour nous indiquer comment allumer la télévision, un scandale !</p>

                <div class="display-ligne-espace">
                    <p class="transparent">.</p>
                    <div class="display-notation">
                        <p>0</p><img src="images/universel/icones/pouce-up.png" class="pouce">
                        <p>0</p><img src="images/universel/icones/pouce-down.png" class="pouce">
                    </div>
                </div>

                <div class="reponse">
                    <div class="display-ligne-espace">

                        <div class="display-ligne">
                            <img src="images/universel/icones/avatar-homme-1.png" class="avatar">
                            <p><strong>Éric Dupont</strong></p>
                            <p><em>24/11/2023</em></p>
                        </div>

                        <p><strong>⁝</strong></p>
                    </div>

                    <p>Nous avons été à votre service durant tout votre séjour, merci de passer votre chemin à l’avenir.</p>

                    <div class="display-ligne-espace">
                        <p class="transparent">.</p>
                        <div class="display-notation">
                            <p>0</p><img src="images/universel/icones/pouce-up.png" class="pouce">
                            <p>0</p><img src="images/universel/icones/pouce-down.png" class="pouce">
                        </div>
                    </div>
                </div>             

            </div>     
            
            

            <div class="fond-blocs-avis">

                <div class="display-ligne-espace">

                    <div class="display-ligne">
                        <img src="images/universel/icones/avatar-homme-2.png" class="avatar">
                        <p><strong>Mathéo</strong></p>
                        <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                        <img src="images/universel/icones/etoile-jaune.png" class="etoile">
                        <p><em>15/12/2022</em></p>
                    </div>

                    <p><strong>⁝</strong></p>
                </div>

                <div class="image-avis"style="display: flex;">
                    <div class="galerie-images-avis">
                        <img src="images/universel/photos/hotel_2.png" alt="Image 1">
                        <img src="images/universel/photos/hotel_2_2.png" alt="Image 2">
                        <img src="images/universel/photos/hotel_2_3.png" alt="Image 3">
                        <img src="images/universel/photos/hotel_2_4.png" alt="Image 4">                    
                    </div>
                    <p>Nous avons passé un séjour absolument merveilleux aux Embruns du Phare. Dès notre arrivée, nous avons été accueillis chaleureusement par le personnel, qui s'est montré attentif et disponible tout au long de notre séjour. La vue depuis notre chambre était à couper le souffle, avec l'océan s'étendant à perte de vue.</p>
                </div>

                <div class="display-ligne-espace">
                    <p class="transparent">.</p>
                    <div class="display-notation">
                        <p>0</p><img src="images/universel/icones/pouce-up.png" class="pouce">
                        <p>0</p><img src="images/universel/icones/pouce-down.png" class="pouce">
                    </div>
                </div>

            </div> 

        </section>        
         
        <div class="navigation display-ligne-espace">
            <button>Retour à la liste des offres</button>
            <button><img src="images/universel/icones/fleche-haut.png"></button>
        </div>

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
                <div class="social-icon" style="background-image: url('images/universel/icones/x.png');"></div>
            </a>
            <a href="https://www.facebook.com/?locale=fr_FR">
                <div class="social-icon" style="background-image: url('images/universel/icones/facebook.png');"></div>
            </a>
            <a href="https://www.youtube.com/">
                <div class="social-icon" style="background-image: url('images/universel/icones/youtube.png');"></div>
            </a>
            <a href="https://www.instagram.com/">
                <div class="social-icon" style="background-image: url('images/universel/icones/instagram.png');"></div>
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

</body>

</html>
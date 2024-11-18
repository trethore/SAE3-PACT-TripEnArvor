<?php
session_start();
if (isset($_POST['titre'])){
    $submitted = true;
}
else{
    $submitted = false;
}
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création offre</title>
    <link rel="stylesheet" href="../../style/styleguide.css" />
    <link rel="stylesheet" href="/style/style_HFB.css" />
    <link rel="stylesheet" href="../../style/style_gereeOffre.css" />
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Seymour+One&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=SeoulNamsan&display=swap" rel="stylesheet">
</head>
<body>
<header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT Pro</div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="index.html"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="index.html"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
<?php
if (!$submitted) {
?>
    <main>
        <h2> Création d'une offre</h2>
        <form action="index.php" method="post" enctype="multipart/form-data" id="dynamicForm">
            <h3>Informations importante</h3>
            <div class="important">
            <table border="0">
                <tr>
                    <td><label for="titre">Titre <span class="required">*</span></label> </td>
                    <td colspan="3"><input type="text" id="titre" name ="titre" placeholder="Insérer un titre" required></td>
                </tr>
                <tr>
                <td><label for="categorie">Catégorie <span class="required">*</span></label></td>
                    <td><div class="custom-select-container">
                        <select class="custom-select" id = "categorie" name = "lacat"> 
                            <option value="">Choisir une catégorie </option>
                            <option value = "restaurant"> Restaurant</option>
                            <option value = "parc"> Parc d'attraction</option>
                            <option value = "spectacle"> Spectacle</option>
                            <option value = "visite"> Visite</option>
                            <option value = "activite"> Activité</option>
                        </select>
                    </div></td>
                </tr>
                <td><label id ="labelprix" for="prix">Prix minimal <span class="required">*</span></label></td><td><input type="number" id="prix">€</td>
                <tr>
                    <label for=""></label>
                </tr>
                <tr>
                    <td><label for="dispo">Disponibilité </label></td>
                    <td><div class="custom-select-container">
                        <select class="custom-select" id = "dispo" name = "ladispo">
                            <option value="">Choisir une disponibilité</option>
                            <option value = "ouvert"> Ouvert </option>
                            <option value = "ferme"> Fermé </option>
                        </select>
                    </div></td>
                </tr>
                
                
                <tr>
                    <td><label for= "adresse">Adresse</label></td>
                    <td colspan="3"><input type="text" id="adresse" name ="adresse" placeholder="(ex : 1 rue Montparnasse)"></td>
                </tr>
                <tr>
                    <td><label for= "cp">Code Postal </label></td>
                    <td><input type="text" id="cp" name ="cp" placeholder="5 chiffres" size="local5"></td>
                    <td><label for= "ville">Ville <span class="required">*</span></label></td>
                    <td><input type="text" id="ville" name ="ville" placeholder="Nom de ville" required></td>
                
                </tr>
                <tr>
                    <td><label for="photo"> Photo <span class="required">*</span> (max. 5)</label></td>
                    <td><div>
                        <!-- <label for="file-upload">
                            <img src="/images/backOffice/icones/plus.png" alt="Uploader une image" class="upload-image" width="50px" height="50px">
                        </label> -->
                        <input id="photo" type="file" required/>
                    </div></td>
                    
                </tr>
               <tr>
                    <td><label for="type">Type de l'offre <span class="required">*</span></label></td>
                    <td><div class="custom-select-container">
                        <select class="custom-select" id = "type" name = "letype">
                            <option value = "standard"> Offre Standard </option>
                            <option value = "premium"> Offre Premium </option>
                        </select>
                    </div></td></tr>
            </table>
            <div id="options">
                    <label>Options</label>
                    <input type="checkbox" id="enRelief" name="enRelief"><label for="enRelief">En relief</label>
                    <input type="checkbox" id="alaune" name="alaune"><label for="alaune">A la une</label>
            </div>
                </td>
               </tr>
            </table>
        
            </div>
            
            <br>
            
            <h3>Tags de l'offre</h3>
            
            <p> -- Choisir une catégorie -- </p>
            <h3>A propos de l'offre</h3>
            <div class="apropos">
                <table border="0">
                    <tr>
                        <td><label for="descriptionC">Courte Description <span class="required">*</span></label></td>
                        <td><textarea id ="descriptionC" name="descriptionC" placeholder="Ecrire une courte description sur l’offre..." required></textarea></td>
                        
                    </tr>
                    <tr>
                        <td><label for="lien">Lien externe</label></td>
                        <td><input type="url" id="lien" name="lien" placeholder="Insérer un lien vers un site internet"></td>
                    </tr>
                    <tr>
                        <td><label for="tel">Numéro de téléphone</label></td>
                        <td><input type="tel" id="tel" name="mobile" pattern="[0-9]{10}" placeholder="(ex : 01 23 45 67 89)"></td>
                    </tr>
                </table>
            </div>
            
            <h3>Description détaillée de l'offre</h3>
            <textarea id="descriptionL" name="descriptionL" placeholder="Ecrire une description plus détaillée... "></textarea>
            
            <div id = "tarifs">
                <h3>Tarifs</h3>
                <input type="text" id="tarif1nom" name="tarif1nom" placeholder= "Nom du tarif">
                <input type="number" name="tarif1" min="0" placeholder="prix"><span>€</span>
                <br>
                <input type="text" id="tarif2nom" name="tarif2nom" placeholder= "Nom du tarif">
                <input type="number" name="tarif2" min="0" placeholder="prix"><span>€</span>
                <br>
                <input type="text" id="tarif3nom" name="tarif3nom" placeholder= "Nom du tarif">
                <input type="number" name="tarif3" min="0" placeholder="prix"><span>€</span>
                <br>
                <input type="text" id="tarif4nom" name="tarif4nom" placeholder= "Nom du tarif">
                <input type="number" name="tarif4" min="0" placeholder="prix"><span>€</span>
                <br>
                <label for="grilleT">Grille tarifaire complète</label>
                <input type="file" id="grilleT" name="grilleT">

            
            </div>
            <br>


    <h3>Ouverture</h3>
    <table border="0">
        <tr>
            <td>Lundi</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
        <tr>
            <td>Mardi</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
        <tr>
            <td>Mercredi</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
        <tr>
            <td>Jeudi</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
        <tr>
            <td>Vendredi</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
        <tr>
            <td>Samedi</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
        <tr>
            <td>Dimanche</td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
            <td>-></td>
            <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
        </tr>
    </table>
    <div class="bt_cree">
                <input class="valider" type="submit" value="Créer l'offre">

                <a href="#" id="back-to-top">
                    <img src="/images/backOffice/icones/fleche-vers-le-haut.png" alt="Retour en haut" width="50"
                        height="50">
                </a>
            </div>

        </form>
        


    </main>
    <div>
        

        
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
                    <a href="https://x.com/?locale=fr">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/x.png');"></div>
                    </a>
                    <a href="https://www.facebook.com/?locale=fr_FR">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/facebook.png');">
                        </div>
                    </a>
                    <a href="https://www.youtube.com/">
                        <div class="social-icon" style="background-image: url('/images/universel/icones/youtube.png');">
                        </div>
                    </a>
                    <a href="https://www.instagram.com/">
                        <div class="social-icon"
                            style="background-image: url('/images/universel/icones/instagram.png');"></div>
                    </a>
                </div>
            </div>


            <!-- Barre en bas du footer incluse ici -->

        </div>
        <div class="footer-bottom">
            Politique de confidentialité - Politique RGPD - <a href="mention_legal.html">Mentions légales</a> - Plan du
            site -
            Conditions générales - ©
            Redden’s, Inc.
        </div>
    </footer>

<?php
}
else {
    // Inclusion des paramètres de connexion
    include('connect_params.php');

    // Récupération des données du formulaire avec $_POST
    $titre = isset($_POST['titre']) ? $_POST['titre'] : '';
    $ville = isset($_POST['ville']) ? $_POST['ville'] : '';
    $resume = isset($_POST['descriptionC']) ? $_POST['descriptionC'] : '';
    $prix = isset($_POST['prix']) ? $_POST['prix'] : '';
    $type = isset($_POST['type']) ? $_POST['type'] : '';
    $photo1 = isset($_POST['photo1']) ? $_POST['photo1'] : '';
    $categorie = isset($_POST['categorie']) ? $_POST['categorie'] : '';

    $id_compte = isset($_SESSION['id_compte']) ? $_SESSION['id_compte'] : '';

    // Vérifier si l'id_compte est défini (s'il est connecté)
    if (!$id_compte) {
        die("Erreur : utilisateur non connecté.");
    }
    $requete .= '(titre, resume, ville) VALUES('$titre','$resume', '$ville');';
    

    try {
        // Connexion à la base de données
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

        // Début de la requête SQL
        $requete = "INSERT INTO offre_";
        
        // Déterminer la table cible selon la catégorie
        switch ($categorie) {
            case 'activite':
                $requete .= 'activite';
                break;
            case 'parc':
                $requete .= 'parc';
                break;
            case 'spectacle':
                $requete .= 'spectacle';
                break;
            case 'visite':
                $requete .= 'visite';
                break;
            default:
                print "Erreur de categorie!";
                die();
        }

        // Construction de la requête SQL avec les champs et les valeurs
        $requete .= "(titre, resume, ville) VALUES (:titre, :resume, :ville) returning id_offre";

        // Préparation de la requête
        $stmt = $dbh->prepare($requete);

        // Liaison des valeurs aux paramètres SQL
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':resume', $resume);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':id_compte', $id_compte);

        // Exécution de la requête
        $stmt->execute();


        // Récupérer l'ID retourné par la requête
        $offre_id = $stmt->fetchColumn();

        // Maintenant, insérer dans la vue 'tarif' avec l'ID de l'offre et le prix
        $requete_tarif = "INSERT INTO _tarif_publique (offre_id, prix) VALUES (:offre_id, :prix)";

        // Préparation de la requête pour la vue tarif
        $stmt_tarif = $dbh->prepare($requete_tarif);

        // Liaison des valeurs pour la vue tarif
        $stmt_tarif->bindParam(':offre_id', $offre_id);
        $stmt_tarif->bindParam(':prix', $prix);

        // Exécution de la requête pour insérer dans la vue tarif
        $stmt_tarif->execute();

        $requete .= "(titre, resume, ville) VALUES (:titre, :resume, :ville) RETURNING id";

        // Préparation de la requête
        $stmt = $dbh->prepare($requete);

        // Liaison des valeurs aux paramètres SQL
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':resume', $resume);
        $stmt->bindParam(':ville', $ville);

        // Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
        $stmt->execute();

        // Récupérer l'ID retourné par la requête
        $offre_id = $stmt->fetchColumn();

        // Maintenant, insérer dans la table 'image' avec l'ID de l'offre et l'ID de l'image
        $requete_image = "INSERT INTO offre_contient_image (id_offre, id_image) VALUES (:id_offre, :id_image)";

        // Préparation de la requête pour la table image
        $stmt_image = $dbh->prepare($requete_image);

        // Liaison des valeurs pour la table image
        $stmt_image->bindParam(':id_offre', $offre_id);
        $stmt_image->bindParam(':id_image', $photo1);  // On suppose que $photo1 est l'ID de l'image

        // Exécution de la requête pour insérer dans la table image
        $stmt_image->execute();

        // Fermeture de la connexion
        $dbh = null;
        
        print "Offre et tarif créés avec succès!";
    } catch (PDOException $e) {
        // Affichage de l'erreur en cas d'échec
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

    // Afficher un message pour confirmer la création de l'offre
    echo "offre creee";
   
}
?>    



    
    <script>
        let type = document.getElementById('type');
        type.addEventListener('change', function() {
            const type = this.value;
            console.log(type);
            if (type == "premium") {
                document.getElementById('options').style.display = 'block';
                document.getElementById('tarifs').style.display = 'block';
            } else{
                document.getElementById('options').style.display = 'none';
                document.getElementById('tarifs').style.display = 'none';
            }
        })

        let categorie = document.getElementById('categorie');
        categorie.addEventListener('change', function() {
            if (categorie == "restaurant"){
                document.getElementById("labelprix").replace("Prix minimal", 'Gamme de prix')
            }
        })
    </script>

</body>
</html>




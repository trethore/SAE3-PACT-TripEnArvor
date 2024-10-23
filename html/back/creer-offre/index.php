<?php
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
    <link rel="stylesheet" href="/style/styleguide.css" />
    <link rel="stylesheet" href="/style/style_HFB.css" />
    <link rel="stylesheet" href="/style/style_gereeOffre.css" />
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
        <form action="creationoffrepayante1.php" method="post" enctype="multipart/form-data" id="dynamicForm">
            <h3>Informations importante</h3>
            <div class="important">
            <table border="0">
                <tr>
                    <td><label for="titre">Titre <span class="required">*</span></label> </td>
                    <td colspan="3"><input type="text" id="titre" name ="titre" placeholder="Insérer un titre" required></td>
                </tr>
                <tr>
                <td><label for="categorie">Catégorie </label></td>
                    <td><div class="custom-select-container">
                        <select class="custom-select" id = "categorie" name = "lacat"> 
                            <option value="">Choisir une categorie</option>
                            <option value = "restaurant"> Restaurant</option>
                            <option value = "parc"> Parc d'attraction</option>
                            <option value = "spectacle"> Spectacle</option>
                            <option value = "visite"> Visite</option>
                            <option value = "activite"> Activité</option>
                        </select>
                    </div></td>
                </tr>
                <td><label for="prix">Prix minimal</label></td><td><input type="number" id="prix"></td>
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
                    <td colspan="3"><input type="text" id="adresse" name ="adresse" placeholder="(ex : 1 rue Montparnasse)" required></td>
                </tr>
                <tr>
                    <td><label for= "cp">Code Postal </label></td>
                    <td><input type="text" id="cp" name ="cp" placeholder="5 chiffres" size="5"></td>
                    <td><label for= "ville">Ville <span class="required">*</span></label></td>
                    <td><input type="text" id="ville" name ="ville" placeholder="Nom de ville" required></td>
                
                </tr>
                <tr>
                    <td><label for="photo"> Photo (max. 5)</label></td>
                    <td><div>
                        <!-- <label for="file-upload">
                            <img src="/images/backOffice/icones/plus.png" alt="Uploader une image" class="upload-image" width="50px" height="50px">
                        </label> -->
                        <input id="file-upload" type="file" />
                    </div></td>
                    
                </tr>
               <tr>
                    <td><label for="type">Type de l'offre <span>*</span></label></td>
                    <td><div class="custom-select-container">
                        <select class="custom-select" id = "type" name = "letype">
                            <option value="">Choisir le type d'offre</option>
                            <option value = "payante"> Offre Payante </option>
                            <option value = "gratuite"> Offre Gratuite </option>
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
                <input type="text" id="tarif1nom" name="tarif1nom" placeholder= "Nom du tarif" required>
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
    $titre = $_POST['titre'];
    $ville = $_POST['ville'];
    $resume = $_POST['descriptionC'];
    $prix = $_POST['prix'];
    $type = $_POST['type'];


include('connect_params.php');
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);


    $stmt = $dbh->prepare(
    "INSERT INTO Offre(id_offre,titre, resume, ville) VALUES('$titre','$resume', '$ville)");
    $stmt->execute();
    
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

}
?>    



    
    <script>
        let element = document.getElementById('type');
        element.addEventListener('change', function() {
            const type = this.value;
            console.log(type);
            if (type == "payante") {
                document.getElementById('options').style.display = 'block';
                document.getElementById('tarifs').style.display = 'block';
            } else{
                document.getElementById('options').style.display = 'none';
                document.getElementById('options').style.display = 'none';
            }
        })
    </script>

</body>
</html>




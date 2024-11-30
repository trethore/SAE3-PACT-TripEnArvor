<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/utils/offres-utils.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/utils/site-utils.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/utils/session-utils.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/utils/auth-utils.php");

    session_start();
    if (isset($_POST['titre'])) { // les autres svp²
        $submitted = true;
    } else {
        $submitted = false;
    }



    function get_file_extension($type)
    {
        $extension = '';
        switch ($type) {
            case 'image/png':
                $extension = '.png';
                break;
            case 'image/jpeg':
                $extension = '.jpg';
                break;
            case 'image/webp':
                $extension = '.webp';
                break;
            case 'image/gif':
                $extension = '.gif';
                break;
            default:
                die("probleme extension image");
                break;
        }
        return $extension;
    }

    $id_compte =  $_SESSION['id'];
    $isIdProPrivee = isIdProPrivee($id_compte);
    $isIdProPublique = isIdProPublique($id_compte);


    if ($isIdProPublique !== true) {
        $isIdProPublique = false;

    } else if ($isIdProPublique === true) {
        $isIdProPrivee = false;
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
    <header id="header">
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17">PACT Pro</div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input type="text" class="input-search" placeholder="Taper votre recherche...">
        </div>
        <a href="index.html"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte">><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
    </header>
    
    <?php if (!$submitted) { ?>
        <!-- <div id="offre">
            <h1>Valider les modifications</h1>
            <p>Voulez-vous valider les modifications<br>apporter à votre offre ?</p>
            <div class="close">
                <button class="bouton1" onclick="closeOffreAnnuler()"> Annuler </button>
                <button class="bouton2" onclick="closeOffreValider()"> Valider </button>
            </div>
        </div>
        <div id="modif">
            <h1>Valider les modifications</h1>
            <p>Voulez-vous valider les modifications<br>apporter à votre offre ?</p>
            <div class="close">
                <button class="bouton1" onclick="closeModifAnnuler()"> Annuler </button>
                <button class="bouton2" onclick="closeModifAnnuler()"> Valider </button>
            </div>
        </div>
        <div id="annuler">
            <h1>Valider les modifications</h1>
            <p>Voulez-vous valider les modifications<br>apporter à votre offre ?</p>
            <div class="close">
                <button class="bouton1" onclick="closeAnnulerAnnuler()"> Annuler </button>
                <button class="bouton2" onclick="closeAnnulerValider()"> Valider </button>
            </div>
        </div>
        <div id="quitter">
            <h1>Valider les modifications</h1>
            <p>Voulez-vous valider les modifications<br>apporter à votre offre ?</p>
            <div class="close">
                <button class="bouton1" onclick="closeQuitterAnnuler()"> Annuler </button>
                <button class="bouton2" onclick="closeQuitterValider()"> Valider </button>
            </div>
        </div> -->
        <main>
            
            <h2> Création d'une offre</h2>

            <form action="index.php" method="post" enctype="multipart/form-data" id="dynamicForm">

                <h3>Informations importantes</h3>

                <div class="important">
                    <table border="0">
                        <tr>
                            <td><label for="titre">Titre <span class="required">*</span></label> </td>
                            <td colspan="3"><input type="text" id="titre" name="titre" placeholder="Insérer un titre" required /></td>
                        </tr>
                        <tr>
                            <td><label for="categorie">Catégorie <span class="required">*</span></label></td>
                               <td> <div class="custom-select-container">
                                        <select class="custom-select" id="categorie" name="lacat">
                                            <option value="">Choisir une catégorie </option>
                                            <option value="restaurant"> Restaurant</option>
                                            <option value="parc"> Parc d'attraction</option>
                                            <option value="spectacle"> Spectacle</option>
                                            <option value="visite"> Visite</option>
                                            <option value="activite"> Activité</option>
                                        </select>
                                </div></td>
                        </tr>
                        <tr>
                            <td><label for="gammedeprix" id="labelgammedeprix">Gamme de prix <span class="required">*</span> </label></td>
                            <td><input type="text" id="gammedeprix" placeholder="€ ou €€ ou €€€" pattern="^€{1,3}$" name="gammeprix" /></td>
                        </tr>
                        <tr>
                            <td><label id="labeldispo" for="dispo">Disponibilité </label></td>
                            <td>
                                <!-- <div class="custom-select-container">
                                    <select class="custom-select" id="dispo" name="ladispo">
                                        <option value="">Choisir une disponibilité</option>
                                        <option value="ouvert"> Ouvert </option>
                                        <option value="ferme"> Fermé </option>
                                    </select> -->
                                </div>
                            </td>
                        </tr>


                <tr>
                    <!-- <td><label id="labeladresse" for="adresse">Adresse</label></td> -->
                    <td colspan="3"><input type="text" id="adresse" name="adresse" placeholder="(ex : 1 rue Montparnasse)" /></td>
                </tr>
                <tr>
                    <td><!--<label for="cp" id="labelcp">Code Postal </label>--></td>
                    <td><!-- <input type="text" id="cp" name="cp" placeholder="5 chiffres" size="local5" /> --></td>
                    <td><label for="ville">Ville <span class="required">*</span></label></td>
                    <td><input type="text" id="ville" name="ville" placeholder="Nom de ville" required /></td>
                </tr>
                <tr>
                    <td><label for="photo"> Photo <span class="required">*</span> (maximum 5)</label></td>
                    <td><div>
                            <!-- <label for="file-upload">
                                <img src="/images/backOffice/icones/plus.png" alt="Uploader une image" class="upload-image" width="50px" height="50px">
                            </label> -->
                            <input id="photo" type="file" name="photo" required />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><label id ="labeltype" for="type">Type de l'offre <span class="required">*</span></label></td>
                    <td>
                        <div class="custom-select-container" id="type">
                            <select class="custom-select" name="letype">
                                <option value="standard"> Offre Standard </option>
                                <option value="premium"> Offre Premium </option>
                            </select>
                        </div>
                                
                    </td>
                </tr>
                <tr>
                    <!-- <div id="options">
                        <td><label>Options</label></td>
                        <td><input type="radio" id="enRelief" name="option" value="enRelief"/><label for="enRelief">En relief</label>
                        <input type="radio" id="alaune" name="option" value="alaune"/><label for="alaune">A la une</label></td>
                    </div> -->
                </tr>
            </table>


            <div>
                <!-- activite, visite, spectacle -->
                <label id="labelduree" for="duree">Durée <span class="required">*</span> </label> <input type="text" id="duree" pattern="\d*" name="duree" /><label id="labelduree2">minutes</label>
                <!-- activité, parc -->
                <label id="labelage" for="age">Age Minimum <span class="required">*</span> </label> <input type="number" id="age" name="age" /> <label id="labelage2">an(s)</label>

                <br>
                <!-- spectacle -->
                <label id="labelcapacite" for="capacite">Capacité de la salle <span class="required">*</span> </label> <input type="number" id="capacite" name="capacite" /><label id="labelcapacite2" for="capacite">personnes</label>
                <br>
                <!-- parc -->
                <label id="labelnbattractions" for="nbattraction">Nombre d'attractions <span class="required">*</span> </label> <input type="number" id="nbattraction" name="attractions" />
                <label id="labelplan" for="plan">Importer le plan du parc <span class="required">*</span> </label> <input type="file" id="plan" name="plan" />
                <br>
                <!-- restaurant -->
                <label id="labelcarte" for="carte">Importer la carte du restaurant <span class="required">*</span> <input type="file" id="carte" name="carte" />
            </div>
                <br>
                </div>

                <!-- <h3>Tags de l'offre</h3> -->

                <!-- <p> -- Choisir une catégorie -- </p> -->
                <h3>A propos de l'offre</h3>
                <div class="apropos">
                    <table border="0">
                        <tr>
                            <td><label for="descriptionC">Courte Description <span class="required">*</span></label></td>
                            <td><textarea id="descriptionC" name="descriptionC" placeholder="Ecrire une courte description sur l’offre..." required></textarea></td>

                        </tr>
                        <!-- <tr>
                            <td><label for="lien">Lien externe</label></td>
                            <td><input type="url" id="lien" name="lien" placeholder="Insérer un lien vers un site internet" /></td>
                        </tr>
                        <tr>
                            <td><label for="tel">Numéro de téléphone</label></td>
                            <td><input type="tel" id="tel" name="mobile" pattern="[0-9]{10}" placeholder="(ex : 01 23 45 67 89)" /></td>
                        </tr> -->
                    </table>
                </div>

                    <!-- <h3>Description détaillée de l'offre</h3> -->
                    <!-- <textarea id="descriptionL" name="descriptionL" placeholder="Ecrire une description plus détaillée... "></textarea> -->

                <div id="tarifs">
                    <h3>Tarifs (minimum 1) <span class="required">*</span></h3>
                    <input type="text" id="nomtarif1" name="nomtarif1" placeholder="Nom du tarif" />
                    <input type="number" name="tarif1" min="0" placeholder="prix" /><span>€</span>
                    <br>
                    <input type="text" id="nomtarif2" name="nomtarif2" placeholder="Nom du tarif" />
                    <input type="number" name="tarif2" min="0" placeholder="prix" /><span>€</span>
                    <br>
                    <input type="text" id="nomtarif3" name="nomtarif3" placeholder="Nom du tarif" />
                    <input type="number" name="tarif3" min="0" placeholder="prix" /><span>€</span>
                    <br>
                    <input type="text" id="nomtarif4" name="nomtarif4" placeholder="Nom du tarif" />
                    <input type="number" name="tarif4" min="0" placeholder="prix" /><span>€</span>
                    <br>
                    <!-- <label for="grilleT">Grille tarifaire complète</label>
                    <input type="file" id="grilleT" name="grilleT" /> -->


                </div>
                <br>


                <!-- <h3>Ouverture</h3>
                <table border="0">
                    <tr>
                        <td>Lundi</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                    </tr>
                    <tr>
                        <td>Mardi</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                    </tr>
                    <tr>
                        <td>Mercredi</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                    </tr>
                    <tr>
                        <td>Jeudi</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                    </tr>
                    <tr>
                        <td>Vendredi</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                    </tr>
                    <tr>
                        <td>Samedi</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00"></td>
                    </tr>
                    <tr>
                        <td>Dimanche</td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                        <td>-></td>
                        <td><input type="text" class="time-input" placeholder="00"> h <input type="text" class="time-input" placeholder="00" /></td>
                    </tr>
                </table> -->
                <div class="bt_cree">
                    <input class="valider" type="submit" value="Créer l'offre" />

                    <a href="#" id="back-to-top">
                        <img src="/images/backOffice/icones/fleche-vers-le-haut.png" alt="Retour en haut" width="50"
                            height="50">
                    </a>
                </div>

            </form>
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
        } else {
            $id_compte =  $_SESSION['id'];
            $type = "standard";

            $resume = $_POST['descriptionC'];
            // Inclusion des paramètres de connexion
            include('../../php/connect_params.php');

            // Récupération des données du formulaire avec $_POST

            if (isset($_POST['titre'])) {
                $titre = $_POST['titre'];
            }

            if (isset($_POST['descriptionC'])) {
                $resume = $_POST['descriptionC'];
            }

            if (isset($_POST['ville'])) {
                $ville = $_POST['ville'];
            }

            if (isset($_POST['gammedeprix'])) {
                $gammedeprix = $_POST['gammedeprix'];
                $gammedeprix = intval($gammedeprix);
            }

            if (isset($_POST['photo'])) {
                $photo1 = $_FILE['photo'];
            }
            if (isset($_POST['duree'])) {
                $duree = $_POST['duree'];
                $duree = intval($duree);
            }
            if (isset($_POST['attractions'])) {
                $nbattraction = $_POST['attractions'];
                $nbattraction = intval($nbattraction);
            }
            if (isset($_POST['age'])) {
                $age = $_POST['age'];
                $age = intval($age);
            }

            if (isset($_POST['capacite'])) {
                $capacite = $_POST['capacite'];
                $capacite = intval($capacite);
            }
            if (isset($_POST['lacat'])) {
                $categorie = $_POST['lacat'];
            }
            if (isset($_POST['type'])) {
                $type = $_POST['type'];
            }
            

            if ($categorie !== "restaurant") {
                    
                if ((isset($_POST['tarif1']))&&(isset($_POST['nomtarif1']))) {
                            $tarif1 = $_POST['tarif1'];
                            $tarif1 = intval($tarif1);
                            $nomtarif1 = $_POST['nomtarif1'];
                            
                }
                else {
                    $tarif1 = 0;
                    $nomtarif1 = "nomtarif1";

                }
                $tarif_min = $tarif1;     
                $tabtarifs = array(
                $nomtarif1 => $tarif1
                );


                if ((isset($_POST['tarif2']))&&(isset($_POST['nomtarif2']))) {
                    $tarif2 = $_POST['tarif2'];
                    $tarif2 = intval($tarif2);
                    $tabtarifs[$_POST['nomtarif2']] = $tarif2;
                }
                if ((isset($_POST['tarif3'])) && (isset($_POST['nomtarif3']))) {
                    $tarif3 = $_POST['tarif3'];
                    $tarif3 = intval($tarif3);
                    $tabtarifs[$_POST['nomtarif3']] = $tarif3;
                }
                if ((isset($_POST['tarif4'])) && (isset($_POST['nomtarif4']))) {
                    $tarif4 = $_POST['tarif4'];
                    $tarif4 = intval($tarif4);
                    $tabtarifs[$_POST['nomtarif4']] = $tarif4;
                }

                foreach ($tabtarifs as $key => $value) {
                    if ($tarif_min > $value) {
                        $tarif_min = $value;
                    } 
                }

            }
            print_r($_POST);
            

            
            try {

            // Vérifier si l'id_compte est défini (s'il est connecté)
            if (!$id_compte) {
                die("Erreur : utilisateur non connecté.");
            }

            // Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

            
            $dbh->prepare("SET SCHEMA 'sae';")->execute();

                

            


           
            //INSERTION IMAGE dans _image
            $time = 'p' . strval(time());
            $file = $_FILES['photo'];
            $file_extension = get_file_extension($file['type']);

            if ($file_extension !== '') {
                move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/' . $time . $file_extension);


                $fichier_img = $time . $file_extension;

                $requete_image = 'INSERT INTO _image(lien_fichier) VALUES (?)';

                //preparation requete
                $stmt_image = $dbh->prepare($requete_image);

                //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                $stmt_image->execute([$fichier_img]);

            }

            $requete_verif = 'SELECT COUNT(*) FROM _image WHERE lien_fichier = ?';
            $stmt_verif = $dbh->prepare($requete_verif);
            $stmt_verif->execute([$fichier_img]);

            if ($stmt_verif->fetchColumn() > 0) {
                die("Erreur : Le fichier existe déjà dans la base de données.");
            }

            $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/universel/';
            $target_file = $target_dir . $time . $file_extension;

            if (file_exists($target_file)) {
                die("Erreur : Le fichier existe déjà dans le répertoire.");
            }
                
            $dbh->beginTransaction();
            // Déterminer la table cible selon la catégorie
            switch ($categorie) {
                case 'activite':
                    $requeteCategorie = 'activite';
                    break;
                case 'parc':
                    $requeteCategorie = 'parc_attraction';
                    break;
                case 'spectacle':
                    $requeteCategorie = 'spectacle';
                    break;
                case 'visite':
                    $requeteCategorie = 'visite';
                    break;
                case 'restaurant':
                        $requeteCategorie = 'restauration';
                default:
                    die("Erreur de categorie!");
            }

            //SWITCH CREATION REQUETE OFFRE
            switch ($categorie) {
                case 'activite':
                    $requete = "INSERT INTO sae.offre_". $requeteCategorie ."(titre, resume, ville, duree, age_min, id_compte_professionnel, prix_offre, type_offre) VALUES (?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                    
                    $stmt = $dbh->prepare($requete);
                    $stmt->execute([$titre, $resume, $ville, $duree, $age,  $id_compte, $tarif_min, $type]);

                    $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];


                    break;

                case 'parc':
                    $file = $_FILES['plan'];
                    $file_extension = get_file_extension($file['type']);
                    $time = 'p' . strval(time());

                    if ($file_extension !== '') {
                        move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/' . 'plan_' . $time . $file_extension);
                        $fichier_plan = 'plan_' . $time . $file_extension;

                        $requete_plan = 'INSERT INTO _image(lien_fichier) VALUES (?)';

                        //print $requete_image;

                        //preparation requete
                        $stmt_plan = $dbh->prepare($requete_plan);

                        //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                        $stmt_plan->execute([$fichier_img]);

                    }

                    $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, age_min, nb_attractions, plan, id_compte_professionnel, prix_offre, type_offre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                    $stmt = $dbh->prepare($requete);
                    $stmt->execute([$titre, $resume, $ville, intval($age), intval($nbattraction), $fichier_img, $id_compte, $tarif_min, $type]);

                    //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                    $requete_offre_contient_image = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                    $stmt_plan_offre = $dbh->prepare($requete_plan_offre);
                    $stmt_plan_offre->execute([$id_offre, $fichier_plan]);


                    $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];

                    break;

                case 'spectacle':
                    $type = "standard";
                    $requete = "INSERT INTO sae.offre_".$requeteCategorie." (titre, resume, ville, duree, capacite, id_compte_professionnel, prix_offre, type_offre) VALUES (?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                    $stmt = $dbh->prepare($requete);
                    $stmt->execute([$titre, $resume, $ville, intval($duree), intval($capacite), $id_compte, $tarif_min, $type]);

                        //print($requete);

                        $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                        /////TEST
                        // if (!$id_offre) {
                        //     throw new Exception("Erreur : l'insertion dans la table offre a échoué, id_offre est NULL.");
                        // }

                        // print_r("ID de l'offre insérée : " . $id_offre);
                    break;

                case 'visite':
                    $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, duree, id_compte_professionnel, prix_offre, type_offre) VALUES (?, ?, ?, ?, ?, ?, ?) returning id_offre";
                    $stmt = $dbh->prepare($requete);
                    $stmt->execute([$titre, $resume, $ville, $duree, $id_compte, $tarif_min, $type]);

                    $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                    break;

                case 'restaurant':
                    $file = $_FILE['carte'];
                    $file_extension = get_file_extension($file['type']);
                    $time = 'p' . strval(time());

                    if ($file_extension !== '') {
                        move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/' . 'carte_' . $time . $file_extension);
                        $fichier_carte= 'carte_' . $time . $file_extension;

                        $requete_carte = 'INSERT INTO _image(lien_fichier) VALUES (?)';

                        //print $requete_image;

                        //preparation requete
                        $stmt_carte = $dbh->prepare($requete_carte);

                        //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                        $stmt_carte->execute([$fichier_img]);

                        $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, gamme_prix, carte, id_compte_professionnel, prix_offre, type_offre) VALUES (?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                        $stmt = $dbh->prepare($requete);
                        $stmt->execute([$titre, $resume, $ville, $gammedeprix, $fichier_carte, $id_compte, $tarif_min, $type]);


                    }
            
                    //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                    $requete_offre_contient_image = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                    $stmt_plan_image = $dbh->prepare($requete_plan_offre);
                    $stmt_plan_image->execute([$id_offre, $fichier_carte]);

                    $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                    break;
                    
                    default:
                    die("Erreur de categorie!");
                }
                

                if ($file_extension !== '') {

                    //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE

                    $requete_offre_contient_image = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                    $stmt_image_offre = $dbh->prepare($requete_offre_contient_image);
                    $stmt_image_offre->execute([$id_offre, $fichier_img]);

                }

                $dbh->commit();


                if ($isIdProPrivee){
                    foreach ($tabtarifs as $key => $value) {
                        $requete_tarif = "INSERT INTO sae._tarif_publique(nom_tarif, prix,id_offre ) VALUES (?, ?, ?);";

                        // Préparation de la requête pour la vue tarif
                        $stmt_tarif = $dbh->prepare($requete_tarif);

                        // Exécution de la requête pour insérer dans la vue tarif
                        $stmt_tarif->execute([$key, $value, $id_offre]);
                    }
                }
                
                
                // Fermeture de la connexion
                $dbh = null;

                print "Offre créée avec succès!";
            } catch (PDOException $e) {
                // Affichage de l'erreur en cas d'échec
                print "Erreur !: " . $e->getMessage() . "<br/>";
                $dbh->rollBack();
                die();
            }
        }
        
        ?>

        <script>
            
            const isIdProPrivee = "<?php echo json_encode($isIdProPrivee) ?>";
            const isIdProPublique = "<?php echo json_encode($isIdProPublique) ?>";
            console.log(isIdProPublique);

            // if(isIdProPublique){
            //     document.getElementById("type").style.display = 'none';
            //     document.getElementById("labeltype").style.display = 'none';
            // }






            let typecategorie = document.getElementById('categorie');
            let typerestaurant = ["carte", "labelcarte"];
            let typevisite = ["labelduree", "duree", "labelduree2"];
            let typeactivite = ["labelage", "age", "labelage2", "labelduree", "duree", "labelduree2"];
            let typespectacle = ["labelduree", "duree", "labelduree2", "labelcapacite", "capacite", "labelcapacite2"];
            let typeparc = ["labelnbattractions", "nbattraction", "labelplan", "plan"];
            let obligatoireselontype = ["carte", "labelcarte", "labelgammedeprix", "gammedeprix", "labelage", "age", "labelage2", "labelduree", "duree", "labelduree2", "labelnbattractions", "nbattraction", "labelplan", "plan", "labelcapacite", "capacite", "labelcapacite2"];

            obligatoireselontype.forEach(element => {
                document.getElementById(element).style.display = 'none';
            });

            document.getElementById("tarifs").style.display = 'none';


            categorie.addEventListener('change', function() {
                let typeselectionne = categorie.value;
                // Afficher les champs selon la catégorie sélectionnée test
                switch (typeselectionne) {
                    case "restaurant":
                        afficheSelonType(typerestaurant);

                        if (isIdProPrivee) {
                            document.getElementById("labelgammedeprix").style.display = 'inline';
                            document.getElementById("gammedeprix").style.display = 'inline';
                        }
                        document.getElementById("tarifs").style.display = 'none';


                        break;

                    case "activite":
                        afficheSelonType(typeactivite);
                        break;

                    case "visite":
                        afficheSelonType(typevisite);
                        break;

                    case "spectacle":
                        afficheSelonType(typespectacle);
                        break;

                    case "parc":
                        afficheSelonType(typeparc);
                        break;

                    default:
                        console.log("Aucune catégorie sélectionnée.");
                }
            });



            function afficheSelonType(typechoisi) {
                obligatoireselontype.forEach(element => {
                    document.getElementById(element).style.display = 'none';
                });
                typechoisi.forEach(element => {
                    document.getElementById(element).style.display = 'inline';
                });
                if ((typechoisi !== "restaurant") && (isIdProPrivee)) {
                    document.getElementById("tarifs").style.display = 'inline';
                }
            }



            let offreDiv = document.getElementById("offre");

            function showOffre() {
                offreDiv.style.display = "block";
            }

            function closeOffreAnnuler() {
                offreDiv.style.display = "none";
            }

            function closeOffreValider() {
                offreDiv.style.display = "none";
                alert("Modification valider avec succès");
            }

            var modifDiv = document.getElementById("modif");

            function showModif() {
                modifDiv.style.display = "block";
            }

            function closeModifAnnuler() {
                modifDiv.style.display = "none";
            }

            function closeModifValider() {
                modifDiv.style.display = "none";
                alert("Modification valider avec succès");
            }

            var annulerDiv = document.getElementById("annuler");

            function showAnnuler() {
                annulerDiv.style.display = "block";
            }

            function closeAnnulerAnnuler() {
                annulerDiv.style.display = "none";
            }

            function closeAnnulerValider() {
                annulerDiv.style.display = "none";
                alert("Modification valider avec succès");
            }

            var quitterDiv = document.getElementById("quitter");

            function showQuitter() {
                quitterDiv.style.display = "block";
            }

            function closeQuitterAnnuler() {
                quitterDiv.style.display = "none";
            }

            function closeQuitterValider() {
                quitterDiv.style.display = "none";
                alert("Modification valider avec succès");
            }
        </script>

    </body>

</html>
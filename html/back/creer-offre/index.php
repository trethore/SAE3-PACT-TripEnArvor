<?php
    require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
    require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
    require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
    require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
    require_once($_SERVER['DOCUMENT_ROOT'] . DEBUG_UTILS);

    session_start();

    if (isset($_POST['titre'])) { // les autres svp²
        $submitted = true;
    } else {
        $submitted = false;
    }
    $photosDir = "../../images/universel/photos/";
    if (!is_dir($photosDir)) {
        if (mkdir($photosDir,0755,true)) {
            printInConsole("Dossier photo crée !");
        }
    }


    function get_file_extension($type) {
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

    $id_compte = $_SESSION['id'];
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
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">

</head>

<body>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');
startSession();
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $stmt = $dbh->prepare('SELECT * from sae._offre where id_compte_professionnel = ?');
    $stmt->execute([$_SESSION['id']]);
    $offres = $stmt->fetchAll(); // Récupère uniquement la colonne "titre"
    $dbh = null;
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des titres : " . $e->getMessage();
}
?>

<header>
        <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
        <div class="text-wrapper-17"><a href="/back/liste-back">PACT Pro</a></div>
        <div class="search-box">
            <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
            <input  autocomplete="off" role="combobox" id="input" name="browsers" list="cont" class="input-search" placeholder="Taper votre recherche...">
            <datalist id="cont">
                <?php foreach ($offres as $offre) { ?>
                    <option value="<?php echo htmlspecialchars($offre['titre']); ?>" data-id="<?php echo $offre['id_offre']; ?>">
                        <?php echo htmlspecialchars($offre['titre']); ?>
                    </option>
                <?php } ?>
            </datalist>
        </div>
        <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
        <a href="/back/mon-compte"><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const inputSearch = document.querySelector(".input-search");
                const datalist = document.querySelector("#cont");
                // Événement sur le champ de recherche
                inputSearch.addEventListener("input", () => {
                    // Rechercher l'option correspondante dans le datalist
                    const selectedOption = Array.from(datalist.options).find(
                        option => option.value === inputSearch.value
                    );
                    if (selectedOption) {
                        const idOffre = selectedOption.getAttribute("data-id");
                        //console.log("Option sélectionnée :", selectedOption.value, "ID:", idOffre);
                        // Rediriger si un ID valide est trouvé
                        if (idOffre) {
                            // TD passer du back au front quand fini
                            window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
                        }
                    }
                });
                // Debugging pour vérifier les options disponibles
                const options = Array.from(datalist.options).map(option => ({
                    value: option.value,
                    id: option.getAttribute("data-id")
                }));
                //console.log("Options disponibles dans le datalist :", options);
            });
        </script>
    </header>
    
    <?php if (!$submitted) { ?>
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
                                            <option value="" selected disabled hidden>Choisir une catégorie </option>
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
                            <td><input type="text" id="gammedeprix" placeholder="€ ou €€ ou €€€" pattern="^€{1,3}$" name="gammedeprix" /></td>
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
                        <div class="custom-select-container" id="divtype">
                            <select class="custom-select" name="letype" id="selectype">
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
                <label id="labelduree" for="duree">Durée <span class="required">*</span> </label> <input type="text" id="duree" pattern="\d*" name="duree" /><label id="labelduree2" for="duree">minutes</label>
                <!-- activité, parc -->
                <label id="labelage" for="age">Age Minimum <span class="required">*</span> </label> <input type="number" id="age" name="age" /> <label id="labelage2" for="age">an(s)</label>
                <!-- viste et spectacle -->
                <br>
                <label id="labeldate_event" for="date_event">Date de l'événement <span class="required">*</span></label>
                <input type="date" id="date_event" name="date_event">
               
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
                    <input class="valider" type="submit" id="valider" value="Créer l'offre" />

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

            $resume = $_POST['descriptionC'];
            // Inclusion des paramètres de connexion
            include('../../php/connect_params.php');

            // Récupération des données du formulaire avec $_POST
            
           
            if (!isset($_POST['date_event']) || empty($_POST['date_event'])) {
                $date_event = null;
            }else {
                $date_event = $_POST['date_event']; // La date de l'événement, par exemple '2024-12-19'
            }
           
            

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
            if (isset($_POST['type'])&&($isIdProPrivee)) {
                $type = $_POST['type'];
            }else {
                $type = "gratuit";
            }
            

            if ($categorie !== "restaurant") {
                    
                if ((isset($_POST['tarif1'])) && (isset($_POST['nomtarif1'])) && $_POST['tarif1'] !== "") {
                    $tarif1 = $_POST['tarif1'];
                    $tarif1 = intval($tarif1);
                    $nomtarif1 = $_POST['nomtarif1'];
                            
                }
                else {
                    $tarif1 = 0;
                    $nomtarif1 = "nomtarif1";

                }
               
                $tabtarifs = array(
                $nomtarif1 => $tarif1
                );
              

                if ((isset($_POST['tarif2'])) && (isset($_POST['nomtarif2'])) && $_POST['tarif2'] !== "") {
                    $tarif2 = $_POST['tarif2'];
                    $tarif2 = intval($tarif2);
                    $tabtarifs[$_POST['nomtarif2']] = $tarif2;
                }else
                if ((isset($_POST['tarif3'])) && (isset($_POST['nomtarif3'])) && $_POST['tarif3'] !== "") {
                    $tarif3 = $_POST['tarif3'];
                    $tarif3 = intval($tarif3);
                    $tabtarifs[$_POST['nomtarif3']] = $tarif3;
                }
                if ((isset($_POST['tarif4'])) && (isset($_POST['nomtarif4'])) && $_POST['tarif4'] !== "") {
                    $tarif4 = $_POST['tarif4'];
                    $tarif4 = intval($tarif4);
                    $tabtarifs[$_POST['nomtarif4']] = $tarif4;
                }

            }
            print_r($_POST);
            print_r($_FILES);
            

            
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
                    move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/photos/' . $time . $file_extension);


                    $fichier_img = $time . $file_extension;

                    $requete_image = 'INSERT INTO _image(lien_fichier) VALUES (?)';

                    //preparation requete
                    $stmt_image = $dbh->prepare($requete_image);

                    //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                    $stmt_image->execute([$fichier_img]);

                }

                // $requete_verif = 'SELECT COUNT(*) FROM _image WHERE lien_fichier = ?';
                // $stmt_verif = $dbh->prepare($requete_verif);
                // $stmt_verif->execute([$fichier_img]);

                // if ($stmt_verif->fetchColumn() > 0) {
                //     die("Erreur : Le fichier existe déjà dans la base de données.");
                // }


                $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/images/universel/photos/';
                $target_file = $target_dir . $time . $file_extension;


                // if (file_exists($target_file)) {
                //     die("Erreur : Le fichier existe déjà dans le répertoire.");
                // }
                    
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
                    case "restaurant":
                            $requeteCategorie = 'restauration';
                            break;
                    default:
                        die("Erreur de categorie!");
                }

                //SWITCH CREATION REQUETE OFFRE
                switch ($categorie) {
                    case 'activite':
                        $requete = "INSERT INTO sae.offre_". $requeteCategorie ."(titre, resume, ville, duree, age_min, id_compte_professionnel, prix_offre, abonnement) VALUES (?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                        
                        $stmt = $dbh->prepare($requete);
                        $stmt->execute([$titre, $resume, $ville, $duree, $age,  $id_compte, $tarif_min, $type]);

                        $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];


                        break;

                    case 'parc':
                        $file = $_FILES['plan'];
                        $file_extension = get_file_extension($file['type']);
                        $time = 'p' . strval(time());

                        if ($file_extension !== '') {
                            move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/photos/' . 'plan_' . $time . $file_extension);
                            $fichier_plan = 'plan_' . $time . $file_extension;

                            $requete_plan = 'INSERT INTO _image(lien_fichier) VALUES (?)';


                            //preparation requete
                            $stmt_plan = $dbh->prepare($requete_plan);

                            //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                            $stmt_plan->execute([$fichier_plan]);

                        }

                        $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, age_min, nb_attractions, plan, id_compte_professionnel, abonnement) VALUES (?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                        $stmt = $dbh->prepare($requete);
                        $stmt->execute([$titre, $resume, $ville, intval($age), intval($nbattraction), $fichier_img, $id_compte, $type]);

                        $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];

                        //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                        $requete_plan_offre = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                        $stmt_plan_offre = $dbh->prepare($requete_plan_offre);
                        $stmt_plan_offre->execute([$id_offre, $fichier_plan]);


                        

                        break;

                    case 'spectacle':

                        try {
                            // Insertion de la date dans la table _date
                            $reqInsertionDateEvent = "INSERT INTO sae._date (date) VALUES (?) RETURNING id_date";
                            $stmtInsertionDateEvent = $dbh->prepare($reqInsertionDateEvent);
                            $stmtInsertionDateEvent->execute([$date_event]);
                            $idDateEvent = $stmtInsertionDateEvent->fetch(PDO::FETCH_ASSOC)['id_date'];
                            print_r($idDateEvent);
                            print_r("here");
                        } catch (PDOException $e) {
                            echo "Erreur : " . $e->getMessage();
                            die();
                        }
                        try {
                            // Requête pour insérer l'offre dans _offre_spectacle
                            $requete = "INSERT INTO sae._offre_spectacle (titre, resume, ville, duree, capacite, id_compte_professionnel, abonnement, date_evenement) 
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_offre";
                        
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, intval($duree), intval($capacite), $id_compte, $type, $idDateEvent]);
                        
                            $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                            echo "L'offre a été insérée avec succès. ID de l'offre : " . $id_offre;
                        } catch (PDOException $e) {
                            echo "Erreur lors de l'insertion de l'offre : " . $e->getMessage();
                        }





                    case 'visite':
                        $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, duree, id_compte_professionnel, abonnement, date_evenement) VALUES (?, ?, ?, ?, ?, ?, ?) returning id_offre";
                        $stmt = $dbh->prepare($requete);
                        $stmt->execute([$titre, $resume, $ville, $duree, $id_compte, $type, $date_event]);

                        $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                        break;

                    case 'restaurant':
                        $file = $_FILES['carte'];
                        $file_extension = get_file_extension($file['type']);
                        $time = 'p' . strval(time());

                        if ($file_extension !== '') {
                            move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/photos/' . 'carte_' . $time . $file_extension);
                            $fichier_carte= 'carte_' . $time . $file_extension;

                            $requete_carte = 'INSERT INTO _image(lien_fichier) VALUES (?)';

                            //preparation requete
                            $stmt_carte = $dbh->prepare($requete_carte);

                            //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                            $stmt_carte->execute([$fichier_carte]);

                            $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, gamme_prix, carte, id_compte_professionnel, abonnement) VALUES (?, ?, ?, ?, ?, ?, ?) returning id_offre";
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, $gammedeprix, $fichier_carte, $id_compte, $type]); 


                        }

                        $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                
                        //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                        $requete_carte_offre = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                        $stmt_carte_image = $dbh->prepare($requete_carte_offre);
                        $stmt_carte_image->execute([$id_offre, $fichier_carte]);

                        
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


                    if (($isIdProPrivee)&&($categorie !== "restaurant")){
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

                echo "<script>
                        const redirect = confirm('Offre créée ! Cliquez sur OK pour continuer.');
                        if (redirect) {
                            window.location.href = '/back/liste-back/'
                        }
                  </script>"; //if premium afficher a changer si il faut voir les erreurs
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

            if(isIdProPublique){
                 document.getElementById("divtype").style.display = 'none';
                 document.getElementById("labeltype").style.display = 'none';
            }

            let typecategorie = document.getElementById('categorie');
            let typerestaurant = ["carte", "labelcarte"];
            let typevisite = ["labelduree", "duree", "labelduree2","labeldate_event", "date_event"];
            let typeactivite = ["labelage", "age", "labelage2", "labelduree", "duree", "labelduree2"];
            let typespectacle = ["labelduree", "duree", "labelduree2", "labelcapacite", "capacite", "labelcapacite2","labeldate_event", "date_event"];
            let typeparc = ["labelnbattractions", "nbattraction", "labelplan", "plan"];
            let obligatoireselontype = ["carte", "labelcarte", "labelgammedeprix", "gammedeprix", "labelage", "age", "labelage2", "labelduree", "duree", "labelduree2", "labelnbattractions", "nbattraction", "labelplan", "plan", "labelcapacite", "capacite", "labelcapacite2","labeldate_event",  "date_event"];

            obligatoireselontype.forEach(element => {
                document.getElementById(element).style.display = 'none';
            });

            document.getElementById("tarifs").style.display = 'none';


            categorie.addEventListener('change', function() {
                const typeselectionne = categorie.value;
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

            const boutonValider = document.getElementById("valider");
            const lacat = categorie.value; // Récupère la valeur de la catégorie
            
        
            boutonValider.addEventListener("click", function (event) {
                if (typeselectionne === "") {
                    event.preventDefault(); // Empêche la soumission
                    let pasDeCat = alert("Selectionner une categorie");
                }
            });

            // const tarif = tarif.value; // Récupère la valeur de la tarif

            // if((lacat !== "restaurant")&&(tabtarifjs.isEmpty === true)){
            //     boutonValider.addEventListener("click", function (event) {
            //         event.preventDefault(); // Empêche la soumission
            //         let pasdeTarif = alert("Remplir au moins 1 tarif");
            //     });
            // }
            // if((lacat !== "restaurant")&&(tabnomtarifjs.isEmpty === true)){
            //     boutonValider.addEventListener("click", function (event) {
            //         event.preventDefault(); // Empêche la soumission
            //         let pasdenomTarif = alert("Remplir au moins 1 nom de tarif");
            //     });
            // }
            // const gammeprix = gammedeprix.value;
            // if((lacat === "restaurant")&&(gammeprix.isEmpty === true)){
            //     boutonValider.addEventListener("click", function (event) {
            //         event.preventDefault(); // Empêche la soumission
            //         let pasdegammeprix = alert("Remplir la gamme de prix");
            //     });
            // }



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
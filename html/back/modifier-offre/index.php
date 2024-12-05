<?php
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);



if (isset($_POST['titre'])) { // les autres svp²
    $submitted = true;
} else {
    $submitted = false;
}

// Vérifier si l'utilisateur est connecté (si la session 'id' existe)
if (!isset($_SESSION['id'])) {
    // Si l'utilisateur n'est pas connecté, le rediriger vers la page de connexion
    echo "Pas connecté";
    exit;
} else {
    echo "Connecté  avec id : " . $_SESSION['id'];
} 

try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->prepare("SET SCHEMA 'sae';")->execute();
    $id_offre_cible = intval($_GET['id']);

// ===== GESTION DES OFFRES ===== //

    // ===== Requête SQL pour récupérer les informations d'une offre ===== //
    $offre = getOffre($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est une activité ===== //
    $activite = getActivite($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est une visite ===== //
    $visite = getVisite($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est un spectacle ===== //
    $spectacle = getSpectacle($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est un parc d'attractions ===== //
    $attraction = getParcAttraction($id_offre_cible);

    // ===== Requête SQL pour récupérer les informations d'une offre si l'offre est un restaurant ===== //
    $restaurant = getRestaurant($id_offre_cible);

// ===== GESTION DES ADRESSES ===== //

    // ===== Requête SQL pour récupérer les informations de l'adresse d'une offre ===== //
    $adresse = getAdresse($id_offre_cible); 

    // ===== GESTION DES COMPTES PROFESSIONNELS ===== //

    // ===== Requête SQL pour récupérer les informations du compte du propriétaire de l'offre ===== //
    $compte = getCompte($id_offre_cible);

// ===== GESTION DES IMAGES ===== //

    // ===== Requête SQL pour récuéprer les images d'une offre ===== //
    $images = getIMGbyId($id_offre_cible);

    // ===== GESTION DES TAGS ===== //

    // ===== Requête SQL pour récupérer les tags d'une offre ===== //
    $tags = getTags($id_offre_cible);

// ===== GESTION DES TARIFS ===== //

    // ===== Requête SQL pour récupérer les différents tarifs d'une offre ===== //
    $tarifs = getTarifs($id_offre_cible);

// ===== GESTION DE L'OUVERTURE ===== //

    // ===== Requête SQL pour récupérer les jours d'ouverture d'une offre ===== //
    //$jours = getJoursOuverture($id_offre_cible);
    
    // ===== Requête SQL pour récupérer les horaires d'ouverture d'une offre ===== //
    $horaire = getHorairesOuverture($id_offre_cible);

    // ===== GESTION DES CATEGORIES ===== //

    // ===== Requête SQL pour récupérer le type d'une offre ===== //
    $categorie = getTypeOffre($id_offre_cible);



    

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}

    $liste_tags = array("Culturel", "Patrimoine", "Histoire", "Urbain", "Nature", "Plein air", "Nautique", "Gastronomie", "Musée", "Atelier", "Musique", "Famille", "Cinéma", "Cirque", "Son et lumière", "Humour");
    $liste_tags_restaurant = array("Française", "Fruits de mer", "Asiatique", "Indienne", "Gastronomique", "Italienne", "Restauration rapide", "Creperie");

    $categorieBase = $categorie;

    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style/styleguide.css" />
    <link rel="stylesheet" href="/style/style_HFB.css" />
    <link rel="stylesheet" href="../../style/style_modifierOffre.css" />
    <title>Modifier offre</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">

</head>
    <body>
        <header id="header">
            <img class="logo" src="/images/universel/logo/Logo_blanc.png" />
            <div class="text-wrapper-17">PACT Pro</div>
            <div class="search-box">
                <button class="btn-search"><img class="cherchero" src="/images/universel/icones/chercher.png" /></button>
                <input type="text" class="input-search" placeholder="Taper votre recherche...">
            </div>
            <a href="/back/liste-back"><img class="ICON-accueil" src="/images/universel/icones/icon_accueil.png" /></a>
            <a href="/back/mon-compte">><img class="ICON-utilisateur" src="/images/universel/icones/icon_utilisateur.png" /></a>
        </header>
        
        <?php if (!$submitted) { ?>
            <div id="offre">
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
            </div>
            <main>
                
                <h2> Modifier <?php echo htmlentities($offre['titre']) ?> </h2>

                <form action="index.php" method="post" enctype="multipart/form-data" id="dynamicForm">

                    <h3>Informations importantes</h3>

                    <div class="important">
                        <table border="0">
                            <tr>
                                <td><label for="titre">Titre <span class="required">*</span></label> </td>
                                <td colspan="3"><input type="text" id="titre" name="titre" placeholder="Insérer un titre"  value="<?php echo htmlentities($offre['titre']) ?> " required/></td>
                            </tr>
                            <tr>
                                <td><label for="categorie">Catégorie</label> <?php echo $categorie ?></td>
                                <td><div class="custom-select-container">
                                        <select class="custom-select" id="categorie" name="lacat">
                                            <option value="restaurant" <?php if($categorie === "restaurant"){ "selected";} ?>> Restaurant</option>
                                            <option value="parc" <?php if($categorie === "parc"){echo "selected";} ?>> Parc d'attraction</option>
                                            <option value="spectacle" <?php if($categorie === "spectacle"){echo "selected";} ?>> Spectacle</option>
                                            <option value="visite" <?php if($categorie === "visite"){echo "selected";} ?>> Visite</option>
                                            <option value="activite" <?php if($categorie === "activite"){print("selected");} ?>> Activité</option>  
                                        </select>
                                
                                </div></td>
                            </tr>
                            <tr>
                                <td><label for="gammedeprix" id="labelgammedeprix">Gamme de prix <span class="required" >*</span> </label></td>
                                <td><input type="text" id="gammedeprix" placeholder="€ ou €€ ou €€€" pattern="^€{1,3}$" name="gammeprix" /></td>
                            </tr>
                            <tr>
                                <td><!-- <label id="labeldispo" for="dispo">Disponibilité </label>--></td> 
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
                        <td><label id="labeladresse" for="adresse">Adresse</label></td>
                        <td colspan="3"><input type="text" id="adresse" name="adresse" placeholder="(ex : 1 rue Montparnasse)" value="<?php echo htmlentities($adresse['num_et_nom_de_voie'] . $adresse['complement_adresse'] ) ?>"/></td>
                    </tr>
                    <tr>
                        <td><label for="cp" id="labelcp">Code Postal </label></td>
                        <td><input type="text" id="cp" name="cp" placeholder="5 chiffres" size="local5" value="<?php echo htmlentities($adresse['code_postal']) ?>"/></td>
                        <td><label for="ville">Ville <span class="required">*</span></label></td>
                        <td><input type="text" id="ville" name="ville" placeholder="Nom de ville" value="<?php echo htmlentities($adresse['ville'] )?>"required ></td>
                    </tr>
                    <tr>
                        <td><label for="photo"> Photo <span class="required">*</span> (maximum 5)</label></td>
                        <td><div>
                            <?php foreach ($images as $image) { ?>
                                <img src="/images/universel/photos/<?php echo htmlentities($image) ?>" alt="Image" >
                            <?php } ?>
                                <!-- <label for="file-upload">
                                    <img src="/images/backOffice/icones/plus.png" alt="Uploader une image" class="upload-image" width="50px" height="50px">
                                </label> -->
                                <input id="photo" type="file" name="photo" value="" />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label id ="labeltype" for="type">Type de l'offre (impossible de modifier le type)<span class="required">*</span></label></td>
                        <td>
                            <div class="custom-select-container" id="divtype">
                                <select class="custom-select" name="letype" id="selectype" disabled>
                                    <option value="standard"> Offre Standard </option>
                                    <option value="premium"> Offre Premium </option>
                                </select>
                            </div>
                                    
                        </td>
                    </tr>
                    <tr>
                        <div id="options">
                            <td><label>Options</label></td>
                            <td><input type="radio" id="enRelief" name="option" value="enRelief"/><label for="enRelief">En relief</label>
                            <input type="radio" id="alaune" name="option" value="alaune"/><label for="alaune">A la une</label></td>
                        </div>
                    </tr>
                </table>


                <div>
                    <!-- activite, visite, spectacle -->
                    <label id="labelduree" for="duree">Durée <span class="required">*</span> </label> <input type="text" id="duree" pattern="\d*" name="duree" value="<?php echo htmlentities($activite['duree']) ?>"/><label id="labelduree2" for="duree">minutes</label>
                    <!-- activité, parc -->
                    <label id="labelage" for="age">Age Minimum <span class="required">*</span> </label> <input type="number" id="age" name="age"value="<?php echo htmlentities($activite['age_min']) ?>"/> <label id="labelage2" for="age">an(s)</label>

                    <br>
                    <!-- spectacle -->
                    <label id="labelcapacite" for="capacite">Capacité de la salle <span class="required">*</span> </label> <input type="number" id="capacite" name="capacite" value="<?php echo htmlentities($activite['capacite'])?? '' ?>"/><label id="labelcapacite2" for="capacite">personnes</label>
                    <br>
                    <!-- parc -->
                    <label id="labelnbattractions" for="nbattraction">Nombre d'attractions <span class="required">*</span> </label> <input type="number" id="capacite" name="capacite" value="<?php echo htmlentities($attraction['nbAttractions'] ?? ''); ?>">
                    <label id="labelplan" for="plan">Importer le plan du parc <span class="required">*</span> </label>  <img src="/images/universel/photos/<?php echo htmlentities($attraction[$plan]) ?>" alt="Plan" ><input type="file" id="plan" name="plan" />
                    <br>
                    <!-- restaurant -->
                    <label id="labelcarte" for="carte">Importer la carte du restaurant <span class="required">*</span> <img src="/images/universel/photos/<?php echo htmlentities($restaurant[$carte]) ?>" alt="Carte" > <input type="file" id="carte" name="carte" />
                    
                </div>
                <?php if(isset($activite['duree'])){
                    echo htmlentities($activite['duree']);
                }else{
                    echo "pas dispo";
                }

                ?>
                    <br>
                    </div>

                    <h3>Tags de l'offre</h3>
                    <ul>
                    <?php 
                        if (!empty($tags)) {
                            foreach ($tags as $tag) { ?>
                                <li><input type="checkbox" id="<?php echo htmlentities($tag['nom_tag']); ?>" name="<?php echo htmlentities($tag['nom_tag']); ?>" value="<?php echo htmlentities($tag['nom_tag']); ?>" checked> <?php echo htmlentities($tag['nom_tag']); ?></li>
                    <?php } }
                        foreach($liste_tags as $tag){ 
                            if(!in_array($tag, $tags)){ ?>
                            <li><input type="checkbox" id="<?php echo htmlentities($tag); ?>" name="<?php echo htmlentities($tag); ?>" value="<?php echo htmlentities($tag); ?>"> <?php echo htmlentities($tag); ?></li>
                        <?php }}
                        foreach ($liste_tags_restaurant as $tag) { 
                            if(!in_array($tag, $tags)){ ?>
                            <li><input type="checkbox" id="<?php echo htmlentities($tag); ?>" name="<?php echo htmlentities($tag); ?>" value="<?php echo htmlentities($tag); ?>"> <?php echo htmlentities($tag); ?></li>
                   
                   <?php }}
                         ?>
                        
                     </ul>   
                    <h3>A propos de l'offre</h3>
                    <div class="apropos">
                        <table border="0"> 
                            <tr>
                                <td><label for="descriptionC">Courte Description <span class="required">*</span></label></td>
                                <td><textarea id="descriptionC" name="descriptionC" placeholder="Ecrire une courte description sur l’offre..." required><?php echo htmlentities($offre['resume']) ?></textarea></td>

                            </tr>
                            <tr>
                                <td><label for="lien">Lien externe</label></td>
                                <td><input type="text" id="lien" name="lien" placeholder="Insérer un lien vers un site internet" value="<?php echo htmlentities($offre['site_web']); ?>"/></td>
                            </tr>
                            <tr>
                                <td><label for="tel">Numéro de téléphone</label></td>
                                <td><input type="tel" id="tel" name="mobile" pattern="[0-9]{10}" placeholder="(ex : 01 23 45 67 89)" value="<?php echo htmlentities($compte['tel']); ?>"/></td>
                            </tr>
                        </table>
                    </div>

                        <h3>Description détaillée de l'offre</h3>
                        <textarea id="descriptionL" name="descriptionL" placeholder="Ecrire une description plus détaillée... "><?php echo nl2br(htmlentities($offre['description_detaille'] ?? " ")); ?></textarea>

                        <div id="tarifs">
                            
                            <h3>Tarifs (minimum 1) <span class="required">*</span></h3>
                            <?php  
                            $i = 0; // Compteur pour les champs
                            // Boucle pour afficher les tarifs existants
                            foreach ($tarifs as $t) { 
                                $i++; ?>
                                <input type="text" id="nomtarif<?php echo $i; ?>" name="nomtarif<?php echo $i; ?>" placeholder="Nom du tarif" value="<?php echo htmlentities($t['nom_tarif']); ?>" />
                                <input type="number" id="tarif<?php echo $i; ?>" name="tarif<?php echo $i; ?>" min="0" placeholder="prix" value="<?php echo htmlentities($t['prix']); ?>" /><span>€</span> 
                                <br>
                            <?php 
                            }
                            // Complète les champs vides si moins de 4
                            while ($i < 4) { 
                                $i++; ?>
                                <input type="text" id="nomtarif<?php echo $i; ?>" name="nomtarif<?php echo $i; ?>" placeholder="Nom du tarif" />
                                <input type="number" id="tarif<?php echo $i; ?>" name="tarif<?php echo $i; ?>" min="0" placeholder="prix" /><span>€</span> 
                                <br>
                            <?php 
                            } ?>

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
                        <button class="valider" type="submit" value="Modifier l'offre" onclick="location.href='../../back/modifier-offre/index.php?id=<?php echo $id_offre_cible ?>'" >

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
                </div>
            </footer>
        <?php } else {
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

            if (isset($_POST['photo'])) {
                $photo1 = $_FILE['photo'];
            }else{
                $photo1 = $images[0];
            }
            

            if(isset($_POST['adresse'])){
                $adresse = $_POST['adresse'];
            }else {
                $adresse =null;
            }
            if(isset($_POST['cp'])){
                $cp = $_POST['cp'];
            }else {
                $cp = null;
            }
            if(isset($_POST['option'])){
                $option = $_POST['option'];
            }else {
                $option = null;
            }
            if(isset($_POST['lien'])){
                $lien = $_POST['lien'];
            }else {
                $lien = null;
            }
            if(isset($_POST['tel'])){
                $option = $_POST['tel'];
            }else {
                $tel = null;
            }
            $pays = "France";
            $id_adresse =null;


            if ($categorie !== "restaurant") {
                foreach ($liste_tags as $tag) {
                    if (isset($_POST[$tag['nom_tag']])) {
                        $tagsSelectionnes[] = $tag;// Ajoute uniquement le nom du tag
                    }
                }
            }
           
            $descriptionL = $_POST['descriptionL'];
            
             
             try {

                // Vérifier si l'id_compte est défini (s'il est connecté)
                if (!$id_compte) {
                    die("Erreur : utilisateur non connecté.");
                }

                // Connexion à la base de données
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    
                $dbh->beginTransaction();
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




                if($categorieBase === $categorie){ //SI LA CATEGORIE N'A PAS CHANGE

                    if ((isset($_POST['cp']))&&(isset($_POST['adresse']))) {
                        if ($comp_adresse === '') {$comp_adresse = null;}
                        // Requete SQL pour modifier la table adresse
                        $query = "UPDATE sae._adresse 
                                    set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                        where id_adresse = (select id_adresse from sae._compte where id_offre = ?) returning id_adresse;";
                        $stmt = $conn->prepare($query);
                        $stmt->execute([$adresse, $comp_adresse, $cp, $ville, $pays, $id_offre]);
                        $id_adresse = $stmt->fetch()['id_adresse'];
                        
                    }



                    switch ($categorie) {
                        case 'activite':
                           
                            // Requete SQL pour modifier la vue offre
                            $query = "UPDATE sae.offre_activite
                            set ((titre, resume, ville, duree, age_min, id_compte_professionnel, prix_offre, abonnement, description_detaille, site_web, id_adressse) = (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            where id_offre = ?;";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $duree, $age,  $id_compte, $tarif_min, $type, $resume, $descriptionL, $lien, $id_adresse, $id_offre]);
                            
                            break;

                        case 'parc' :
                            if(isset( $_FILES['plan'])){
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
                            }else{
                                $fichier_plan = $attraction(['plan']);
                            }
                            
                            // Requete SQL pour modifier la vue offre
                            $query = "UPDATE sae.offre_parc
                            set (titre, resume, ville, age_min, nb_attractions, plan, id_compte_professionnel, abonnement, description_detaille, site_web, id_adresse) = (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            where id_offre = ?;";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $age, $nbattraction,$fichier_plan, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_offre]);
                            
                            //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                            $requete_plan_offre = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                            $stmt_plan_offre = $dbh->prepare($requete_plan_offre);
                            $stmt_plan_offre->execute([$id_offre, $fichier_plan]);
                            
                            break;

                        case 'spectacle':
                            // Requete SQL pour modifier la vue offre
                            $query = "UPDATE sae.offre_spectacle
                            set (titre, resume, ville, duree, capacite, id_compte_professionnel, abonnement, description_detaille, site_web, id_adresse) = (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            where id_offre = ?;";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $duree, $capacite, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_offre]);
                            break;
                        
                        case 'visite' :
                            $query = "UPDATE sae.offre_visite
                            set (titre, resume, ville, duree, id_compte_professionnel, abonnement, description_detaille, site_web, id_adresse) = (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            where id_offre = ?;";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $duree, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_offre]);
                            break;
                        
                        case 'restaurant':

                            if(isset( $_FILES['carte'])){
                                $file = $_FILES['carte'];
                                $file_extension = get_file_extension($file['carte']);
                                $time = 'p' . strval(time());
                                    if ($file_extension !== '') {
                                        move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/images/universel/photos/' . 'carte_' . $time . $file_extension);
                                        $fichier_carte = 'carte_' . $time . $file_extension;
            
                                        $requete_carte = 'INSERT INTO _image(lien_fichier) VALUES (?)';
            
            
                                        //preparation requete
                                        $stmt_carte = $dbh->prepare($requete_carte);
            
                                        //Exécution de la requête pour insérer dans la table offre_ et récupérer l'ID
                                        $stmt_carte->execute([$fichier_carte]);
            
                                    }
                            }else{
                                $fichier_carte = $restaurant(['carte']);
                            }
                            
                            // Requete SQL pour modifier la vue offre
                            $query = "UPDATE sae.offre_restauration
                            set (titre, resume, ville, gamme_prix, carte, id_compte_professionnel, abonnement, description_detaille, site_web, id_adresse) = (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                            where id_offre = ?;";
                            $stmt = $conn->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $gammedeprix ,$fichier_carte, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_offre]);
                            
                            //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                            $requete_carte_offre = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                            $stmt_carte_offre = $dbh->prepare($requete_plan_offre);
                            $stmt_carte_offre->execute([$id_offre, $fichier_plan]);
                            
                            break;


                        
                        default:
                            die("Erreur update!");
                            break;
                    }






                }else{
                
                    
                

                    //SWITCH CREATION REQUETE OFFRE
                    switch ($categorie) {
                        case 'activite':
                            try{
                                    // Requête SQL pour supprimer une visite
                                    $requete = "DELETE FROM sae.offre_activite WHERE id_offre = ?";
                                
                                    // Préparation et exécution
                                    $stmt = $dbh->prepare($requete);
                                    $stmt->execute([$id_offre]);
                                
                                    echo "La visite avec l'ID $id_offre a été supprimée avec succès.";
                                } catch (PDOException $e) {
                                    echo "Erreur !: " . $e->getMessage();
                                
                            }
                            $requete = "INSERT INTO sae.offre_activite(titre, resume, ville, duree, age_min, id_compte_professionnel, prix_offre, abonnement, description_detaille, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                            
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, $duree, $age,  $id_compte, $tarif_min, $type, $resume, $descriptionL, $lien]);

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

                            try{
                                // Requête SQL pour supprimer une visite
                                $requete_supprimer = "DELETE FROM sae.offre_parc WHERE id_offre = ?";
                            
                                // Préparation et exécution
                                $stmt = $dbh->prepare($requete_supprimer);
                                $stmt->execute([$id_offre]);
                            
                                echo "La visite avec l'ID $id_offre a été supprimée avec succès.";
                            } catch (PDOException $e) {
                                echo "Erreur !: " . $e->getMessage();
                            }

                            $requete = "INSERT INTO sae.offre_parc(titre, resume, ville, age_min, nb_attractions, plan, id_compte_professionnel, abonnement, description_detaille, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, intval($age), intval($nbattraction), $fichier_img, $id_compte, $type, $descriptionL, $lien]);

                            $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];

                            //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                            $requete_plan_offre = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                            $stmt_plan_offre = $dbh->prepare($requete_plan_offre);
                            $stmt_plan_offre->execute([$id_offre, $fichier_plan]);


                            

                            break;

                        case 'spectacle':
                            try{
                                // Requête SQL pour supprimer une visite
                                $requete_supprimer = "DELETE FROM sae.offre_spectacle WHERE id_offre = ?";
                            
                                // Préparation et exécution
                                $stmt = $dbh->prepare($requete_supprimer);
                                $stmt->execute([$id_offre]);
                            
                                echo "La visite avec l'ID $id_offre a été supprimée avec succès.";
                            } catch (PDOException $e) {
                                echo "Erreur !: " . $e->getMessage();
                            }
                            $requete = "INSERT INTO sae.offre_spectacle (titre, resume, ville, duree, capacite, id_compte_professionnel, abonnement, description_detaille, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, intval($duree), intval($capacite), $id_compte, $type, $descriptionL, $lien]);

                                $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];
                            
                            break;

                        case 'visite':
                            try{
                                // Requête SQL pour supprimer une visite
                                $requete_supprimer = "DELETE FROM sae.offre_visite WHERE id_offre = ?";
                            
                                // Préparation et exécution
                                $stmt = $dbh->prepare($requete_supprimer);
                                $stmt->execute([$id_offre]);
                            
                                echo "La visite avec l'ID $id_offre a été supprimée avec succès.";
                            } catch (PDOException $e) {
                                echo "Erreur !: " . $e->getMessage();
                            }
                            $requete = "INSERT INTO sae.offre_visite(titre, resume, ville, duree, id_compte_professionnel, abonnement, description_detaille, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, $duree, $id_compte, $type, $descriptionL, $lien]);

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

                                $requete = "INSERT INTO sae.offre_".$requeteCategorie."(titre, resume, ville, gamme_prix, carte, id_compte_professionnel, abonnement, description_detaille, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                                $stmt = $dbh->prepare($requete);
                                $stmt->execute([$titre, $resume, $ville, $gammedeprix, $fichier_carte, $id_compte, $type, $descriptionL, $lien]); 


                            }
                            try{
                                // Requête SQL pour supprimer une visite
                                $requete_supprimer = "DELETE FROM sae.offre_restaurant WHERE id_offre = ?";
                            
                                // Préparation et exécution
                                $stmt = $dbh->prepare($requete_supprimer);
                                $stmt->execute([$id_offre]);
                            
                                echo "La visite avec l'ID $id_offre a été supprimée avec succès.";
                            } catch (PDOException $e) {
                                echo "Erreur !: " . $e->getMessage();
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


                        

                        
                    }


                    //INSERTION DANS TARIF
                    if (($isIdProPrivee)&&($categorie !== "restaurant")){
                         // Requête SQL pour supprimer une visite
                         $requete_supprimer = "DELETE FROM sae.offre_tarif_publique WHERE id_offre = ?";
                            
                         // Préparation et exécution
                         $stmt = $dbh->prepare($requete_supprimer);
                         $stmt->execute([$id_offre]);

                        foreach ($tabtarifs as $key => $value) {
                            $requete_tarif = "INSERT INTO sae._tarif_publique(nom_tarif, prix, id_offre) VALUES (?, ?, ?);";

                            // Préparation de la requête pour la vue tarif
                            $stmt_tarif = $dbh->prepare($requete_tarif);

                            // Exécution de la requête pour insérer dans la vue tarif
                            $stmt_tarif->execute([$key, $value, $id_offre]);
                        }
                    }

                    // Fermeture de la connexion
                    $dbh = null;





                echo "<script>
                        const redirect = confirm('Offre modifiée ! Cliquez sur OK pour continuer.');
                        if (redirect) {
                            window.location.href = '/back/liste-back/'
                        }
                  </script>";

            }catch(PDOException $e){
                // Affichage de l'erreur en cas d'échec
                print "Erreur !: " . $e->getMessage() . "<br/>";
                $dbh->rollBack();
                die();
            }

        }?>
        <script> 
                
            liste_tags.forEach(element => {
                
            });
                        // if(!liste_tags.include()){
                        //     if($categorie != "restaurant")
                        //     foreach ($liste_tags as $tag)
                        //     <li><input type="checkbox" name="" value=""> </li>
                        // }



            const isIdProPrivee = "<?php echo json_encode($isIdProPrivee) ?>";
            const isIdProPublique = "<?php echo json_encode($isIdProPublique) ?>";
            console.log(isIdProPublique);

            if(isIdProPublique){
                 document.getElementById("divtype").style.display = 'none';
                 document.getElementById("labeltype").style.display = 'none';
            }

            const liste_tags = "<?php echo json_encode($liste_tags) ?>";
            const liste_tags_restaurant = "<?php echo json_encode($liste_tags_restaurant) ?>";
            const $tags = "<?php echo json_encode($tags) ?>"

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
                        afficherTags(typeparc);
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

            function afficherTags(typechoisi){
                if (typeselectionne === "restaurant"){
                    liste_tags.forEach(tag => {
                        if(!tags.includes(tag)){
                            document.getElementById(tag).style.display ='none';
                        }
                    });
                }else{
                    liste_tags_restaurant.forEach(tag => {
                        if(!tags.includes(tag)){
                            document.getElementById(tag).style.display ='none';
                        }
                    });
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



        </script>
    </body>
</html>
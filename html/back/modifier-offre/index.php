<?php
session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . DEBUG_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);


$id_offre_cible = intval($_SESSION['id_offre'] = $_GET['id']);

$id_compte = $_SESSION['id'];
$isIdProPrivee = isIdProPrivee($id_compte);
$isIdProPublique = isIdProPublique($id_compte);


if ($isIdProPublique !== true) {
    $isIdProPublique = false;

} else if ($isIdProPublique === true) {
    $isIdProPrivee = false;
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

function supprimerAccents($chaine) {
    // Tableau des caractères avec accents et leur équivalent sans accents
    $accents = [
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'ae',
        'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'í' => 'i',
        'ñ' => 'n',
        'ò' => 'o', 'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'õ' => 'o', 'ø' => 'o',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
        'ý' => 'y', 'ÿ' => 'y',
        'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Á' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Æ' => 'AE',
        'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Í' => 'I',
        'Ñ' => 'N',
        'Ò' => 'O', 'Ô' => 'O', 'Ö' => 'O', 'Ó' => 'O', 'Õ' => 'O', 'Ø' => 'O',
        'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ú' => 'U',
        'Ý' => 'Y'
    ];

    // Remplacement des caractères
    return strtr($chaine, $accents);
}
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
    $restauration = getRestaurant($id_offre_cible);

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

   
    
    
    function bon_get_selon_categorie($id_offre_cible, $categorie){
        switch ($categorie) {
            case 'activite':
                $offre_bonne_cat = getActivite($id_offre_cible);
                break;
            case 'parcattraction':
                $offre_bonne_cat = getParcAttraction($id_offre_cible);
                break;
            case 'restauration':
                $offre_bonne_cat = getRestaurant($id_offre_cible);
                break;
            case 'spectacle':
                $offre_bonne_cat = getSpectacle($id_offre_cible);
                
                break;
            case 'visite':
                $offre_bonne_cat = getVisite($id_offre_cible);
                break;
            default:
                die("Erreur de function bon get selon categorie!");
        }
        return $offre_bonne_cat;
    }
    

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}

print_r($tags);

    // Extraire les noms des tags
    $tag_names = array_map(function($tag) {
        return $tag['nom_tag'];
    }, $tags);

    $liste_tags = array("Culturel", "Patrimoine", "Histoire", "Urbain", "Nature", "Plein air", "Nautique", "Gastronomie", "Musée", "Atelier", "Musique", "Famille", "Cinéma", "Cirque", "Son et lumière", "Humour");
    $liste_tags_restauration = array("Française", "Fruits de mer", "Asiatique", "Indienne", "Gastronomique", "Italienne", "Restauration rapide", "Creperie");

    $categorie = preg_replace('/\s+/', '', strtolower(supprimerAccents($categorie))); //formatage de $categorie

    print($categorie);
    $categorieBase = $categorie;

    $offre_bonne_cat = bon_get_selon_categorie($id_offre_cible, $categorie);
    print_r($offre_bonne_cat);

    if (($categorie == 'spectacle')) {
        $date_evenement = getDateSpectacle($id_offre_cible);
        $date_evenement = $date_evenement[0]['date'];
    }elseif ($categorie == 'visite') {
        $date_evenement = getDateVisite($id_offre_cible);
        $date_evenement = $date_evenement[0]['date'];
    }else {
        $date_evenement = null; // Gestion par défaut si aucune catégorie ne correspond
    }

    print_r (getDateSpectacle($id_offre_cible));
    echo ($date_evenement);

    print_r($adresse);
    

    

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/style.css" />
    <link rel="stylesheet" href="../../style/style_modifierOffre.css" />
    <title>Modifier offre</title>
    <link rel="icon" type="image/jpeg" href="/images/universel/logo/Logo_icone.jpg">

</head>
    <body class="back modifier-offre">
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
                <?php foreach ($offres as $offreT) { ?>
                    <option value="<?php echo htmlspecialchars($offreT['titre']); ?>" data-id="<?php echo $offreT['id_offre']; ?>">
                        <?php echo htmlspecialchars($offreT['titre']); ?>
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

                <form action="index.php?id=<?php echo $id_offre_cible ?>" method="post" enctype="multipart/form-data" id="dynamicForm">

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
                                            <option value="restauration" <?php if($categorie === "restauration"){ echo "selected";} ?>> Restauration</option>
                                            <option value="parcattraction" <?php if($categorie === "parcattraction"){echo "selected";} ?>> Parc d'attraction</option>
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
                        <td colspan="3"><input type="text" id="adresse" name="adresse" placeholder="(ex : 1 rue Montparnasse)" value="
                        <?php if (isset($adresse['num_et_nom_de_voie'])) {
                            echo htmlentities($adresse['num_et_nom_de_voie']);
                            if (isset($adresse['complement_adresse'] )){
                                echo htmlentities($adresse['complement_adresse']);
                            }; } ?>"/></td>
                    </tr>
                    <tr>
                        <td><label for="cp" id="labelcp">Code Postal </label></td>
                        <td><input type="text" id="cp" name="cp" placeholder="5 chiffres" size="local5" value="<?php 
                        if (isset($adresse['code_postal'])) {
                            echo htmlentities($adresse['code_postal']); } ?>"/></td>
                        <td><label for="ville">Ville <span class="required">*</span></label></td>
                        <td><input type="text" id="ville" name="ville" placeholder="Nom de ville" value="<?php if(isset($offre['ville'])) {echo htmlentities($offre['ville']); } ?>"required ></td>
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
                        <td><label id ="labeltype" for="type">Type de l'offre<span class="required">*</span></label></td>
                        <td>
                            <div class="custom-select-container" id="divtype">
                                <select class="custom-select" name="letype" id="selectype" disabled>
                                    <option value="standard"> Offre Standard </option>
                                    <option value="premium"> Offre Premium </option>
                                </select>
                            </div>
                                    
                        </td>
                        <td>(impossible de modifier le type)</td>
                    </tr>
                    <tr>
                        <div id="options">
                            <td><label>Options</label></td>
                            <td><input type="radio" id="enRelief" name="optionPayante" value="enRelief"/><label for="enRelief">En relief</label>
                            <input type="radio" id="alaune" name="optionPayante" value="alaune"/><label for="alaune">A la une</label></td>
                        </div>
                    </tr>
                </table>


                <div>
                    <!-- activite, visite, spectacle -->
                    <label id="labelduree" for="duree">Durée <span class="required">*</span> </label> <input type="text" id="duree" pattern="\d*" name="duree" value=" <?php if(isset($offre_bonne_cat['duree'])){
                                                                                                                                                                        echo htmlentities($offre_bonne_cat['duree']);} ?>"/> <label id="labelduree2" for="duree">minutes</label><br>
                    
                    <!-- activité, parc -->
                    <label id="labelage" for="age">Age Minimum <span class="required">*</span> </label> <input type="number" id="age" name="age" value="<?php if(isset($offre_bonne_cat['age_min'])){ echo htmlentities($offre_bonne_cat['age_min']); }?>"/> 
                    <label id="labelage2" for="age">an(s)</label>
                    
                    <!-- activite CHANGER POUR PRESTATION -->
                    <br>
                    <label id="labelpresta" for="presta">Prestation proposée  <span class="required">*</span></label> <input type="text" id="presta" name="presta" value=" <?php if(isset($offre_bonne_cat['age_min'])){ echo htmlentities($offre_bonne_cat['age_min']); } ?>"/> 
                    <br>
                    <label id="labeldescpresta" for="descpresta">Description de la prestation  <span class="required">*</span></label> <input type="text" id="descpresta" name="descpresta" /> 
                
                    <!-- viste et spectacle -->
                    <br>
                    <label id="labeldate_event" for="date_event">Date et heure de l'événement<span class="required">*</span></label><input type="datetime-local" id="date_event" name="date_event" <?php if($date_evenement != null){ ?> value="<?php echo $date_evenement; ?>" <?php } ?>>
                    <br>
                    <!-- spectacle -->
                    <label id="labelcapacite" for="capacite">Capacité de la salle <span class="required">*</span> </label> <input type="number" id="capacite" name="capacite" value="<?php if(isset($offre_bonne_cat['capacite'])){
                                                                                                                                                                                    echo htmlentities($offre_bonne_cat['capacite']);} ?>"/>
                    <label id="labelcapacite2" for="capacite">personnes</label>
                    <br>
                    <!-- parc -->
                    <label id="labelnbattractions" for="nbattraction">Nombre d'attractions <span class="required">*</span> </label> <input type="number" id="nbattraction" name="nbattraction" value="<?php if(isset($offre_bonne_cat['nb_attractions'])){
                                                                                                                                                                                            echo htmlentities($offre_bonne_cat['nb_attractions']); } ?>">
                    <label id="labelplan" for="plan">Importer le plan du parc <span class="required">*</span> </label>  <?php if(isset($offre_bonne_cat['plan'])){ ?> <img src="/images/universel/photos/ <?php
                                                                                                                                                            echo htmlentities($offre_bonne_cat['plan']);  ?>"  > <?php } ?> <input type="file" id="plan" name="plan" />
                    <br>
                    <!-- restauration -->
                    <label id="labelcarte" for="carte">Importer la carte du restaurant <span class="required">*</span> <?php if(isset($offre_bonne_cat['carte'])){ ?> <img src="/images/universel/photos/ <?php 
                                                                                                                                                                    echo htmlentities($offre_bonne_cat['carte']); ?>"> <?php } ?> <input type="file" id="carte" name="carte" />
                    
                </div>
                
                    <br>
                    </div>

                    <h3>Tags de l'offre</h3>
                    <ul>
                    <?php 
                        if (!empty($tags)) {
                            foreach ($tags as $tag) { ?>
                                <li><input type="checkbox" id="<?php echo htmlentities($tag['nom_tag']); ?>" name="tag[]" value="<?php echo htmlentities($tag['nom_tag']); ?>" checked> <?php echo htmlentities($tag['nom_tag']); ?></li>
                    <?php } } 
                    foreach($liste_tags as $tag){ 
                            if(!in_array($tag, $tag_names)){ ?>
                            <li><input type="checkbox" id="<?php echo htmlentities($tag); ?>" name="tags[]" value="<?php echo htmlentities($tag); ?>"> <?php echo htmlentities($tag); ?></li>
                        <?php }}
                        foreach ($liste_tags_restauration as $tag) { 
                            if(!in_array($tag, $tag_names)){ ?>
                            <li><input type="checkbox" id="<?php echo htmlentities($tag); ?>" name="tag[]" value="<?php echo htmlentities($tag); ?>"> <?php echo htmlentities($tag); ?></li>
                   
                   <?php } 
                    } ?>
                         
                        
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
                                <td><input type="text" id="lien" name="lien" placeholder="Insérer un lien vers un site internet" value="<?php if(isset($offre['site_web'])){
                                                                                                                                        echo htmlentities($offre['site_web']); } ?>"/></td>
                            </tr>
                            
                        </table>
                    </div>

                        <h3>Description détaillée de l'offre</h3>
                        <textarea id="descriptionL" name="descriptionL" placeholder="Ecrire une description plus détaillée... "><?php if(isset($offre['description_detaille'])){
                                                                                                                                echo nl2br(htmlentities($offre['description_detaille'])); } ?></textarea>

                        <div id="tarifs">
                            
                            <h3>Tarifs (minimum 1) <span class="required">*</span></h3>
                            <?php  
                            $i = 0; // Compteur pour les champs
                            // Boucle pour afficher les tarifs existants
                            
                            foreach ($tarifs as $t) {
                                if(!($t['nom_tarif'] == "nomtarif1")){ //n'affiche pas nomtarif1 qui est la valeur par defeaut si aucun tarif n'est rentré lors de la creation de l'offre
                                    $i++; ?>
                                <input type="text" id="nomtarif<?php echo $i; ?>" name="nomtarif<?php echo $i; ?>" placeholder="Nom du tarif" value="<?php echo htmlentities($t['nom_tarif']); ?>" />
                                <input type="number" id="tarif<?php echo $i; ?>" name="tarif<?php echo $i; ?>" min="0" placeholder="prix" value="<?php echo htmlentities($t['prix']); ?>" /><span>€</span> 
                                <br>
                            <?php 
                                }
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
                        <input class="valider" type="submit" value="Modifier l'offre">

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
            $id_offre = $id_offre_cible;
            $id_compte = $_SESSION['id'];
            if (isset($_POST['titre'])) {
                $titre = $_POST['titre'];
            }

            if (isset($_POST['presta'])) {
                $presta = $_POST['presta'];
            }
            if (isset($_POST['descpresta'])) {
                $descpresta = $_POST['descpresta'];
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

            if (!isset($_POST['date_event']) || empty($_POST['date_event'])) {
                $date_event = null;
            }else {
                $date_event = $_POST['date_event'];
                $date_event = date('Y-m-d H:i:s'); // La date de l'événement, par exemple '2024-12-19'
            }

            if (isset($_POST['capacite'])) {
                $capacite = $_POST['capacite'];
                $capacite = intval($capacite);
            }

            if (isset($_POST['age'])) {
                $age = $_POST['age'];
                $age = intval($age);
            }

            if (isset($_POST['nbattraction'])) {
                $nbattraction = $_POST['nbattraction'];
                $nbattraction = intval($nbattraction);
            }
            if (isset($_POST['lacat'])) {
                $categorie = $_POST['lacat'];
            }
            if (isset($_POST['type'])&&($isIdProPrivee)) {
                $type = $_POST['type'];
            }else {
                $type = "gratuit";
            }
           
            if(isset($_POST['optionPayante'])){
                $optionP = $_POST['optionPayante'];
            }else {
                $optionP = null;
            }
            

            if ($categorie !== "restauration") {
                    
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
                $tel = $_POST['tel'];
            }else {
                $tel = null;
            }
            $pays = "France";
            $id_adresse =null;
            if (isset($_POST['lacat'])) {
                $categorie = $_POST['lacat'];
            }


            if ($categorie !== "restauration") {
                foreach ($liste_tags as $tag) {
                    if (isset($_POST[$tag])) {
                        $tagsSelectionnes[] = $tag;// Ajoute uniquement le nom du tag
                    }
                }
            }
           
            $descriptionL = $_POST['descriptionL'];
            
             print_r($_FILES);
             print($photo1);

             print($categorieBase);
             print($categorie);
             echo "<br>";

             try {


                // Vérifier si l'id_compte est défini (s'il est connecté)
                if (!$id_compte) {
                    die("Erreur : utilisateur non connecté.");
                }

                // Connexion à la base de données
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    
                $dbh->beginTransaction();
                $dbh->prepare("SET SCHEMA 'sae';")->execute();



                if(isset($_POST['photo'])){
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
                }
                




                if($categorieBase === $categorie){ //SI LA CATEGORIE N'A PAS CHANGE
                    if ((isset($_POST['cp']))&&(isset($_POST['adresse']))) {
                        if(empty($adresse['complement_adresse'])){$comp_adresse = null;}else{$comp_adresse = $adresse['complement_adresse'];}
                        // Requete SQL pour modifier la table adresse
                        $query = "UPDATE sae._adresse 
                                    set (num_et_nom_de_voie, complement_adresse, code_postal, ville, pays) = (?, ?, ?, ?, ?) 
                                        where id_adresse = (select id_adresse from sae._compte where id_compte = ?) returning id_adresse;";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute([$adresse, $comp_adresse, $cp, $ville, $pays, $id_compte]);
                        $id_adresse = $stmt->fetch()['id_adresse'];
                        
                    }
                    if ($date_evenement != null) {
                        // Insertion de la date dans la table _date
                        $reqInsertionDateEvent = 'INSERT INTO sae._date (date) VALUES (?) RETURNING id_date';
                        $stmtInsertionDateEvent = $dbh->prepare($reqInsertionDateEvent);
                        $stmtInsertionDateEvent->execute([$date_event]);
                        $id_date_event = $stmtInsertionDateEvent->fetch(PDO::FETCH_ASSOC)['id_date'];

                    }
                    echo $date_evenement;
                    echo "<br>";
                    echo $id_date_event;


                    switch ($categorie) {
                        case 'activite':
                           
                            $query = "UPDATE sae.offre_activite
                                    SET titre = ?, 
                                        resume = ?, 
                                        ville = ?, 
                                        duree = ?, 
                                        age_min = ?,  
                                        abonnement = ?, 
                                        description_detaille = ?, 
                                        site_web = ?, 
                                        id_adresse = ?,
                                    WHERE id_offre = ?;";
                            $stmt = $dbh->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $duree, $age, $type, $descriptionL, $lien, $id_adresse, $id_offre]);
                                        
                            break;

                        case 'parcattraction' :
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
                            SET titre = ?,
                                resume = ?, 
                                ville =?,
                                age_min = ?,
                                nb_attractions = ?, 
                                plan = ?, 
                                id_compte_professionnel = ?, 
                                abonnement = ?, 
                                description_detaille = ?, 
                                site_web = ?, 
                                id_adresse = ?
                            where id_offre = ?;";
                            $stmt = $dbh->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $age, $nbattraction,$fichier_plan, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_offre]);
                            
                            //INSERTION IMAGE DANS _OFFRE_CONTIENT_IMAGE
                            $requete_plan_offre = 'INSERT INTO _offre_contient_image(id_offre, id_image) VALUES (?, ?)';
                            $stmt_plan_offre = $dbh->prepare($requete_plan_offre);
                            $stmt_plan_offre->execute([$id_offre, $fichier_plan]);
                            
                            break;

                        case 'spectacle':
                            // Requete SQL pour modifier la vue offre
                            $query = "UPDATE sae.offre_spectacle
                                SET titre = ?, 
                                    resume = ?, 
                                    ville = ?, 
                                    duree = ?,
                                    capacite = ?,
                                    id_compte_professionnel = ?, 
                                    abonnement = ?, 
                                    description_detaille = ?, 
                                    site_web = ?, 
                                    id_adresse = ?
                                    date_evenement = ?
                                WHERE id_offre = ?;";
                        $stmt = $dbh->prepare($query);
                        $stmt->execute([$titre, $resume, $ville, $duree, $capacite, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_date_event, $id_offre]);

                            break;
                        
                        case 'visite' :
                            $query = "UPDATE sae.offre_visite
                            SET titre = ?, 
                            resume = ?, 
                            ville = ?, 
                            duree = ?, 
                            id_compte_professionnel = ?, 
                            abonnement = ?, 
                            description_detaille = ?, 
                            site_web = ?, 
                            id_adresse = ?
                            date_evenement = ? 
                            where id_offre = ?;";
                            $stmt = $dbh->prepare($query);
                            $stmt->execute([$titre, $resume, $ville, $duree, $id_compte, $type, $descriptionL, $lien, $id_adresse, $id_date_event, $id_offre]);
                            break;
                        
                        case 'restauration':

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
                                $fichier_carte = $restauration(['carte']);
                            }
                            
                            // Requete SQL pour modifier la vue offre
                            $query = "UPDATE sae.offre_restauration
                            set (titre = ?, 
                            resume = ?, 
                            ville = ?, 
                            gamme_prix = ?, 
                            carte = ?, 
                            id_compte_professionnel = ?, 
                            abonnement = ?, 
                            description_detaille = ?, 
                            site_web = ?, 
                            id_adresse = ?
                            where id_offre = ?;";
                            $stmt = $dbh->prepare($query);
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

                    //SWITCH CREATION REQUETE OFFRE //AJOUTER TABLE TARIF          SI LA CATEGORIE CHANGE ON SUPPRIME L4OFFRE POUR LA RECRÉÉE
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
                            $stmt->execute([$titre, $resume, $ville, $duree, $age,  $id_compte, $tarif1, $type, $resume, $descriptionL, $lien]);

                            $id_offre = $stmt->fetch(PDO::FETCH_ASSOC)['id_offre'];


                            break;

                        case 'parcattraction':
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
                            $requete = "INSERT INTO sae.offre_spectacle (titre, resume, ville, duree, nbattraction, id_compte_professionnel, abonnement, description_detaille, site_web) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) returning id_offre";
                            $stmt = $dbh->prepare($requete);
                            $stmt->execute([$titre, $resume, $ville, intval($duree), intval($nbattraction), $id_compte, $type, $descriptionL, $lien]);

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

                        case 'restauration':
                            
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
                                $requete_supprimer = "DELETE FROM sae.offre_restauration WHERE id_offre = ?";
                            
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
                            die("Erreur recréaction d'offre!");
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
                    if (($isIdProPrivee)&&($categorie !== "restauration")){
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
                            window.location.href = '/back/consulter-offre/id'
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
            const liste_tags_restauration = "<?php echo json_encode($liste_tags_restauration) ?>";
            const $tags = "<?php echo json_encode($tags) ?>"

            let typecategorie = document.getElementById('categorie');
            let typerestauration = ["carte", "labelcarte"];
            let typevisite = ["labelduree", "duree", "labelduree2"];
            let typeactivite = ["labelage", "age", "labelage2", "labelduree", "duree", "labelduree2"];
            let typespectacle = ["labelduree", "duree", "labelduree2", "labelnbattractions", "nbattraction", "labelnbattractions2"];
            let typeparc = ["labelnbattractions", "nbattraction", "labelplan", "plan"];
            let obligatoireselontype = ["carte", "labelcarte", "labelgammedeprix", "gammedeprix", "labelage", "age", "labelage2", "labelduree", "duree", "labelduree2", "labelnbattractions", "nbattraction", "labelplan", "plan", "labelnbattractions", "nbattraction", "labelnbattractions2"];

            obligatoireselontype.forEach(element => {
                document.getElementById(element).style.display = 'none';
            });

            document.getElementById("tarifs").style.display = 'none';


            categorie.addEventListener('change', function() {
                const typeselectionne = categorie.value;
                // Afficher les champs selon la catégorie sélectionnée test
                switch (typeselectionne) {
                    case "restauration":
                        afficheSelonType(typerestauration);

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

                    case "parcattraction":
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
                if ((typechoisi !== "restauration") && (isIdProPrivee)) {
                    document.getElementById("tarifs").style.display = 'inline';
                }
            }

            function afficherTags(typechoisi){
                if (typeselectionne === "restauration"){
                    liste_tags.forEach(tag => {
                        if(!tags.includes(tag)){
                            document.getElementById(tag).style.display ='none';
                        }
                    });
                }else{
                    liste_tags_restauration.forEach(tag => {
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
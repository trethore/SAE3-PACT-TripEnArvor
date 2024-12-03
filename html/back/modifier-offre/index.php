<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/offres-utils.php');

session_start();
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
    $jours = getJoursOuverture($id_offre_cible);
    
    // ===== Requête SQL pour récupérer les horaires d'ouverture d'une offre ===== //
    $horaire = getHorairesOuverture($id_offre_cible);

    // ===== GESTION DES CATEGORIES ===== //

    // ===== Requête SQL pour récupérer le type d'une offre ===== //
    $categorie = getTypeOffre($id_offre_cible);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    die();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../style/styleguide.css" />
    <link rel="stylesheet" href="/style/style_HFB.css" />
    <link rel="stylesheet" href="../../style/style_gereeOffre.css" />
    <title>Document</title>
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
                
                <h2> Modifier <?php echo htmlentities($titre) ?></h2>

                <form action="index.php" method="post" enctype="multipart/form-data" id="dynamicForm">

                    <h3>Informations importantes</h3>

                    <div class="important">
                        <table border="0">
                            <tr>
                                <td><label for="titre">Titre <span class="required">*</span></label> </td>
                                <td colspan="3"><input type="text" id="titre" name="titre" placeholder="Insérer un titre"  value="<?php echo htmlentities($titre) ?> " required/></td>
                            </tr>
                            <tr>
                                <td><label for="categorie">Catégorie</label></td>
                                <td><?php echo htmlentities($categorie) ?> </td>
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
                                <!-- <label for="file-upload">
                                    <img src="/images/backOffice/icones/plus.png" alt="Uploader une image" class="upload-image" width="50px" height="50px">
                                </label> -->
                                <input id="photo" type="file" name="photo" value="" required />
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
                    <label id="labelcapacite" for="capacite">Capacité de la salle <span class="required">*</span> </label> <input type="number" id="capacite" name="capacite" value="<?php echo htmlentities($activite['capacite']) ?>"/><label id="labelcapacite2" for="capacite">personnes</label>
                    <br>
                    <!-- parc -->
                    <label id="labelnbattractions" for="nbattraction">Nombre d'attractions <span class="required">*</span> </label> <input type="number" id="nbattraction" name="attractions" value="<?php echo htmlentities($activite['age_min']) ?>"/>
                    <label id="labelplan" for="plan">Importer le plan du parc <span class="required">*</span> </label> <input type="file" id="plan" name="plan" />
                    <br>
                    <!-- restaurant -->
                    <label id="labelcarte" for="carte">Importer la carte du restaurant <span class="required">*</span> <input type="file" id="carte" name="carte" />
                </div>
                    <br>
                    </div>

                    <h3>Tags de l'offre</h3>

                    <p> -- Choisir une catégorie -- </p>
                    <h3>A propos de l'offre</h3>
                    <div class="apropos">
                        <table border="0">
                            <tr>
                                <td><label for="descriptionC">Courte Description <span class="required">*</span></label></td>
                                <td><textarea id="descriptionC" name="descriptionC" placeholder="Ecrire une courte description sur l’offre..." required><?php echo htmlentities($offre['resume']) ?></textarea></td>

                            </tr>
                            <tr>
                                <td><label for="lien">Lien externe</label></td>
                                <td><input type="url" id="lien" name="lien" placeholder="Insérer un lien vers un site internet" value="<?php echo htmlentities($offre['site_web']); ?>"/></td>
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
                        <?php  $i=0; foreach ($tarifs as $t) { $i++; ?>
                        <input type="text" id="nomtarif1" name="nomtarif1" placeholder="Nom du tarif" value="<?php echo htmlentities($t['nom_tarif']) ?>"/>
                        <input type="number" name="tarif1" min="0" placeholder="prix" value="<?php echo htmlentities($t['prix']) ?>"/><span>€</span> 
                        <br><?php 
                        }
                        while($i!==4){ ?>
                            <input type="text" id="nomtarif1" name="nomtarif1" placeholder="Nom du tarif"/>
                            <input type="number" name="tarif1" min="0" placeholder="prix" /><span>€</span> 
                            <br>
                        <?php $i++; 
                        } ?>
                       
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
                        <input class="valider" type="submit" value="Modifier l'offre" />

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
             
             try {
    
                // Connexion à la base de données
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    
                
                $dbh->prepare("SET SCHEMA 'sae';")->execute();
            }catch(PDOException $e){

            }

        }?>
    </body>
</html>
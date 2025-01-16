<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

// Vérification de l'existence de numero_facture
if (isset($_GET['numero_facture'])) {
    $num_facture = $_GET['numero_facture'];
} else {
    die();
}
try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

startSession();
$id_compte = $_SESSION["id"]; 
if (isset($id_compte)) {
    redirectToConnexionIfNecessaryPro($id_compte);
} else {
    redirectTo('https://redden.ventsdouest.dev/front/consulter-offres/');
}

$TVA = 20; // TVA en %
$TotalHT = 0; // Somme final hors taxe
$TotalTVA = 0; // Somme finale TVA
$num_facture = $_GET["numero_facture"];
$today = new DateTime(); // Obtenir la date d'aujourd'hui

// Obtenir la date du dernier jour du mois et la convertir en chaîne de caractères
$emissionDate = new DateTime();
$emissionDate->modify('last day of this month');
$emissionDate->setTime(23, 59, 59);
$emissionDateFormatted = $emissionDate->format('Y-m-d H:i:s');

// Calculer la date d'échéance et la convertir en chaîne de caractères
$echeanceDate = clone $emissionDate;
$echeanceDate->modify('+15 days');
$echeanceDateFormatted = $echeanceDate->format('Y-m-d H:i:s');

$reqInsertDate = "INSERT INTO sae._date (date) VALUES (:date) returning id_date";

$reqCompte = "SELECT * from sae.compte_professionnel_prive cp
                join sae._adresse a on a.id_adresse = cp.id_adresse
                where id_compte =  :id_compte;";

$reqFacture = "SELECT numero_facture, d.date as date_emission, da.date as date_echeance, d.id_date as id_date_emission, da.id_date as id_date_echeance from sae._facture 
                join sae._date d on d.id_date = id_date_emission
                join sae._date da on da.id_date = id_date_echeance
                where numero_facture = :nu_facture;";

$reqUpdateDate = "UPDATE sae._date set date = :date_emission_maj where id_date = :id_date_emission;";

$reqFactureAbonnement = "SELECT o.titre, o.abonnement, prix_ht_jour_abonnement, d.date as date_mise_en_ligne from sae._offre o
                        join sae._abonnement a on o.abonnement = a.nom_abonnement
                        join sae._facture f on o.id_offre = f.id_offre
                        join sae._historique_prix_abonnements ha on a.nom_abonnement = ha.nom_abonnement
                        join sae._offre_dates_mise_en_ligne oml on o.id_offre = oml.id_offre
                        join sae._date d on oml.id_date = d.id_date
                        where f.numero_facture = :nu_facture;";

$reqOption = "SELECT os.nom_option, d.date, ho.prix_ht_hebdo_abonnement as prix from sae._offre_souscrit_option os
                join sae._date d on d.id_date = os.id_date_souscription
                join sae._historique_prix_options ho on ho.nom_option = os.nom_option
                where id_offre = :id_offre;"
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="/style/style.css">
</head>
<body class="genFacture">
    <?php 
        // Préparation et exécution de la requête
        $stmt = $conn->prepare($reqCompte);
        $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
        $stmt->execute();
        $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);

        // Préparation et exécution de la requête du premier select afin d'avoir id_date_emission pour pouvoir l'update juste après
        $stmt = $conn->prepare($reqFacture);
        $stmt->bindParam(':nu_facture', $num_facture, PDO::PARAM_INT); // Lié à l'ID de la facture
        $stmt->execute();
        $detailFacture = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update de la date d'émission
        $stmt = $conn->prepare($reqUpdateDate);
        $stmt->bindParam(':date_emission_maj', $emissionDateFormatted, PDO::PARAM_STR);
        $stmt->bindParam(':id_date_emission', $detailFacture["id_date_emission"], PDO::PARAM_INT);
        $stmt->execute();

        // Update de la date d'émission
        $stmt = $conn->prepare($reqUpdateDate);
        $stmt->bindParam(':date_emission_maj', $echeanceDateFormatted, PDO::PARAM_STR);
        $stmt->bindParam(':id_date_emission', $detailFacture["id_date_echeance"], PDO::PARAM_INT);
        $stmt->execute();

        // Préparation et exécution de la requête
        $stmt = $conn->prepare($reqFacture);
        $stmt->bindParam(':nu_facture', $num_facture, PDO::PARAM_INT); // Lié à l'ID de la facture
        $stmt->execute();
        $detailFacture = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="infoFacture">
        <img src="/images/universel/logo/Logo_couleurs.png" alt="logo de PACT">
        <article>
            <h3>Numéro de facture #<?php echo htmlentities($detailFacture["numero_facture"]); ?></h3>
            <p><?php 
            $date_emission_DMY = new DateTime($detailFacture["date_emission"]);
            $date_emission_DMY = $date_emission_DMY->format('d-m-Y');
            echo htmlentities($date_emission_DMY); 
            ?></p>
        </article>
    </div>
    <section>
        <article class="delivre">
            <h3>Délivré à</h3>
            <p>
                <!-- Dénomination Sociale -->
                <?php echo htmlentities($detailCompte["denomination"] ?? '');?>
                <br>
                <!-- Adresse -->
                <?php echo htmlentities($detailCompte["num_et_nom_de_voie"] ?? '');?>
                <br>
                <!-- CP -->
                <?php echo htmlentities($detailCompte["code_postale"] ?? '');?>
                <!-- Ville -->
                <?php echo htmlentities($detailCompte["ville"] ?? '');?>
                <br>
                <!-- Tel -->
                Tél : <?php echo htmlentities($detailCompte["tel"] ?? '');?>
                <br>
                <!-- Email -->
                Email : <?php echo htmlentities($detailCompte["email"] ?? '');?>
            </p>
        </article>
        <article class="emetteur">
            <h3>Emetteur</h3>
            <p>
                Trip en Arvor <br>
                Rue Édouard Branly <br>
                22300 Lannion <br>
                Tél : 02 96 46 93 00 <br>
                Email : tripenarvor@gmail.com
            </p>
        </article>
    </section>
    <article class="facture-details">
        <table>
            <thead>
                <tr>
                    <th>Nom offre</th>
                    <th>Type offre</th>
                    <th>Nb Jour</th>
                    <th>% TVA</th>
                    <th>Prix HT Journalier</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <?php try {
                    // Préparation et exécution de la requête
                    $stmt = $conn->prepare($reqFactureAbonnement);
                    $stmt->bindParam(':nu_facture', $num_facture, PDO::PARAM_INT); // Lié à l'ID du compte
                    $stmt->execute();
                    $factAbos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Vérifiez si $factAbos est un tableau avant de le parcourir
                    if ($factAbos && is_array($factAbos)) {
                        foreach($factAbos as $factAbo) { ?>
                        <tr>
                            <!-- Titre de l'offre -->
                            <td><?php echo htmlentities($factAbo["titre"]);?></td>
                            <!-- Type de l'abonnement -->
                            <td><?php echo htmlentities($factAbo["abonnement"]);?></td>
                            <!-- Nb de jour -->
                            <td>
                            <?php 
                            $nb_jour = getNbJours($factAbo["date_mise_en_ligne"], $today);
                            echo htmlentities($nb_jour);
                            ?>
                            </td>
                            <!-- TVA en % -->
                            <td><?php echo htmlentities($TVA) ?>%</td>
                            <!-- Prix HT -->
                            <td><?php echo htmlentities(convertCentimesToEuros($factAbo["prix_ht_jour_abonnement"]));?></td>
                            <!-- Prix total TTC -->
                            <td><?php echo htmlentities(convertCentimesToEuros(getOffreTTC($factAbo["prix_ht_jour_abonnement"],$nb_jour, $TVA)));?></td>

                            <?php // Calcul pour le total final
                                $TotalHT += $factAbo["prix_ht_jour_abonnement"]*$nb_jour;
                                $TotalTVA += $factAbo["prix_ht_jour_abonnement"]*$nb_jour*$TVA/100;
                            ?>
                        </tr>
                    <?php }}
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                } ?>
            </tbody>
        </table>
    </article>
    <article class="facture-details">
        <table>
            <thead>
                <tr>
                    <th>Nom Option</th>
                    <th>Nb semaines</th>
                    <th>% TVA</th>
                    <th>Prix HT Hebdomadaire</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <?php try {
                    // Préparation et exécution de la requête
                    $stmt = $conn->prepare($reqOption);
                    $stmt->bindParam(':id_offre', $id_offre, PDO::PARAM_INT); // Lié à l'ID de l'offre
                    $stmt->execute();
                    $factOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    // Vérifiez si $factOptions est un tableau avant de le parcourir
                    echo $id_offre;
                    if ($factOptions && is_array($factOptions)) {
                        foreach($factOptions as $factOption) { ?>
                        <tr>
                            <!-- Titre de l'option -->
                            <td><?php echo htmlentities($factOption["nom_option"]);?></td>
                            <!-- Nb de semaine  -->
                            <td><?php 
                            $nb_semaine = getNbSemaine($factOption["date"], $today);
                            echo htmlentities($nb_semaine);
                            ?></td>
                            <!-- TVA en % -->
                            <td><?php echo htmlentities($TVA);?>%</td>
                            <!-- Prix HT hebdo -->
                            <td><?php echo htmlentities(convertCentimesToEuros($factOption["prix"]));?></td>
                            <!-- Prix TTC total de l'option -->
                            <td><?php echo htmlentities(convertCentimesToEuros(getOffreTTC($factOption["prix"],$nb_semaine, $TVA)))?></td>
                            <?php // Calcul pour le total final
                                $TotalHT += $factOption["prix"]*$nb_semaine;
                                $TotalTVA += $factOption["prix"]*$nb_semaine*$TVA/100;
                            ?>
                        </tr>
                    <?php }}
                } catch (PDOException $e) {
                    echo "Erreur : " . $e->getMessage();
                } ?>
            </tbody>
        </table>
    </article>
    <hr>
    <table class="totals">
        <tr>
            <td>Total HT</td>
            <td><?php echo htmlentities(convertCentimesToEuros($TotalHT)) ?? '' ?></td>
        </tr>
        <tr>
            <td>Total TVA</td>
            <td><?php echo htmlentities(convertCentimesToEuros($TotalTVA)) ?? '' ?></td>
        </tr>
        <tr>
            <td><b>Total TTC</b></td>
            <td><b><?php echo htmlentities(convertCentimesToEuros($TotalHT + $TotalTVA)) ?? '' ?></b></td>
        </tr>
    </table>
    <article class="payment-terms">
        <h3>Conditions et modalités de paiement</h3>
        <p>Le paiement est à régler jusqu'au <?php 
        $date_echeance_DMY = new DateTime($detailFacture["date_echeance"]);
        $date_echeance_DMY = $date_echeance_DMY->format('d-m-Y');
        echo htmlentities($date_echeance_DMY);
        ?></p>
        <p>
            Banque PACT<br>
            Nom du compte: Trip en Arvor<br>
            Numéro de compte : 123-456-7890
        </p>
    </article>
</body>
</html>
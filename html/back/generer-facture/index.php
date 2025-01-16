<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . AUTH_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . SESSION_UTILS);

try {
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

$TVA = 20; // TVA en %
$TotalHT = 0; // Somme final hors taxe
$TotalTVA = 0; // Somme finale TVA

// Obtenir la date d'aujourd'hui
$today = new DateTime();
// La date du dernier jour du mois
$emissionDate = date("Y-m-d H:i:s");

// Conversion de la chaîne en objet DateTime pour faciliter les calculs
$emissionDateDate = new DateTime($emissionDate);
$echeanceDate = $emissionDateDate->modify('+15 days');
$echeanceDate = $emissionDateDate->format('Y-m-d H:i:s');

startSession();
$id_compte = $_SESSION["id"]; 
if (isset($id_compte)) {
    redirectToConnexionIfNecessaryPro($id_compte);
} else {
    redirectTo('https://redden.ventsdouest.dev/front/consulter-offres/');
}

$reqInsertDate = "INSERT INTO sae._date (date) VALUES (:date)";

$reqInsertFact = "INSERT INTO sae._facture (montant_ht, id_date_emission, id_date_echeance, id_offre) 
                   VALUES (:montant_ht, :id_date_emission, :id_date_echeance, :id_offre)";

$reqCompte = "SELECT * from sae.compte_professionnel_prive cp
                join sae._adresse a on a.id_adresse = cp.id_adresse
                where id_compte =  :id_compte;";

$reqFacture = "SELECT numero_facture, d.date as date_emission, da.date as date_echeance from sae._facture 
                join sae._date d on d.id_date = id_date_emission
                join sae._date da on da.id_date = id_date_echeance
                where numero_facture = :nu_facture;";

$reqFactureAbonnement = "SELECT o.titre, o.abonnement, prix_ht_jour_abonnement, d.date from sae._offre o
                        join sae._abonnement a on o.abonnement = a.nom_abonnement
                        join sae._facture f on o.id_offre = f.id_offre
                        join sae._historique_prix_abonnements ha on a.nom_abonnement = ha.nom_abonnement
                        join sae._offre_dates_mise_en_ligne oml on o.id_offre = oml.id_offre
                        join sae._date d on oml.id_date = d.id_date
                        where o.id_compte_professionnel = :id_compte;";
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
    if (!factureExiste($conn, $_GET["numero_facture"]) && $today > $echeanceDate) {
        // Check si les dates existes pour pas faire de doublons
        if (!dateExiste($conn, $emissionDate)) {
            // Insert de la date d'emission de la facture dans la table _date
            $stmt = $conn->prepare($reqInsertDate);
            $stmt->bindParam(':date', $emissionDate, PDO::PARAM_STR);
            $stmt->execute();
        }
        if (!dateExiste($conn, $echeanceDate)) {
            // Insert de la date d'échéance de la facture dans la table _date
            $stmt = $conn->prepare($reqInsertDate);
            $stmt->bindParam(':date', $echeanceDate, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Insert d'une facture
        $stmt = $conn->prepare($reqInsertFact);
        $stmt->bindParam(':montant_ht', $montant_ht, PDO::PARAM_INT);
        $stmt->bindParam(':id_date_emission', $emissionDate, PDO::PARAM_INT);
        $stmt->bindParam(':id_date_echeance', $echeanceDate, PDO::PARAM_INT);
        $stmt->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
        $stmt->execute();
    }
        // Préparation et exécution de la requête
        $stmt = $conn->prepare($reqCompte);
        $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
        $stmt->execute();
        $detailCompte = $stmt->fetch(PDO::FETCH_ASSOC);
        // Préparation et exécution de la requête
        $stmt = $conn->prepare($reqFacture);
        $stmt->bindParam(':nu_facture', $_GET["numero_facture"], PDO::PARAM_INT); // Lié à l'ID de la facture
        $stmt->execute();
        $detailFacture = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="infoFacture">
        <img src="/images/universel/logo/Logo_couleurs.png" alt="logo de PACT">
        <article>
            <h3>Numéro de facture</h3>
            <h3>#<?php echo htmlentities($detailFacture["numero_facture"]); ?></h3>
            <p><?php echo htmlentities($detailFacture["date_emission"]); ?></p>
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
                <?php // Préparation et exécution de la requête
                $stmt = $conn->prepare($reqFactureAbonnement);
                $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
                $stmt->execute();
                $factAbos = $stmt->fetch(PDO::FETCH_ASSOC);
                // Vérifiez si $factAbos est un tableau avant de le parcourir
                if ($factAbos && is_array($factAbos)) {
                    foreach($factAbos as $factAbo) { ?>
                    <tr>
                        <!-- Titre de l'offre -->
                        <td><?php echo htmlentities($factAbo["titre"] ?? '');?></td>
                        <!-- Type de l'abonnement -->
                        <td><?php echo htmlentities($factAbo["abonnement"] ?? '');?></td>
                        <!-- Nb de semaine -->
                        <td>
                        <?php echo htmlentities(getNbSemaine($factAbo["date"], $today));?>
                        </td>
                        <!-- TVA en % -->
                        <td><?php echo htmlentities($TVA) ?>%</td>
                        <!-- Prix HT -->
                        <td><?php echo htmlentities($factAbo["prix_ht_jour_abonnement"] ?? '');?></td>
                        <!-- Prix total TTC -->
                        <td><?php echo htmlentities(getOffreTTC($factAbo["prix_ht_jour_abonnement"],$factAbo["nbSemaine"], $TVA));?></td>

                        <?php // Calcul pour le total final
                            $TotalHT += $factAbo["prix_ht_jour_abonnement"];
                            $TotalTVA += (getOffreTTC($factAbo["prix_ht_jour_abonnement"],$factAbo["nbSemaine"], $TVA) - $factAbo["prix_ht_jour_abonnement"]);
                        ?>
                    </tr>
                <?php }} ?>
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
                <tr>
                    <td>A la Une</td>
                    <td>1</td>
                    <td>20%</td>
                    <td>12.00€</td>
                    <td>18.00€</td>
                </tr>
            </tbody>
        </table>
    </article>
    <hr>
    <table class="totals">
        <tr>
            <td>Total HT</td>
            <td><?php echo htmlentities($TotalHT) ?? '' ?></td>
        </tr>
        <tr>
            <td>Total TVA</td>
            <td><?php echo htmlentities($TotalTVA) ?? '' ?></td>
        </tr>
        <tr>
            <td><b>Total TTC</b></td>
            <td><b><?php echo htmlentities($TotalHT + $TotalTVA) ?? '' ?></b></td>
        </tr>
    </table>
    <article class="payment-terms">
        <h3>Conditions et modalités de paiement</h3>
        <p>Le paiement est à régler jusqu'au <?php echo htmlentities($detailFacture["date_echeance"]) ?></p>
        <p>
            Banque PACT<br>
            Nom du compte: Trip en Arvor<br>
            Numéro de compte : 123-456-7890
        </p>
    </article>
</body>
</html>
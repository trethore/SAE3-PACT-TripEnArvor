<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

date_default_timezone_set('Europe/Paris');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_offre = $_POST['id_offre'] ?? null;
    $id_membre = $_POST['id_membre'] ?? null;
    $dateBlacklist = date('Y-m-d H:i:s');

    if (isset($id_offre) && isset($id_membre)) {

        if (getOffre($id_offre)['nb_jetons'] > 0) {

            $nb_jetons = getOffre($id_offre)['nb_jetons'] - 1;

            try {
                //Connexion à la base de données
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                //Insertion de la date de blacklistage
                $reqInsertionDateBlacklist = "INSERT INTO sae._blacklister(id_offre, id_membre, blackliste_le) VALUES (?, ?, ?)";
                $stmtInsertionDateBlacklist = $dbh->prepare($reqInsertionDateBlacklist);
                $stmtInsertionDateBlacklist->execute([$id_offre, $id_membre, $dateBlacklist]);

                //Update du nombre de jetons de blacklistage
                $reqUpdateJetons = "UPDATE sae._offre SET nb_jetons = :nb_jetons WHERE id_offre = :id_offre";
                $stmtUpdateJetons = $dbh->prepare($reqUpdateJetons);
                $stmtUpdateJetons->execute([':nb_jetons' => $nb_jetons, ':id_offre' => $id_offre]);

                if (getOffre($id_offre)['jeton_perdu_le'] == null) {
                    //Update de la date de perte du jeton de blacklistage
                    $reqUpdateDatePerteJeton = "UPDATE sae._offre SET jeton_perdu_le = :jeton_perdu_le WHERE id_offre = :id_offre";
                    $stmtUpdateDatePerteJeton = $dbh->prepare($reqUpdateDatePerteJeton);
                    $stmtUpdateDatePerteJeton->execute([':jeton_perdu_le' => $dateBlacklist, ':id_offre' => $id_offre]);
                }

            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
        }
    }
}
?>
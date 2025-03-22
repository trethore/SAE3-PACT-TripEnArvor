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

    if (isset($id_offre)) {

        if (getOffre($id_offre)['nb_jetons'] < 3) {

            $nb_jetons = getOffre($id_offre)['nb_jetons'] + 1;

            try {
                //Connexion à la base de données
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $reqUpdateJetonDate = "UPDATE sae._offre SET nb_jetons = :nb_jetons, jeton_perdu_le = NOW() WHERE jeton_perdu_le <= NOW() - INTERVAL 30 SECOND AND id_offre = :id_offre";
                $stmtUpdateJetonDate = $dbh->prepare($reqUpdateJetonDate);
                $stmtUpdateJetonDate->execute([':nb_jetons' => $nb_jetons, ':id_offre' => $id_offre]);

            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
        }
    }
}
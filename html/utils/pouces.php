<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
require_once($_SERVER['DOCUMENT_ROOT'] . OFFRES_UTILS);
require_once($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/session-utils.php');

startSession();

date_default_timezone_set('Europe/Paris');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_membre = $_POST['id_membre'] ?? null;
    $id_offre = $_POST['id_offre'] ?? null;
    $type = $_POST['type'] ?? null; 
    $action = $_POST['action'] ?? null; 
    $change = $_POST['change'] ?? false; 

    if (isset($id_membre) && isset($id_offre) && isset($type) && isset($action)) {

        $pouce = ($type === 'like') ? 'nb_pouce_haut' : 'nb_pouce_bas';
        $pouce_oppose = ($type === 'like') ? 'nb_pouce_bas' : 'nb_pouce_haut';

        try {
            //Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($action === "add") {
                $reqUpdatePouce = "UPDATE sae._avis SET $pouce = $pouce + 1 WHERE id_membre = :id_membre AND id_offre = :id_offre";
                $stmtUpdatePouce = $dbh->prepare($reqUpdatePouce);
                $stmtUpdatePouce->execute(['id_membre' => $id_membre, 'id_offre' => $id_offre]);
                if ($change === "true") {
                        $reqUpdatePouceOppose = "UPDATE sae._avis SET $pouce_oppose = GREATEST($pouce_oppose - 1, 0) WHERE id_membre = :id_membre AND id_offre = :id_offre";
                        $stmtUpdatePouceOppose = $dbh->prepare($reqUpdatePouceOppose);
                        $stmtUpdatePouceOppose->execute(['id_membre' => $id_membre, 'id_offre' => $id_offre]);
                }
            } else {
                $reqUpdatePouce = "UPDATE sae._avis SET $pouce = GREATEST($pouce - 1, 0) WHERE id_membre = :id_membre AND id_offre = :id_offre";
                $stmtUpdatePouce = $dbh->prepare($reqUpdatePouce);
                $stmtUpdatePouce->execute(['id_membre' => $id_membre, 'id_offre' => $id_offre]);
            }

        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>
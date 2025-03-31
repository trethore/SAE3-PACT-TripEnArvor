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
    $id_membre_avis = $_POST['id_membre_avis'] ?? null;
    $id_membre_reaction = $_POST['id_membre_reaction'] ?? null;
    $type = $_POST['type'] ?? null; 
    $action = $_POST['action'] ?? null;

    if (!isset($id_offre, $id_membre_avis, $id_membre_reaction, $type, $action)) {
        echo "Paramètres manquants.";
        exit;
    }

    try {
        // Connexion à la base de données
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();

        // Vérification de la présence d'une réaction existante
        $reqCheck = "SELECT nb_pouce_haut, nb_pouce_bas FROM sae._reaction_avis 
                     WHERE id_offre = :id_offre AND id_membre_avis = :id_membre_avis 
                     AND id_membre_reaction = :id_membre_reaction";
        $stmtCheck = $dbh->prepare($reqCheck);
        $stmtCheck->execute([
            ':id_offre' => $id_offre,
            ':id_membre_avis' => $id_membre_avis,
            ':id_membre_reaction' => $id_membre_reaction
        ]);
        $check = $stmtCheck->fetch();

        $likeChange = 0;
        $dislikeChange = 0;
        $updatePouceHaut = $check['nb_pouce_haut'] ?? 0;
        $updatePouceBas = $check['nb_pouce_bas'] ?? 0;

        if ($type === 'like') {
            if ($action === 'add' && $updatePouceHaut == 0) {
                $likeChange = 1;
                if ($updatePouceBas == 1) {
                    $dislikeChange = -1;  
                }
                $updatePouceHaut = 1;
                $updatePouceBas = 0;
            } elseif ($action === 'remove' && $updatePouceHaut == 1) {
                $likeChange = -1;
                $updatePouceHaut = 0;
            }
        } elseif ($type === 'dislike') {
            if ($action === 'add' && $updatePouceBas == 0) {
                $dislikeChange = 1;
                if ($updatePouceHaut == 1) {
                    $likeChange = -1;  
                }
                $updatePouceHaut = 0;
                $updatePouceBas = 1;
            } elseif ($action === 'remove' && $updatePouceBas == 1) {
                $dislikeChange = -1;
                $updatePouceBas = 0;
            }
        }

        // Mise à jour ou insertion de la réaction
        if (!$check) {
            $reqInsert = "INSERT INTO sae._reaction_avis (id_offre, id_membre_avis, id_membre_reaction, nb_pouce_haut, nb_pouce_bas) 
                          VALUES (:id_offre, :id_membre_avis, :id_membre_reaction, :nb_pouce_haut, :nb_pouce_bas)";
            $stmtInsert = $dbh->prepare($reqInsert);
            $stmtInsert->execute([
                ':id_offre' => $id_offre,
                ':id_membre_avis' => $id_membre_avis,
                ':id_membre_reaction' => $id_membre_reaction,
                ':nb_pouce_haut' => $updatePouceHaut,
                ':nb_pouce_bas' => $updatePouceBas
            ]);
        } else {
            $reqUpdate = "UPDATE sae._reaction_avis 
                          SET nb_pouce_haut = :nb_pouce_haut, nb_pouce_bas = :nb_pouce_bas 
                          WHERE id_offre = :id_offre AND id_membre_avis = :id_membre_avis 
                          AND id_membre_reaction = :id_membre_reaction";
            $stmtUpdate = $dbh->prepare($reqUpdate);
            $stmtUpdate->execute([
                ':nb_pouce_haut' => $updatePouceHaut,
                ':nb_pouce_bas' => $updatePouceBas,
                ':id_offre' => $id_offre,
                ':id_membre_avis' => $id_membre_avis,
                ':id_membre_reaction' => $id_membre_reaction
            ]);
        }

        // Mise à jour du total des pouces dans _avis si un changement a eu lieu
        if ($likeChange !== 0 || $dislikeChange !== 0) {
            $reqUpdateAvis = "UPDATE sae._avis 
                              SET nb_pouce_haut = nb_pouce_haut + :likeChange, 
                                  nb_pouce_bas = nb_pouce_bas + :dislikeChange 
                              WHERE id_offre = :id_offre AND id_membre = :id_membre_avis";
            $stmtUpdateAvis = $dbh->prepare($reqUpdateAvis);
            $stmtUpdateAvis->execute([
                ':likeChange' => $likeChange,
                ':dislikeChange' => $dislikeChange,
                ':id_offre' => $id_offre,
                ':id_membre_avis' => $id_membre_avis
            ]);
        }

        echo json_encode([
            'success' => true,
            'nb_pouce_haut' => $updatePouceHaut,
            'nb_pouce_bas' => $updatePouceBas
        ]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>

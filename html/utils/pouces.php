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

    if (!isset($id_offre, $id_membre_avis, $id_membre_reaction, $type)) {
        echo json_encode(['success' => false, 'error' => 'Paramètres manquants.']);
        exit;
    }

    try {
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->prepare("SET SCHEMA 'sae';")->execute();

        // Check existing reaction
        $stmtCheck = $dbh->prepare("
            SELECT nb_pouce_haut, nb_pouce_bas 
            FROM sae._reaction_avis 
            WHERE id_offre = :id_offre 
            AND id_membre_avis = :id_membre_avis 
            AND id_membre_reaction = :id_membre_reaction
        ");
        $stmtCheck->execute([
            ':id_offre' => $id_offre,
            ':id_membre_avis' => $id_membre_avis,
            ':id_membre_reaction' => $id_membre_reaction
        ]);
        $existingReaction = $stmtCheck->fetch();

        $isLike = ($type === 'like');
        $currentLike = $existingReaction['nb_pouce_haut'] ?? 0;
        $currentDislike = $existingReaction['nb_pouce_bas'] ?? 0;
        
        // Determine new values
        $newLike = $isLike ? ($currentLike ? 0 : 1) : 0;
        $newDislike = !$isLike ? ($currentDislike ? 0 : 1) : 0;
        
        // Calculate changes for avis table
        $likeChange = $newLike - $currentLike;
        $dislikeChange = $newDislike - $currentDislike;

        // Update or insert reaction
        if ($existingReaction) {
            $stmtUpdate = $dbh->prepare("
                UPDATE sae._reaction_avis 
                SET nb_pouce_haut = :like, nb_pouce_bas = :dislike
                WHERE id_offre = :id_offre 
                AND id_membre_avis = :id_membre_avis 
                AND id_membre_reaction = :id_membre_reaction
            ");
            $stmtUpdate->execute([
                ':like' => $newLike,
                ':dislike' => $newDislike,
                ':id_offre' => $id_offre,
                ':id_membre_avis' => $id_membre_avis,
                ':id_membre_reaction' => $id_membre_reaction
            ]);
        } else {
            $stmtInsert = $dbh->prepare("
                INSERT INTO sae._reaction_avis 
                (id_offre, id_membre_avis, id_membre_reaction, nb_pouce_haut, nb_pouce_bas) 
                VALUES (:id_offre, :id_membre_avis, :id_membre_reaction, :like, :dislike)
            ");
            $stmtInsert->execute([
                ':id_offre' => $id_offre,
                ':id_membre_avis' => $id_membre_avis,
                ':id_membre_reaction' => $id_membre_reaction,
                ':like' => $newLike,
                ':dislike' => $newDislike
            ]);
        }

        // Update avis counts
        if ($likeChange !== 0 || $dislikeChange !== 0) {
            $stmtUpdateAvis = $dbh->prepare("
                UPDATE sae._avis 
                SET nb_pouce_haut = nb_pouce_haut + :likeChange, 
                    nb_pouce_bas = nb_pouce_bas + :dislikeChange 
                WHERE id_offre = :id_offre AND id_membre = :id_membre_avis
            ");
            $stmtUpdateAvis->execute([
                ':likeChange' => $likeChange,
                ':dislikeChange' => $dislikeChange,
                ':id_offre' => $id_offre,
                ':id_membre_avis' => $id_membre_avis
            ]);
        }

        echo json_encode(['success' => true]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
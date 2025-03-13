<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');
require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);

$id = $_POST["id_compte"];

try {
    global $driver, $server, $dbname, $user, $pass;
    $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $conn->prepare("SET SCHEMA 'sae';")->execute();

    // Vérifier si l'utilisateur existe avant suppression
    $stmt = $conn->prepare("SELECT id_compte FROM sae._compte WHERE id_compte = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    if (!$stmt->fetch()) {
        echo "Erreur : compte inexistant.";
        exit;
    }
    
    $conn->beginTransaction();

    // Suppression du compte membre
    $stmt = $conn->prepare("DELETE FROM sae._compte_membre WHERE id_compte = :id;");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    // Suppression du compte
    $stmt = $conn->prepare("DELETE FROM sae._compte WHERE id_compte = :id;");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    $conn->commit();

    echo "Compte supprimé avec succès.";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

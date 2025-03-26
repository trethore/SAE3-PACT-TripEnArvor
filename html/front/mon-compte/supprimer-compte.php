<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_compte"])) {
    $id = (int)$_POST["id_compte"];
    
    try {
        global $driver, $server, $dbname, $user, $pass;
        $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $conn->prepare("SET SCHEMA 'sae';")->execute();

        // Suppression du compte membre
        $query = "DELETE FROM sae._compte_membre WHERE id_compte = :id;";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        // Suppression du compte
        $query = "DELETE FROM sae._compte WHERE id_compte = :id;";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "Compte supprimé avec succès.";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Requête invalide.";
}
?>

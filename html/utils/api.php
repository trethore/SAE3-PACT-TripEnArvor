<?php
header("Content-Type: application/json");

require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/compte-utils.php');

try {
    $result = array();

    if (isset($_GET['pseudo-exist'])) {
        $result['pseudoExist'] = pseudo_exist($_GET['pseudo-exist']);
    }

    echo(json_encode($result));
} catch (PDOException $e) {
    http_response_code(500); // Erreur serveur
    echo json_encode(["error" => "Erreur lors de la récupération des données"]);
}
?>
<?php 
    include('/var/www/html/php/connect_params.php');
    // Quelques fonctions pour avoir les infos des offres
    function getTypeOffre($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqTypeOffre = "SELECT 
            CASE
                WHEN EXISTS (SELECT 1 FROM sae._offre_restauration r WHERE r.id_offre = o.id_offre) THEN 'Restauration'
                WHEN EXISTS (SELECT 1 FROM sae._offre_parc_attraction p WHERE p.id_offre = o.id_offre) THEN 'Parc attraction'
                WHEN EXISTS (SELECT 1 FROM sae._offre_spectacle s WHERE s.id_offre = o.id_offre) THEN 'Spectacle'
                WHEN EXISTS (SELECT 1 FROM sae._offre_visite v WHERE v.id_offre = o.id_offre) THEN 'Visite'
                WHEN EXISTS (SELECT 1 FROM sae._offre_activite a WHERE a.id_offre = o.id_offre) THEN 'Activité'
                ELSE 'Inconnu'
            END AS offreSpe
            FROM sae._offre o
            WHERE o.id_offre = :id_offre;";

            try {
                $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

                // Préparation et exécution de la requête
                $stmt = $conn->prepare($reqTypeOffre);
                $stmt->bindParam(':id_offre', $id_offre, PDO::PARAM_INT); // Lié à l'ID de l'offre
                $stmt->execute();
                $type_offre = $stmt->fetch(PDO::FETCH_COLUMN);
                print_r($type_offre);
                $conn = null;
                return $type_offre;
            } catch(Exception $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
    }

?>
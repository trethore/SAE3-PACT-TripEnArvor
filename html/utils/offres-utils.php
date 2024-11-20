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

                $conn = null;
                return $type_offre;
            } catch(Exception $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
    }

    function getFirstIMG($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqIMG = "SELECT img.lien_fichier 
            FROM sae._image img
            JOIN sae._offre_contient_image oci 
            ON img.lien_fichier = oci.id_image
            WHERE oci.id_offre = :id_offre
            LIMIT 1;";
        
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

            // Préparer et exécuter la requête
            $stmtIMG = $conn->prepare($reqIMG);
            $stmtIMG->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtIMG->execute();

            // Récupérer la première image
            $image = $stmtIMG->fetch(PDO::FETCH_ASSOC);



            if ($image && !empty($image['lien_fichier'])) {
                // Afficher l'image si elle existe
                $lienIMG = $image['lien_fichier'];
            } else {
                // Afficher une image par défaut
                $lienIMG = 'default-image.jpg';
            }

            $conn = null;
            return $lienIMG;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    function getIMGbyId($id_offre) {
        global $driver, $server, $dbname, $user, $pass;
        $reqIMG = "SELECT img.lien_fichier 
            FROM sae._image img
            JOIN sae._offre_contient_image oci 
            ON img.lien_fichier = oci.id_image
            WHERE oci.id_offre = :id_offre;";
        
        try {
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

            // Préparer et exécuter la requête
            $stmtIMG = $conn->prepare($reqIMG);
            $stmtIMG->bindParam(':id_offre', $id_offre, PDO::PARAM_INT);
            $stmtIMG->execute();

            // Récupérer toutes les images sous forme de tableau
            $images = $stmtIMG->fetchAll(PDO::FETCH_COLUMN);

            // Si aucune image trouvée, retourner une image par défaut
            if (empty($images)) {
                $images[] = 'default-image.jpg';
            }

            $conn = null;
            return $images;
        } catch (Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
?>
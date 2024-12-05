<?php 
    include($_SERVER['DOCUMENT_ROOT'] . '/php/connect_params.php');
    include($_SERVER['DOCUMENT_ROOT'] . '/utils/site-utils.php');
    function getTypeCompte($id_compte) {
        global $driver, $server, $dbname, $user, $pass;
        $reqTypeCompte = "SELECT 
                            CASE
                                WHEN EXISTS (SELECT 1 FROM sae._compte_professionnel_publique pu WHERE pu.id_compte = co.id_compte) THEN 'proPublique'
                                WHEN EXISTS (SELECT 1 FROM sae._compte_professionnel_prive pr WHERE pr.id_compte = co.id_compte) THEN 'proPrive'
                                ELSE 'Inconnu'
                            END AS comptePro
                            FROM sae._compte co
                            WHERE co.id_compte = :id_compte;";

            try {
                $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                $conn->prepare("SET SCHEMA 'sae';")->execute();

                // Préparation et exécution de la requête
                $stmt = $conn->prepare($reqTypeCompte);
                $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT); // Lié à l'ID du compte
                $stmt->execute();
                $type_compte = $stmt->fetch(PDO::FETCH_COLUMN);

                $conn = null;
                return $type_compte;
            } catch(Exception $e) {
                print "Erreur !: " . $e->getMessage() . "<br>";
                die();
            }
    }

    function pseudo_exist(string $pseudo) : bool {
        try {
            global $driver, $server, $dbname, $user, $pass;
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            $query = "SELECT COUNT(*) FROM sae._compte_membre WHERE pseudo = ?";

            // Préparation et exécution de la requête
            $stmt = $conn->prepare($query);
            $stmt->execute([$pseudo]);
            $result = $stmt->fetch()['count'] > 0;

            $conn = null;
            return $result;
        } catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }
?>
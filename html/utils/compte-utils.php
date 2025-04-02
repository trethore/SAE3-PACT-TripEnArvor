<?php 
    require_once($_SERVER['DOCUMENT_ROOT'] . '/utils/file_paths-utils.php');

    require_once($_SERVER['DOCUMENT_ROOT'] . CONNECT_PARAMS);
    require_once($_SERVER['DOCUMENT_ROOT'] . SITE_UTILS);
   
    function getTypeCompte($id_compte) {
        global $driver, $server, $dbname, $user, $pass;
        $reqTypeCompte = "SELECT 
                            CASE
                                WHEN EXISTS (SELECT 1 FROM sae._compte_professionnel_publique pu WHERE pu.id_compte = co.id_compte) THEN 'proPublique'
                                WHEN EXISTS (SELECT 1 FROM sae._compte_professionnel_prive pr WHERE pr.id_compte = co.id_compte) THEN 'proPrive'
                                WHEN EXISTS (SELECT 1 FROM sae._compte_membre m WHERE m.id_compte = co.id_compte) THEN 'membre'
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

    function email_exist(string $email) : bool {
        try {
            global $driver, $server, $dbname, $user, $pass;
            $conn = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $conn->prepare("SET SCHEMA 'sae';")->execute();

            $query = "SELECT COUNT(*) FROM sae._compte WHERE email = ?";

            // Préparation et exécution de la requête
            $stmt = $conn->prepare($query);
            $stmt->execute([$email]);
            $result = $stmt->fetch()['count'] > 0;

            $conn = null;
            return $result;
        } catch(Exception $e) {
            print "Erreur !: " . $e->getMessage() . "<br>";
            die();
        }
    }

    function generateOTP($length = 6) {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        return password_hash($otp, PASSWORD_DEFAULT);;
    }

    function saveOTP(PDO $pdo, $id_compte, $hashedOTP) {
        // Définir l'expiration, 1 minutes
        $expire_le = date("Y-m-d H:i:s", strtotime("+1 minutes"));
    
        $sql = "INSERT INTO _compte_otp (id_compte, code_otp, expire_le) VALUES (:id_compte, :code_otp, :expire_le)";
        $stmt = $pdo->prepare($sql);
    
        $stmt->bindParam(':id_compte', $id_compte, PDO::PARAM_INT);
        $stmt->bindParam(':code_otp', $hashedOTP, PDO::PARAM_STR);
        $stmt->bindParam(':expire_le', $expire_le, PDO::PARAM_STR);
    
        $stmt->execute();
    }
    
?>
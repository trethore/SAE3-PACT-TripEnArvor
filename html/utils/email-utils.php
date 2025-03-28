<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/PHPMailer/src/Exception.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/PHPMailer/src/PHPMailer.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/lib/PHPMailer/src/SMTP.php');

function sendEmail($to, $subject, $body, $altBody) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //mettre à 2 pour déboguer, 0 en production
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'redden.pact@gmail.com';                    
        $mail->Password   = 'yecr phsd lyhw kxmi';                            
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port       = 587;                                    

        //Recipients
        $mail->setFrom('redden.pact@gmail.com');
        $mail->addAddress($to);               

        //Content
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $altBody;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur lors de l'envoi de l'email : {$mail->ErrorInfo}");  
        return false;
    }
}

?>
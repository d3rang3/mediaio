<?php
namespace Mediaio;
//require "autoload.php";
require_once __DIR__ .'/vendor/autoload.php';
//require_once __DIR__ .'/Config.php';


//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Europe/Budapest');

/**
 * Handles sending e-mails
 */

class MailService
{

    static function sendContactMail($to,$subject,$content)
    {

        $config = json_decode(file_get_contents(__DIR__ . '/server/mailCredentials.json'), true);
        

        // Create a new phpmail instance
        $mail = new \PHPMailer\PHPMailer\PHPMailer();

        $mail->IsSMTP();
        $mail->Host = $config['server'];

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $config['security'];
        $mail->Port = $config['port'];
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($config['from'], 'Árpád Média IO');
        $mail->addBCC($config['from']);
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        $mail->Body = $content;
        // Send the mail
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return 500;
        } else {
            //echo 'Message has been sent';
            return 200;
        }

    }

    static function sendContactMailWithAttachment(){

    }

    
}
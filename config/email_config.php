<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendRegistrationConfirmationEmail($email, $fullName)
{
    $mail = new PHPMailer;
    // SMTP configuration 
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'Codeyaima3301@gmail.com';
    $mail->Password = 'hazi iped jkhy wsoa';
    $mail->SMTPSecure = 'tls'; // Use 'tls' or 'ssl'
    $mail->Port = 587; // Use the appropriate port for your SMTP configuration

    // Enable SMTP debugging and log errors
    $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Set to 2 for detailed debug output
    $mail->Debugoutput = function ($str, $level) {
        error_log("[$level] $str");
    };

    $mail->setFrom('codeyaima3301@gmail.com', 'Amir Maharjan');
    $mail->addAddress($email, $fullName);
    $mail->Subject = 'Registration Confirmation';
    $mail->Body = 'Thank you for registering!';

    try {
        if ($mail->send()) {
            return true;
        } else {
            error_log('Failed to send confirmation email. Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }
    } catch (Exception $e) {
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}
?>

<?php
/**
 * Created by PhpStorm.
 * User: EStrohbusch
 * Date: 3/29/2017
 * Time: 8:52 AM
 */
require 'PHPMailer\PHPMailerAutoload.php';
$mail = new PHPMailer;
//$mail->SMTPDebug = 3;                           // Enable verbose debug output
$mail->isSMTP();                                // Set mailer to use SMTP
$mail->Host = 'ets-exch2010.etsexpress.local';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                        // Enable SMTP authentication
$mail->Username = 'donotreply';                          // SMTP username
$mail->Password = 'RkFl!p9';                        // SMTP password
$mail->SMTPSecure = 'tls';                      // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                              // TCP port to connect to
$mail->setFrom('DoNotReply@EtsExpress.com', 'ETS EXPRESS'); // Who is this from?
$mail->isHTML(true);                                          // Set email format to HTML

$password = '123abc';
$user = 'Eric';
$mail->addAddress('eric.strohbusch@gmail.com');              // TO BE EXTRACTED
$mail->Subject = 'ETS EXPRESS RESET';
$mail->Body =
    'Hello, '.$user.', We are sorry to hear you are having difficulty...
<br>However, We are more than happy to assist you in resetting your password!
<br>Password: '.$password.'
<br>Please remember to keep this password in a safe place.
<br>If this does not solve your problem please email...';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if (!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}

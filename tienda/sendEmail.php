<?php
header("Access-Control-Allow-Origin:*");
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include('../../resources/commons/email/class/PHPMailerAutoload.php');

$message = '
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Tienda Moringa</title>
</head>
<body>
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 11px;">
<span style="font-size: 18px">☘️</span><h1 style="display:inline">Tienda Moringa </h1>
<br/>
    <span style="font-size: 18px">✉️</span><h1 style="display:inline">'.$_POST['subject'].'</h1>
  <p><strong>Email:</strong> '.$_POST['email'].'.</p>
  <p><strong>Mensaje:</strong> '.$_POST['message'].'.</p>
</div>
</body>
</html>
';

try {
$mail = new PHPMailer;
$mail->SetLanguage("ES");
$mail->CharSet = 'UTF-8';
$mail->isSMTP();
$mail->SMTPDebug = 0;
$mail->Debugoutput = 'html';
$mail->Host = 'smtp.mi.com.co';
$mail->Port = 465;
$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->Username = 'mifactura@gesadmin.co';
$mail->Password = 'Droi_123!';
$mail->setFrom('mifactura@gesadmin.co', 'TIENDA MORINGA');
$mail->IsHTML(true);
$mail->AddAddress("moringaventasinternet@gmail.com");
$mail->Subject = $_POST['subject'];
$mail->Body = $message;
$mail->AltBody = "Tienda Moringa";

if($mail->send()){
    echo "Mensaje enviado correctamente";
}
echo $mail->ErrorInfo;
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

?>
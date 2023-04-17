<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



function sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject, $template,$inicio){
	if($inicio==0){
		require 'PHPMailer/src/Exception.php';
	    require 'PHPMailer/src/PHPMailer.php';
	    require 'PHPMailer/src/SMTP.php';
	}    
	$mail = new PHPMailer;
	$mail->isSMTP();                            // Establecer el correo electrónico para utilizar SMTP
	$mail->Host = 'smtp.gmail.com';             // Especificar el servidor de correo a utilizar 
	$mail->SMTPAuth = true;                     // Habilitar la autenticacion con SMTP
//	$mail->Username = "ibnored@ibnorca.org";          // Correo electronico saliente ejemplo: tucorreo@gmail.com
	$mail->Username = "facturacion@ibnorca.org";          // Correo electronico saliente ejemplo: tucorreo@gmail.com
	$mail_setFromEmail=$mail->Username;
//	$mail->Password = "I8nor3d!1bn0"; 		// Tu contraseña de gmail
	$mail->Password = "$2023#B0l1v14nn0rm4l1z4c10n#"; 		// Tu contraseña de gmail
	$mail->SMTPSecure = 'tls';                  // Habilitar encriptacion, `ssl` es aceptada
	$mail->Port = 587;                          // Puerto TCP  para conectarse 
	$mail->setFrom($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe aparecer el correo electrónico. Puede utilizar cualquier dirección que el servidor SMTP acepte como válida. El segundo parámetro opcional para esta función es el nombre que se mostrará como el remitente en lugar de la dirección de correo electrónico en sí.
	$mail->addReplyTo($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe responder. El segundo parámetro opcional para esta función es el nombre que se mostrará para responder
	$mail->addAddress($mail_addAddress);   // Agregar quien recibe el e-mail enviado

	
	///////////////////////////////////////para la version de php 7
	$mail->SMTPOptions = array(
          'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
          )
      );
	///////////////////////////////////////////////////////////////77
	$message = file_get_contents($template);
	$message = str_replace('{{first_name}}', $mail_setFromName, $message);
	$message = str_replace('{{titulo_men}}', $mail_subject, $message);
	$message = str_replace('{{message}}', $txt_message, $message);
	$message = str_replace('{{customer_email}}', $mail_setFromEmail, $message);
	$mail->isHTML(true);  // Establecer el formato de correo electrónico en HTML
	
	// $mail->Subject = $mail_subject;
	$subject = $mail_subject;
	$subject = utf8_decode($subject);
	$mail->Subject = $subject;
	// $mail->Subject = $mail_subject;
	$mail->CharSet = 'UTF-8';


	
	$mail->msgHTML($message);

	if(!$mail->send()){
      return 0;
	}else{
	  return 1;
	}
}
function sendemailFiles($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject, $template,$inicio,$rutaArchivo,$nombreArchivo,$rutaARchivo2,$nombreArchivo2){
	if($inicio==0){
		require_once 'PHPMailer/src/Exception.php';
	    require_once 'PHPMailer/src/PHPMailer.php';
	    require_once 'PHPMailer/src/SMTP.php';
	}   
	//recibimos correos
	$mail = new PHPMailer;
	$mail->isSMTP();                            // Establecer el correo electrónico para utilizar SMTP
	$mail->Host = 'smtp.gmail.com';             // Especificar el servidor de correo a utilizar 
	// $mail->Host = 'smtp.gmail.com';             // Especificar el servidor de correo a utilizar 
	$mail->SMTPAuth = true;                     // Habilitar la autenticacion con SMTP
	//$mail->Username = 'ibnored@ibnorca.org';          // Correo electronico saliente ejemplo: tucorreo@gmail.com
	$mail->Username = 'facturacion@ibnorca.org';          // Correo electronico saliente ejemplo: tucorreo@gmail.com
	$mail_setFromEmail=$mail->Username;
	//$mail->Password = 'I8nor3d!1bn0'; 		// Tu contraseña de gmail
	$mail->Password = "1bn0Factur4s"; 		// Tu contraseña de gmail
	$mail->SMTPSecure = 'tls';                  // Habilitar encriptacion, `ssl` es aceptada
	$mail->Port = 587;                          // Puerto TCP  para conectarse 
	$mail->setFrom($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe aparecer el correo electrónico. Puede utilizar cualquier dirección que el servidor SMTP acepte como válida. El segundo parámetro opcional para esta función es el nombre que se mostrará como el remitente en lugar de la dirección de correo electrónico en sí.
	$mail->addReplyTo($mail_setFromEmail, $mail_setFromName);//Introduzca la dirección de la que debe responder. El segundo parámetro opcional para esta función es el nombre que se mostrará para responder
	
	$correo_array=explode( ',', $mail_addAddress);//convertimos a array para el envio multiple
	for($i = 0; $i < count($correo_array); $i++) {	    
	    $mail->addAddress($correo_array[$i]);   // Agregar quien recibe el e-mail enviado
	}

	$mail->addAttachment($rutaArchivo,$nombreArchivo);
	//$mail->addAttachment($rutaArchivo2,$nombreArchivo2);
	
	
	///////////////////////////////////////para la version de php 7
	$mail->SMTPOptions = array(
          'ssl' => array(
          'verify_peer' => false,
          'verify_peer_name' => false,
          'allow_self_signed' => true
          )
      );
	///////////////////////////////////////////////////////////////77
	$message = file_get_contents($template);
	$message = str_replace('{{first_name}}', $mail_setFromName, $message);
	$message = str_replace('{{titulo_men}}', $mail_subject, $message);
	$message = str_replace('{{message}}', $txt_message, $message);
	$message = str_replace('{{customer_email}}', $mail_setFromEmail, $message);
	$mail->isHTML(true);  // Establecer el formato de correo electrónico en HTML
	
	$subject = $mail_subject;
	$subject = utf8_decode($subject);
	$mail->Subject = $subject;
	// $mail->Subject = $mail_subject;
	$mail->CharSet = 'UTF-8';



	// $mail->Subject = $mail_subject;
	$mail->msgHTML($message);

	if(!$mail->send()){
      // echo 'Message could not be sent.';
	  // echo 'Mailer Error: ' . $mail->ErrorInfo;
		return 0;
	}else{
	  return 1;
	}

}
?>
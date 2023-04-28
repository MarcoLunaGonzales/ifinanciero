<?php
require '../notificaciones_sistema/PHPMailer/send.php';
require_once '../conexion.php';
//$dbh = new Conexion();
$dbhB = new Conexion();

$fechaActual=date("Y-m-d H:m:s");
$asunto="TEST EMAIL";
$mensaje="Vamos Probando";
		$mail_username="";//Correo electronico emisor
		$mail_userpassword="";// contraseña correo emisor
		$mail_addAddress="lunagonzalesmarco@gmail.com";//correo electronico destino
		$template="../notificaciones_sistema/PHPMailer/email_template_factura.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
		/*Inicio captura de datos enviados por $_POST para enviar el correo */
		$mail_setFromEmail=$mail_username;
		$mail_setFromName="IBNORCA";
		$txt_message=$mensaje;
		$mail_subject=$asunto; //el subject del mensaje

		$flag=sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template,0);
		if($flag!=0){//se envio correctamente
		 	echo "OK";
	   
		}else{//error al enviar el correo
		 	echo "NO";
		}
?>

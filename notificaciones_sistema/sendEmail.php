<?php
require_once '../conexion.php';
require_once 'configModule.php'; //configuraciones
require_once '../styles.php';
require_once '../functionsGeneral.php';
require 'PHPMailer/send.php';

$correo=$_GET['correo'];
$personal=$_GET['personal'];
$evento=$_GET['evento'];
$titulo=$_GET['titulo'];

$mensaje=$_GET['mensaje'];

//datos para el envio
$dbhB = new Conexion();
 
    
    $mail_username="";//Correo electronico saliente ejemplo: tucorreo@gmail.com
	$mail_userpassword="";//Tu contraseña de gmail
	$mail_addAddress=$correo;//correo electronico que recibira el mensaje
	$template="PHPMailer/email_template.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
				
				/*Inicio captura de datos enviados por $_POST para enviar el correo */
	$mail_setFromEmail=$mail_username;
	$mail_setFromName="IBNORCA";
	$txt_message=$mensaje;
	$mail_subject=$titulo; //el subject del mensaje
	
	$flag=sendemail($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template);			
     if($flag!=0){
     	echo "1$$$".$correo;
     	$sqlB="INSERT INTO eventos_sistemapersonal (cod_eventosistema,cod_personal,cod_estadoreferencial,texto) 
     	VALUES('$evento','$personal','1','$mensaje')";
        $stmtB = $dbhB->prepare($sqlB);
        $stmtB->execute();
       
	 }else{
	 	echo "0$$$".$correo;
	 }

/*$dbhB = new Conexion();
*/
?>
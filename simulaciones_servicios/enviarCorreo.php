<?php
require '../notificaciones_sistema/PHPMailer/send.php';
//RECIBIMOS LAS VARIABLES
$codigo_facturacion=$_POST['codigo_facturacion'];
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
$nro_factura=$_POST['nro_factura'];
$correo_destino=$_POST['correo_destino'];
// $asunto=$_POST['asunto'];
// $mensaje=$_POST['mensaje'];

$asunto="ENVIO FACTURA - IBNORCA";
$mensaje="Estimado cliente,<br>\n<br>\n Le Hacemos el envío de la Factura Nro. ".$nro_factura.".<br>\n<br>\nSaludos.";

if($correo_destino==''||$asunto==''||$mensaje==''){
	// echo "<script>alert('Los Campos marcados con * son obligatorios');location.href='javascript:history.back()';</script>";
	echo "0$$$";
}else{

//obtenemos el archivo ya generado en PDF
$nom="IBNORCA-".$codigo_facturacion."-".$nro_factura.".pdf";//nombre factura
$dir="../simulaciones_servicios/facturas/";
$sw=0;
$d=opendir($dir.".");
while ($file=readdir($d)) {	
	if($nom==$file){
		// echo "si<br>";
		$sw=1;
	}
	// echo "filename : ".$file."<br>";
}
closedir($d);
if($sw==1){//existe archivo
		// $rutaArchivo = opendir("simulaciones_servicios/facturas/".$nom.".pdf"); //ruta actual
	$rutaArchivo=$dir.$nom;
	$nombreArchivo = $nom; //
	$mail_username="noresponse@minkasoftware.com";//Correo electronico emisor
	$mail_userpassword="minka@2019";// contraseña correo emisor
	$mail_addAddress=$correo_destino;//correo electronico destino
	$template="../notificaciones_sistema/PHPMailer/email_template.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
	/*Inicio captura de datos enviados por $_POST para enviar el correo */
	$mail_setFromEmail=$mail_username;
	$mail_setFromName="IBNORCA";
	$txt_message=$mensaje;
	$mail_subject=$asunto; //el subject del mensaje

	$flag=sendemailFiles($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template,0,$rutaArchivo,$nombreArchivo);
	if($flag!=0){//se envio correctamente
	 	echo "1$$$".$correo_destino;
	 	// $sqlB="INSERT INTO eventos_sistemapersonal (cod_eventosistema,cod_personal,cod_estadoreferencial,texto) 
	 	// VALUES('$evento','$personal','1','$mensaje')";
	  //   $stmtB = $dbhB->prepare($sqlB);
	  //   $stmtB->execute();
	   
	}else{
	 	echo "2$$$".$correo_destino;
	}
}else{
	// echo "<script>alert('ERROR AL ENVIAR CORREO... Antes de envíar correo, genere la factura, gracias...');location.href='javascript:history.back()';</script>";
	echo "3$$$";
}


}
?>
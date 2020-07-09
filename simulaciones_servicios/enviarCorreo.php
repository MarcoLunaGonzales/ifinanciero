<?php
require '../notificaciones_sistema/PHPMailer/send.php';
require_once '../conexion.php';
//$dbh = new Conexion();
$dbhB = new Conexion();
//RECIBIMOS LAS VARIABLES
$codigo_facturacion=$_POST['codigo_facturacion'];
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
$nro_factura=$_POST['nro_factura'];
$correo_destino=$_POST['correo_destino'];
$listaCorreos=explode(",", $correo_destino);
//$correo_destino=trim($correo_destino,',');

$fechaActual=date("Y-m-d H:m:s");
$asunto="Envio De Factura";
$mensaje="Estimado Cliente,<br>\n<br>\n Adjunto la Factura Nro. ".$nro_factura.".<br>\n<br>\nSaludos.";
// echo $correo_destino."<br>";
// echo $asunto."<br>";
// echo $mensaje."<br>";

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
		$mail_username="";//Correo electronico emisor
		$mail_userpassword="";// contraseña correo emisor
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
		 	$sql="UPDATE facturas_venta set cod_estadofactura='3' where codigo=$codigo_facturacion";
		    $stmtB = $dbhB->prepare($sql);
		    $stmtB->execute();
            for ($crr=0; $crr < count($listaCorreos); $crr++) { 
            	$corr=$listaCorreos[$crr];
		        $sqlInsert="INSERT INTO log_instancias_envios_correo(detalle,fecha,cod_alumno,cod_persona,correo,cod_factura) VALUES ('Envio de factura','$fechaActual',0,0,'$corr','$codigo_facturacion')";
		        $stmtBInsert = $dbhB->prepare($sqlInsert);
		        $stmtBInsert->execute();  	
            }
		   
		}else{//error al enviar el correo
		 	echo "2$$$".$correo_destino;
		}
	}else{
		// echo "<script>alert('ERROR AL ENVIAR CORREO... Antes de envíar correo, genere la factura, gracias...');location.href='javascript:history.back()';</script>";
		echo "3$$$";
	}
}
?>
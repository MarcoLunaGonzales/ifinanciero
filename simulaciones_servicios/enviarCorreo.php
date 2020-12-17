<?php
require '../notificaciones_sistema/PHPMailer/send.php';
require_once '../conexion.php';
//$dbh = new Conexion();
$dbhB = new Conexion();
//RECIBIMOS LAS VARIABLES
$codigo_facturacion=$_POST['codigo_facturacion'];
$cod_solicitudfacturacion=$_POST['cod_solicitudfacturacion'];
$nro_factura=$_POST['nro_factura'];
$razon_social=$_POST['razon_social'];

$correo_destino=$_POST['correo_destino'];
$listaCorreos=explode(",", $correo_destino);
//$correo_destino=trim($correo_destino,',');
$fechaFactura=strftime('%d/%m/%Y',strtotime(obtenerFechaFacturaVenta($codigo_facturacion)));
$fechaActual=date("Y-m-d H:m:s");
$asunto="Envio De Factura";
$mensaje="Estimado Cliente ".$razon_social.",<br>\n<br>\n Adjuntamos la Factura Nro. ".$nro_factura." de fecha ".$fechaFactura.".<br>\n<br>\nSaludos.";
// echo $correo_destino."<br>";
// echo $asunto."<br>";
// echo $mensaje."<br>";

if($correo_destino==''||$asunto==''||$mensaje==''){
	// echo "<script>alert('Los Campos marcados con * son obligatorios');location.href='javascript:history.back()';</script>";
	echo "0$$$";
}else{
	//obtenemos el archivo ya generado en PDF
	$nom="IBNORCA-C".$codigo_facturacion."-F".$nro_factura.".pdf";//nombre factura
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
		$template="../notificaciones_sistema/PHPMailer/email_template_factura.html";//Ruta de la plantilla HTML para enviar nuestro mensaje
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

function obtenerFechaFacturaVenta($codigo){
    $dbh = new Conexion();
    $stmtVerif = $dbh->prepare("SELECT fecha_factura FROM facturas_venta where codigo=$codigo");
    $stmtVerif->execute();
    $resultVerif = $stmtVerif->fetch();    
    $fecha = $resultVerif['fecha_factura'];
    return $fecha;
    }
?>
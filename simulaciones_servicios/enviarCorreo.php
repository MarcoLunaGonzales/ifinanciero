<?php
require_once '../notificaciones_sistema/PHPMailer/send.php';
require_once '../conexion.php';
require_once '../functions.php';
//$dbh = new Conexion();
$dbhB = new Conexion();

/* error_reporting(E_ALL);
 ini_set('display_errors', '1');
*/
$url_list_siat=obtenerValorConfiguracion(103);

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

$codigoFacturaSIAT=obtenerTransaccionSIAT($codigo_facturacion);

$asunto="Envio De Factura";
$mensaje="Estimado Cliente ".$razon_social.",<br>\n<br>\n Adjuntamos la Factura Nro. ".$nro_factura." de fecha ".$fechaFactura.".<br>\n<br>\nSaludos.";

if($correo_destino==''||$asunto==''||$mensaje==''){
	// echo "<script>alert('Los Campos marcados con * son obligatorios');location.href='javascript:history.back()';</script>";
	echo "0$$$";
}else{
	$carpetaSiat=obtenerValorConfiguracion(107);
	$rutaArchivoPHP=$url_list_siat."descargarFacturaXMLEnvioCorreo.php";

	$sIde = "MinkaSw123*";
    $sKey = "rrf656nb2396k6g6x44434h56jzx5g6";
    $parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "accion"=>"accionDescargarArchivos", "codVenta"=>$codigoFacturaSIAT);
	$json = callService($parametros,$rutaArchivoPHP);
	$json = preg_replace('/\xEF\xBB\xBF/', '', $json);
	$json = trim($json);
	$obj = json_decode($json);
	
	//var_dump($obj);

	$banderaEnvioArchivosCorreo=0;
	$nombreArchivo="";
	$nombreArchivo2="";
	$estadoX=0;
	$rutaArchivo="";

    	$estadoX=$obj->estado;
  		$banderaEnvioArchivosCorreo=$obj->banderaArchivos;
		$nombreArchivo=$obj->nameOnlyFile1;
		$nombreArchivo2=$obj->nameOnlyFile2;
		$rutaArchivo=$obj->rutaArchivoDescargar;
		$rutaArchivo=$url_list_siat.$rutaArchivo;

	if($banderaEnvioArchivosCorreo==1){			//existen los archivos
		$rutaArchivo1=$rutaArchivo.$nombreArchivo;
		$rutaArchivo2=$rutaArchivo.$nombreArchivo2;

		$mail_username="";
		$mail_userpassword="";
		$mail_addAddress=$correo_destino;
		$template="../notificaciones_sistema/PHPMailer/email_template_factura.html";//Ruta de la plantilla HTML para enviar nuestro 
		$mail_setFromEmail=$mail_username;
		$mail_setFromName="IBNORCA";
		$txt_message=$mensaje;
		$mail_subject=$asunto; //el subject del mensaje

		$flag=sendemailFiles($mail_username,$mail_userpassword,$mail_setFromEmail,$mail_setFromName,$mail_addAddress,$txt_message,$mail_subject,$template,0,$rutaArchivo1,$nombreArchivo,$rutaArchivo2,$nombreArchivo2);
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

function obtenerTransaccionSIAT($codigo){
    $dbh = new Conexion();
    $stmtVerif = $dbh->prepare("SELECT idTransaccion_siat FROM facturas_venta where codigo=$codigo");
    $stmtVerif->execute();
    $resultVerif = $stmtVerif->fetch();    
    $codigoSIAT = $resultVerif['idTransaccion_siat'];
    return $codigoSIAT;
    }
?>
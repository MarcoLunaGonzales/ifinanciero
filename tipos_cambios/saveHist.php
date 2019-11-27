<?php
session_start();
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$fechaHoraActual=date("Y-m-d H:i:s");

//$cantReg=contarTipoCambio($codigo,$fi,$ff);
$cantFilas=$_POST["cantidad_filas"];
$codigo=$_POST['codigo_tipo_cambio'];
//guardar las ediciones
for ($i=0;$i<$cantFilas;$i++){
	$valor=$_POST["valor".($i+1)];
	if($valor!=0 || $valor!=""){
     $fecha=$_POST["fecha".($i+1)]; 
     $sql="INSERT INTO tipo_cambiomonedas (cod_moneda,fecha,valor)VALUES ('$codigo','$fecha','$valor');";
     $stmt = $dbh->prepare($sql);
     $flagSuccess=$stmt->execute();
	}
} 

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}


?>

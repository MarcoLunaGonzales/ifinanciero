<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$filas=$_POST['cantidad_filas'];
$monto=$_POST['monto_contabilizar'];
$flagSuccess=false;
for ($i=1; $i <=$filas ; $i++) { 
  $codigo=$_POST['cod_libretadetalle'.$i];	
  // Prepare
  $stmt = $dbh->prepare("UPDATE libretas_bancariasdetalle set cod_estado=1 where codigo=:codigo");
  // Bind
  $stmt->bindParam(':codigo', $codigo);
  $flagSuccess=$stmt->execute();
}
showAlertSuccessError($flagSuccess,"../".$urlList);	
?>
<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codDistribucion=$_POST['codigo_distribucion'];
$porcentaje=$_POST['porcentaje'];
$total=$_POST['total'];



foreach( $codDistribucion as $key => $n ) {
 //echo "El Id es ".$n.", detalle es ".$detalle[$key].", cod_descuento es ".$codDescPerMes[$key];

 if ($total==100){
  $stmt = $dbh->prepare("UPDATE distribucion_gastosporcentaje set porcentaje=$porcentaje[$key]
   where codigo=$n");
  $flagSuccess=$stmt->execute();

 }

}

showAlertSuccessError($flagSuccess,"../".$urlList);

?>

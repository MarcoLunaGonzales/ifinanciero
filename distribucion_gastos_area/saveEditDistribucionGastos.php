<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codDistribucionD=$_POST['codigo_distribucion'];
$porcentaje=$_POST['porcentaje'];
$total=$_POST['total'];
$codDistribucionGastos=$_POST['codDistribucionGastos'];


foreach( $codDistribucionD as $key => $n ) {
 //echo "El Id es ".$n.", detalle es ".$detalle[$key].", cod_descuento es ".$codDescPerMes[$key];
	//echo "n:".$n."- porcentaje".$porcentaje[$key]."<br>";
  $stmt = $dbh->prepare("UPDATE distribucion_gastosarea_detalle set porcentaje=$porcentaje[$key]
   where codigo=$n and cod_distribucionarea=$codDistribucionGastos");
  $flagSuccess=$stmt->execute();

}

showAlertSuccessError($flagSuccess,"../".$urlDistribucionGastosDetalle."&codigo=".$codDistribucionGastos);

?>

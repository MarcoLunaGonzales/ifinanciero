<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codDescuento = $_POST["cod_descuento"];
$codMes = $_POST["cod_mes"];
$codGestion = $_POST["cod_gestion"];

$codPersona=$_POST['codigo_persona'];
$detalle=$_POST['detalle'];
$codDescPerMes=$_POST['codDescPerMes'];
$codEstado="1";
$montos=json_decode($_POST["montos"]);

foreach( $codPersona as $key => $n ) {
 //echo "El Id es ".$n.", detalle es ".$detalle[$key].", cod_descuento es ".$codDescPerMes[$key];
if($codDescPerMes[$key]==null){
  $stmtt = $dbh->prepare("INSERT INTO descuentos_personal_mes(cod_descuento,cod_personal,cod_gestion,cod_mes,monto,cod_estadoreferencial) 
  VALUES($codDescuento,$n,$codGestion,$codMes,$detalle[$key],$codEstado)");
  $flagSuccess=$stmtt->execute();
}else{
  $stmt = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$detalle[$key] where cod_personal=$n
  and cod_estadoreferencial=1 and cod_descuento=$codDescuento and cod_mes=$codMes and cod_gestion=$codGestion");
  $flagSuccess=$stmt->execute();
}

}
/*for ($i=0;$i<count($montos);$i++){ 
	$n=$montos[$i]->cod_persona;
 	$montoDet=$montos[$i]->monto;
 if($montos[$i]->desc_mes==null){
   $stmtt = $dbh->prepare("INSERT INTO descuentos_personal_mes(cod_descuento,cod_personal,cod_gestion,cod_mes,monto,cod_estadoreferencial) 
  VALUES($codDescuento,$n,$codGestion,$codMes,$montoDet,$codEstado)");
  $flagSuccess=$stmtt->execute();
 }else{
  $stmt = $dbh->prepare("UPDATE descuentos_personal_mes set monto=$montoDet where cod_personal=$n
  and cod_estadoreferencial=1 and cod_descuento=$codDescuento and cod_mes=$codMes and cod_gestion=$codGestion");
  $flagSuccess=$stmt->execute();
 }

}*/
$flagSuccess=true;
showAlertSuccessError($flagSuccess,"../".$urlListMesPersona."&cod_descuento=".$codDescuento."&cod_mes=".$codMes);

?>

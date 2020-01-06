<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codBono = $_POST["cod_bono"];
$codMes = $_POST["cod_mes"];
$codGestion = $_POST["cod_gestion"];

$codPersona=$_POST['codigo_persona'];
$detalle=$_POST['detalle'];
$codBonPerMes=$_POST['codBonPerMes'];
$codEstado="1";
$montos=json_decode($_POST["montos"]);

/*foreach( $codPersona as $key => $n ) {
 //echo "El Id es ".$n.", detalle es ".$detalle[$key].", cod_descuento es ".$codDescPerMes[$key];
if($codBonPerMes[$key]==null){
  $stmtt = $dbh->prepare("INSERT INTO bonos_personal_mes(cod_bono,cod_personal,cod_gestion,cod_mes,monto,cod_estadoreferencial) 
  VALUES($codBono,$n,$codGestion,$codMes,$detalle[$key],$codEstado)");
  $flagSuccess=$stmtt->execute();
}else{
  $stmt = $dbh->prepare("UPDATE bonos_personal_mes set monto=$detalle[$key] where cod_personal=$n
  and cod_estadoreferencial=1 and cod_bono=$codBono and cod_mes=$codMes and cod_gestion=$codGestion");
  $flagSuccess=$stmt->execute();
}

}*/
for ($i=0;$i<count($montos);$i++){ 
	$n=$montos[$i]->cod_persona;
 	$montoDet=$montos[$i]->monto;
 if($montos[$i]->bono_mes==null){
   $stmtt = $dbh->prepare("INSERT INTO bonos_personal_mes(cod_bono,cod_personal,cod_gestion,cod_mes,monto,cod_estadoreferencial) 
  VALUES($codBono,$n,$codGestion,$codMes,$montoDet,$codEstado)");
  $flagSuccess=$stmtt->execute();
 }else{
  $stmt = $dbh->prepare("UPDATE bonos_personal_mes set monto=$montoDet where cod_personal=$n
  and cod_estadoreferencial=1 and cod_bono=$codBono and cod_mes=$codMes and cod_gestion=$codGestion");
  $flagSuccess=$stmt->execute();
 }

}

$flagSuccess=true;
showAlertSuccessError($flagSuccess,"../".$urlListMesPersona."&cod_bono=".$codBono."&cod_mes=".$codMes);

?>

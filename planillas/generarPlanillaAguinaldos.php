<?php //ESTADO FINALIZADO

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalCodUnidad=$_SESSION["globalUnidad"];

$dbh = new Conexion();
$dbhI = new Conexion();
$dbhIPD = new Conexion();

$anio_actual=date('Y');//generar del anio y mes actual
// $anio_actual=2019;

//obteniendo codigo de gestion para el registro de planilla
$stmt = $dbh->prepare("SELECT codigo from gestiones where nombre=$anio_actual");
$stmt->execute();
$result= $stmt->fetch();
$cod_gestion=$result['codigo'];

$cod_estadoplanilla=1;
$created_by=1;
$modified_by=1;
// echo "mes ".$mes_actual;
//$fecha_actual=date('Y-m-d');
$cont=0;
//verificamos si exite registro de planilla en esta gestion
$stmtPlanillas = $dbh->prepare("SELECT codigo from planillas_aguinaldos where cod_gestion=$cod_gestion");
$stmtPlanillas->execute();
$stmtPlanillas->bindColumn('codigo',$codigo_planilla);
while ($row = $stmtPlanillas->fetch())
{
  $cont+=1;
}
if($cont==0){//insert - cuando no existe planilla
  $sqlInsert="INSERT into planillas_aguinaldos(cod_gestion,cod_estadoplanilla,created_by,modified_by) values(:cod_gestion,:cod_estadoplanilla,:created_by,:modified_by)";
  $stmtInsert = $dbhI->prepare($sqlInsert);
  $stmtInsert->bindParam(':cod_gestion', $cod_gestion);
  $stmtInsert->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':modified_by',$modified_by);
  $flagSuccess=$stmtInsert->execute();
}else{
  $flagSuccess=0;//alerta indicando que ya existe planilla del mes
}
showAlertSuccessError4($flagSuccess,$urlPlanillasAguinaldosList);



?>
<?php //ESTADO FINALIZADO

require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'rrhh/configModule.php';

$globalCodUnidad=$_SESSION["globalUnidad"];

$dbh = new Conexion();
$dbhI = new Conexion();
$dbhIPD = new Conexion();

// $anio_actual=date('Y');//generar del anio y mes actual
// $mes_actual=date('m');//generar del anio y mes actual
$mes_actual=$_SESSION['globalMes'];
$anio_actual=$_SESSION['globalNombreGestion'];


//obteniendo codigo de gestion para el registro de planilla
$stmt = $dbh->prepare("SELECT * from gestiones where nombre=$anio_actual");
$stmt->execute();
$result= $stmt->fetch();
$cod_gestion=$result['codigo'];

$cod_mes=(integer)$mes_actual;
$cod_estadoplanilla=1;
$created_by=1;
$modified_by=1;
// echo "mes ".$mes_actual;
//$fecha_actual=date('Y-m-d');
$cont=0;
$comprobante=0;
//verificamos si exite registro de planilla en este mes
$stmtPlanillas = $dbh->prepare("SELECT codigo from planillas where cod_gestion=$cod_gestion and cod_mes=$cod_mes");
$stmtPlanillas->execute();
$stmtPlanillas->bindColumn('codigo',$codigo_planilla);
while ($row = $stmtPlanillas->fetch())
{
  $cont+=1; 
}
if($cont==0){//insert - cuando no existe planilla
  $sqlInsert="INSERT into planillas(cod_gestion,cod_mes,cod_estadoplanilla,created_by,modified_by,comprobante) values(:cod_gestion,:cod_mes,:cod_estadoplanilla,:created_by,:modified_by,:comprobante)";
  $stmtInsert = $dbhI->prepare($sqlInsert);
  $stmtInsert->bindParam(':cod_gestion', $cod_gestion);
  $stmtInsert->bindParam(':cod_mes',$cod_mes);
  $stmtInsert->bindParam(':cod_estadoplanilla',$cod_estadoplanilla);
  $stmtInsert->bindParam(':created_by',$created_by);
  $stmtInsert->bindParam(':modified_by',$modified_by);
  $stmtInsert->bindParam(':comprobante',$comprobante);
  $flagSuccess=$stmtInsert->execute();
}else{
  $flagSuccess=0;//alerta indicando que ya existe planilla del mes
}
showAlertSuccessError3($flagSuccess,$urlPlanillasSueldoList);



?>
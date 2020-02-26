<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$plantilla=$_GET['plantilla'];
$codigo=$_GET['codigo'];
$cant=$_GET['cant'];
$monto=$_GET['monto'];
$montoe=$_GET['montoe'];
$dias=$_GET['dias'];

$sql1="SELECT * from plantillas_servicios_auditores where cod_plantillaservicio=$plantilla and cod_tipoauditor=$codigo";
$stmt1 = $dbh->prepare($sql1);
$stmt1->execute();

$existe=0;$codigoFila=0;
 while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
    $existe=1;
    $codigoFila=$rowServ['codigo'];
}

if($existe==0){
    $sql="INSERT INTO plantillas_servicios_auditores (cod_plantillaservicio, cod_tipoauditor,cantidad,monto,cod_estadoreferencial,dias,monto_externo,cod_externolocal) 
       VALUES ('".$plantilla."','".$codigo."','".$cant."','".$monto."', 1,'".$dias."','".$montoe."',1)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
   echo "0"; 
}else{
	$sql="UPDATE plantillas_servicios_auditores SET cantidad='".$cant."',monto='".$monto."',monto_externo='".$montoe."' WHERE codigo=$codigoFila ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  echo "1"; 
}
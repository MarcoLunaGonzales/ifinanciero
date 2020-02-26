<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


if(isset($_GET['cod'])){
	$cod=$_GET['cod'];
	$montoal=$_GET['m'];
	$montoalExt=$_GET['me'];
	$monto=$_GET['nm'];
	$montoExt=$_GET['nme'];
	$glosa=$_GET['g'];
  $dbh = new Conexion();
  $sqlInsert="UPDATE plantillas_servicios_detalle SET monto_unitario='$monto',monto_total='$monto',glosa='$glosa',editado_alumno='$montoal',editado_alumnoext='$montoalExt',monto_totalext='$montoExt' WHERE codigo=$cod";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $stmtInsert->execute();
}

?>
<?php
require_once 'conexion.php';
require_once 'configModule.php';


$dbh = new Conexion();

// $table="componentessis";
// $urlRedirect="../index.php?opcion=listComponentesSIS";

$codigo=$codigo;

//cambiamos el estado de la distribucion activa a inactiva
$stmt = $dbh->prepare("UPDATE distribucion_gastosporcentaje set estado=0 where estado=1");
$flagSuccess=$stmt->execute();
if($flagSuccess){
	//activamos la nieva distribucion
	$stmtD = $dbh->prepare("UPDATE distribucion_gastosporcentaje set estado=1 where codigo='$codigo'");	
	$flagSuccess=$stmtD->execute();
}
showAlertSuccessError($flagSuccess,$urlList);

?>

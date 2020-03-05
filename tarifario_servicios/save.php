<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();



$numero_regiones=$_POST["numero_regiones"];
for ($i=1; $i <=$numero_regiones ; $i++) { 
	$numero_filas=$_POST["numero_filas".$i];
	for ($j=1; $j <=$numero_filas; $j++) { 
		$monto_tarifario1=$_POST["monto_tarifario1FFF".$i."FFF".$j];
		$monto_tarifario2=$_POST["monto_tarifario2FFF".$i."FFF".$j];
		$monto_tarifario3=$_POST["monto_tarifario3FFF".$i."FFF".$j];
		$monto_tarifario4=$_POST["monto_tarifario4FFF".$i."FFF".$j];

		$codigos1=$_POST["codigos1FFF".$i."FFF".$j];
		$codigos2=$_POST["codigos2FFF".$i."FFF".$j];
		$codigos3=$_POST["codigos3FFF".$i."FFF".$j];
		$codigos4=$_POST["codigos4FFF".$i."FFF".$j];
       
        $stmt1 = $dbh->prepare("UPDATE $table SET monto='$monto_tarifario1' where codigo=$codigos1");
        $stmt1->execute();
        $stmt2 = $dbh->prepare("UPDATE $table SET monto='$monto_tarifario2' where codigo=$codigos2");
        $stmt2->execute();
        $stmt3 = $dbh->prepare("UPDATE $table SET monto='$monto_tarifario3' where codigo=$codigos3");
        $stmt3->execute();
        $stmt4 = $dbh->prepare("UPDATE $table SET monto='$monto_tarifario4' where codigo=$codigos4");
        $stmt4->execute();
	}
}

$flagSuccess=true;
showAlertSuccessError($flagSuccess,"../".$urlList);

?>
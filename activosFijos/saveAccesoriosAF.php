<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../perspectivas/configModule.php';

$result=0;


$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$idAccE=$_POST['idAccE'];
$codigoAF=$_POST['codigoAF'];
$nombreAcc=$_POST['nombreAcc'];
$estadoAcc=$_POST['estadoAcc'];



//echo "llega ".$cod_estadoasignacionaf;
//$fecha_recepcion=date("Y-m-d H:i:s");

if($estadoAcc==6){
	$cod_estadoreferencialAcc=2;
	$stmtD = $dbh->prepare("UPDATE accesorios_af set cod_estadoreferencialAcc=:cod_estadoreferencialAcc where codigo = :idAccE");
	    $stmtD->bindParam(':idAccE', $idAccE);
	    $stmtD->bindParam(':cod_estadoreferencialAcc', $cod_estadoreferencialAcc);
	    if($stmtD->execute()){
		      $result =1;
			}
}else{
	if($idAccE==0){
		$cod_estadoreferencialAcc=1;
		$stmtI = $dbh->prepare("INSERT INTO accesorios_af (cod_activofijo, nombre, cod_estadoaccesorioaf,cod_estadoreferencialAcc)
		 VALUES (:codigoAF, :nombre,  :estadoAcc,:cod_estadoreferencialAcc)");
		// Bind
		$stmtI->bindParam(':codigoAF', $codigoAF);
		$stmtI->bindParam(':nombre', $nombreAcc);
		$stmtI->bindParam(':estadoAcc', $estadoAcc);
		$stmtI->bindParam(':cod_estadoreferencialAcc', $cod_estadoreferencialAcc);
		
		if($stmtI->execute()){
		      $result =1;
		    }
	}else{
		$stmtU = $dbh->prepare("UPDATE accesorios_af set nombre=:nombreAcc,cod_estadoaccesorioaf=:estadoAcc where codigo = :idAccE");
	    $stmtU->bindParam(':idAccE', $idAccE);
	    $stmtU->bindParam(':nombreAcc', $nombreAcc);
	    $stmtU->bindParam(':estadoAcc', $estadoAcc);
	    if($stmtU->execute()){
		      $result =1;
			}

	}
}

echo $result;
$dbh=null;

?>
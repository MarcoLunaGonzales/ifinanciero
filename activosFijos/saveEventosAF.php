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
$idEve=$_POST['idEveE'];
$codigoAF=$_POST['codigoAF'];
$nombreEve=$_POST['nombreEve'];
$estadoEve=$_POST['estadoEve'];
$cod_personal=$_POST['personalEve'];
$fecha=date("Y-m-d H:i:s");

//echo "llgo: ".$cod_personal."---";



if($estadoEve==2){
	
	$stmtD = $dbh->prepare("UPDATE eventos_af set cod_estadoreferencial=:estadoEve where codigo = :idEve");
	    $stmtD->bindParam(':idEve', $idEve);
	    $stmtD->bindParam(':estadoEve', $estadoEve);
	    if($stmtD->execute()){
		      $result =1;
			}
}else{
	if($idEve==0){
		$stmtI = $dbh->prepare("INSERT INTO eventos_af (cod_activofijo, nombre,fecha, cod_estadoreferencial,cod_personalresponsable) VALUES (:codigoAF, :nombre,:fecha,:estadoEve,:cod_personal)");
		// Bind
		$stmtI->bindParam(':codigoAF', $codigoAF);
		$stmtI->bindParam(':nombre', $nombreEve);
		$stmtI->bindParam(':fecha', $fecha);
		$stmtI->bindParam(':estadoEve', $estadoEve);
		$stmtI->bindParam(':cod_personal', $cod_personal);
		if($stmtI->execute()){
		      $result =1;
		    }
	}else{		
		$stmtU = $dbh->prepare("UPDATE eventos_af set nombre=:nombreEve,cod_personalresponsable=:cod_personal where codigo = :idEve");
	    $stmtU->bindParam(':idEve', $idEve);
	    $stmtU->bindParam(':nombreEve', $nombreEve);
	    $stmtU->bindParam(':cod_personal', $cod_personal);
	    if($stmtU->execute()){
		      $result =1;
			}
	}
}

echo $result;
$dbh=null;

?>
<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$result=0;


$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();
//recibimos variables 
$cod_cargo=$_POST["cod_cargo"];
$nroFila=$_POST["nroFila"];
// echo "cod_cargo: ".$cod_cargo."<br>";
// echo "nroFila: ".$nroFila;
//limpiamos escala salarial con cod_Cargo para insertar nuevamente
$sqlDelete="DELETE from cargos_escala_salarial where cod_cargo=$cod_cargo";
$stmtDelete = $dbhU->prepare($sqlDelete);
$stmtDelete->execute();

$cod_estadoreferencial=1;
for ($i=1;$i<=$nroFila;$i++){
	$cod_nivel_escala=$_POST['cod_nivel_escala'.$i];	
	$monto=$_POST['monto'.$i];	

	// 	echo "codigo_rendicionA: ".$cod_rendicion."<br>";
	// echo "cod_tipo_documentoA: ".$tipo_doc."<br>";
	// echo "numero_doc: ".$numero_doc."<br>";
	// echo "fecha_doc: ".$fecha_doc."<br>";
	// echo "monto_A: ".$monto_A."<br>";
	// echo "observacionesA: ".$observacionesA."<br>";
	//insertamos rendicones_detalle
	$sql="INSERT INTO cargos_escala_salarial(cod_cargo,cod_nivel_escala_salarial,monto,cod_estadoreferencial) values($cod_cargo,$cod_nivel_escala,$monto,$cod_estadoreferencial)";
	$stmtU = $dbhU->prepare($sql);
	$flagSuccess=$stmtU->execute();

}

if($flagSuccess){
	showAlertSuccessError($flagSuccess,$urlCargosEscalaSalarial."&codigo=".$cod_cargo);
}else{
	showAlertSuccessError($flagSuccess,$urlCargosEscalaSalarial."&codigo=".$cod_cargo);
}
//
?>
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
set_time_limit(300);


$sIde = "monitoreo"; 
$sKey = "837b8d9aa8bb73d773f5ef3d160c9b17";

//NORMAS
$parametros=array("sIdentificador"=>$sIde, "sKey"=>$sKey, "TipoLista"=>"Todos");
$url="http://ibnored.ibnorca.org/wsibno/catalogo/ws-catalogo-nal.php";
$tableInsert="normas";
$json=callService($parametros, $url);
$obj=json_decode($json);
header('Content-type: application/json'); 	
print_r($json); 	


$stmtDel=$dbh->prepare("DELETE FROM $tableInsert");
$flagDel=$stmtDel->execute();

$detalle=$obj->lista;
foreach ($detalle as $objDet){
	$codigoX=$objDet->IdNorma;
	$nombreX=strtoupper(clean_string($objDet->NombreNorma));
	$abreviaturaX=strtoupper($objDet->CodigoNorma);
	$codSectorX=$objDet->IdSector;
	$estadoX="1";

	$stmt = $dbh->prepare("INSERT INTO $tableInsert (codigo, nombre, abreviatura, cod_sector,cod_estado) VALUES (:codigo, :nombre, :abreviatura, :cod_sector, :cod_estado)");
	$stmt->bindParam(':codigo', $codigoX);
	$stmt->bindParam(':nombre', $nombreX);
	$stmt->bindParam(':abreviatura', $abreviaturaX);
	$stmt->bindParam(':cod_sector', $codSectorX);
	$stmt->bindParam(':cod_estado', $estadoX);
	$flagSuccess=$stmt->execute();
}

?>

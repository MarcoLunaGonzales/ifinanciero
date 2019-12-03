<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];
// Prepare
$stmt = $dbh->prepare("UPDATE $table set cod_estadoreferencial=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);
$flagSuccess=$stmt->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,$urlList2);	
}else{
	showAlertSuccessError(false,$urlList2);
}
?>
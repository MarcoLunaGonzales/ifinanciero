<?php
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
$codigo=$_GET['codigo'];

// Prepare
$stmt = $dbh->prepare("UPDATE $table set cod_estadoreferencial=2 where codigo=:codigo");
// Bind
$stmt->bindParam(':codigo', $codigo);

$flagSuccess=$stmt->execute();
if(isset($_GET['q'])){
	 $q=$_GET['q'];
	 $s=$_GET['s'];
	 $u=$_GET['u'];
	 showAlertSuccessError($flagSuccess,$urlList."&q=".$q."&s=".$s."&u=".$u);
}else{
	showAlertSuccessError($flagSuccess,$urlList);
}

?>
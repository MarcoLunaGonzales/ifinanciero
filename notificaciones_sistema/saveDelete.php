<?php
require_once 'conexion.php';
require_once 'configModule.php'; //configuraciones
require_once 'functionsGeneral.php';
require_once 'functions.php';

$codigo=$_GET['cod'];

//datos para el envio
$dbhB = new Conexion();
  $sqlB="UPDATE eventos_sistemapersonal SET cod_estadoreferencial=2 WHERE codigo=$codigo";
 $stmtB = $dbhB->prepare($sqlB);
 $flagSuccess=$stmtB->execute();
if($flagSuccess==true){
	showAlertSuccessError(true,$urlList);	
}else{
	showAlertSuccessError(false,$urlList);
}
?>
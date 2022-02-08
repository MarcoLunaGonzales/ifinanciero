<?php
//session_start();
require_once 'conexion.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
require_once 'tipos_cambios/configModule.php';

$dbh = new Conexion();

$fecha=date("Y-m-d");
$codigo=$_GET["codigo"];

 $sql="DELETE from tipo_cambiomonedas where cod_moneda='$codigo' and fecha='$fecha'";
 //echo $sql;
 $stmt = $dbh->prepare($sql);
 $flagSuccess=$stmt->execute();    

 if($flagSuccess==true){
  showAlertSuccessError(true,$urlList); 
}


?>

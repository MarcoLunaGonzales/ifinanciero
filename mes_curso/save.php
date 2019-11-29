<?php
session_start();
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$fechaHoraActual=date("Y-m-d H:i:s");

$codigo=$_GET['codigo']; 
     $sql1="UPDATE meses_trabajo SET cod_estadomesestrabajo=1 where cod_estadomesestrabajo=3";
     $stmt1 = $dbh->prepare($sql1);
     $stmt1->execute();

     $sql="UPDATE meses_trabajo SET cod_estadomesestrabajo=3 where codigo=$codigo";
     $stmt = $dbh->prepare($sql);
     $flagSuccess=$stmt->execute();
	 

if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}

?>

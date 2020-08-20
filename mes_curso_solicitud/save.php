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
$estado=$_GET['estado'];
if($estado==3){    
    $sql1="UPDATE meses_trabajo_solicitudes SET cod_estadomesestrabajo=1 where cod_estadomesestrabajo=3";
    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute();
    $sql="UPDATE meses_trabajo_solicitudes SET cod_estadomesestrabajo=3 where codigo=$codigo";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}elseif($estado==2){    
    $sql="UPDATE meses_trabajo_solicitudes SET cod_estadomesestrabajo=2 where codigo=$codigo";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}elseif($estado==1){    
    $sql="UPDATE meses_trabajo_solicitudes SET cod_estadomesestrabajo=1 where codigo=$codigo";
    $stmt = $dbh->prepare($sql);
    $flagSuccess=$stmt->execute();
}   



$sqlMesActivo="SELECT cod_mes from meses_trabajo_solicitudes where cod_estadomesestrabajo=3";
$stmtMesActivo = $dbh->prepare($sqlMesActivo);
$stmtMesActivo->execute();
$mesActivo=0;
while ($rowMesActivo = $stmtMesActivo->fetch(PDO::FETCH_ASSOC)) {
      $mesActivo=$rowMesActivo['cod_mes'];
}
$_SESSION['globalMes']=$mesActivo;


if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList);	
}else{
	showAlertSuccessError(false,"../".$urlList);
}

?>

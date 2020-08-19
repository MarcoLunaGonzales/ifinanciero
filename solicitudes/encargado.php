<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$codigo=$_GET["cod"];
session_start();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalMes=$_SESSION['globalMes'];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$fechaHoraActual=date("Y-m-d H:i:s");
$userAdmin=obtenerValorConfiguracion(74);

$flagSuccess=false;
if(isset($_GET["personal_encargado"])){
 //insertamos la distribucion
  $sqlDel="DELETE FROM solicitud_recursosencargado where cod_solicitudrecurso=$codigo";
  $stmtDel = $dbh->prepare($sqlDel);
  $stmtDel->execute();
  
  if($_GET["personal_encargado"]>0){
  $codEncargado=$_GET["personal_encargado"];
  $sqlInsert="INSERT INTO solicitud_recursosencargado (cod_solicitudrecurso,cod_personal) 
        VALUES ('$codigo','$codEncargado')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();  
  } 
}


//       LINK DE RETORNO ("q" -> DESDE INTRANET)
if(isset($_GET['q'])){
  $q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];

}

if(isset($_GET['admin'])){
  $urlList2=$urlList;
  $urlc="&q=".$q."&s=".$s."&u=".$u."&v=".$v;
  if(isset($_GET['reg'])){
    $urlList2=$urlList3;
  }
}else{
  $urlc="&q=".$q."&s=".$s."&u=".$u;
  if(isset($_GET['r'])){
    $urlc=$urlc."&r=".$_GET['r'];
  }
}
if(isset($_GET['q'])){
	$q=$_GET['q'];
  $s=$_GET['s'];
  $u=$_GET['u'];
  $v=$_GET['v'];
  if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList4.$urlc);	
   }else{
	showAlertSuccessError(false,"../".$urlList4.$urlc);
   }
}else{
	if($flagSuccess==true){
	showAlertSuccessError(true,"../".$urlList4);	
   }else{
	showAlertSuccessError(false,"../".$urlList4);
   }
}

?>
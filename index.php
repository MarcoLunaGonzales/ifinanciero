<?php 
	//carga la plantilla con la header y el footer
set_time_limit(0);

session_start();
if(isset($_SESSION['logueado'])){
	require_once('layouts/layout.php');	
}else{
	if(isset($_GET['q'])){
       $q=$_GET['q'];
require_once 'conexion.php';
require_once 'functions.php';
$dbh = new Conexion();
    $sql="SELECT p.codigo,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre, p.cod_area, p.cod_unidadorganizacional, pd.perfil, pd.usuario_pon 
      from personal p, personal_datosadicionales pd 
      where p.codigo=pd.cod_personal and p.codigo='$q'";
//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('nombre', $nombre);
$stmt->bindColumn('cod_area', $codArea);
$stmt->bindColumn('cod_unidadorganizacional', $codUnidad);
$stmt->bindColumn('perfil', $perfil);

while ($rowDetalle = $stmt->fetch(PDO::FETCH_BOUND)) {
  $nombreUnidad=abrevUnidad($codUnidad);
  $nombreArea=abrevArea($codArea);
  //SACAMOS LA GESTION ACTIVA
  $sqlGestion="SELECT cod_gestion FROM gestiones_datosadicionales where cod_estado=1";
  $stmtGestion = $dbh->prepare($sqlGestion);
  $stmtGestion->execute();
  while ($rowGestion = $stmtGestion->fetch(PDO::FETCH_ASSOC)) {
    $codGestionActiva=$rowGestion['cod_gestion'];

    $sql1="SELECT cod_mes from meses_trabajo where cod_gestion='$codGestionActiva' and cod_estadomesestrabajo=3";
        $stmt1 = $dbh->prepare($sql1);
        $stmt1->execute();
        while ($row1= $stmt1->fetch(PDO::FETCH_ASSOC)) {
          $codMesActiva=$row1['cod_mes'];
        }
  }
  $nombreGestion=nameGestion($codGestionActiva);

  $_SESSION['globalUser']=$codigo;
  $_SESSION['globalNameUser']=$nombre;
  $_SESSION['globalGestion']=$codGestionActiva;
  $_SESSION['globalMes']=$codMesActiva;
  $_SESSION['globalNombreGestion']=$nombreGestion;


  $_SESSION['globalUnidad']=$codUnidad;
  $_SESSION['globalNombreUnidad']=$nombreUnidad;

  $_SESSION['globalArea']=$codArea;
  $_SESSION['globalNombreArea']=$nombreArea;
  $_SESSION['logueado']=1;
  $_SESSION['globalPerfil']=$perfil;

  if($codigo==90 || $codigo==89 || $codigo==227 || $codigo==195){
    $_SESSION['globalAdmin']=1;     
  }else{
    $_SESSION['globalAdmin']=0; 
  }
  
  $_SESSION['globalServerArchivos']="http://ibnored.ibnorca.org/itranet/documentos/";
  $_SESSION['modulo']=4;
}
         require_once('layouts/layout.php');	
	}else{
	 header("location:login.html");	
	}
	
}
 ?>

<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

$globalUser=$_SESSION["globalUser"];
$globalNombreGestion=$_SESSION["globalNombreGestion"];

$fechaHoraActual=date("Y-m-d H:i:s");

if(isset($_POST['nombre'])){
$nombre=$_POST['nombre'];
$abrev=$_POST['abreviatura'];
$cliente="";
$productos="";
$norma="";
$dias=$_POST['dias'];
$anios=$_POST['anio'];
$utmin=$_POST['utilidad_minima'];
$unidad=$_POST['unidad'];
$area=$_POST['area'];
$q=$_POST['q'];
$s=$_POST['s'];
$u=$_POST['u'];
$ingresoPresupuestado=obtenerValorConfiguracion(21);
  $codOficina=0;
  if(obtenerValorConfiguracion(52)==1){
    $codOficina=$unidad;
  }
if($area==39){
  $cantidadAuditorias=obtenerValorConfiguracion(17);
}else{
  $cantidadAuditorias=obtenerValorConfiguracion(18);
}

$ingresoPresupuestado=obtenerPresupuestoEjecucionPorArea($codOficina,$area,$globalNombreGestion,12)['presupuesto'];

//obtener PRESUPUESTO AREA OFICINA
//$ingresoPresupuestado=obtenerPresupuestoAreaGestion((int)$globalNombreGestion-1,$area);

  $codPlanServ=obtenerCodigoPlanServ();
  $dbh = new Conexion();
  $sqlInsert="INSERT INTO plantillas_servicios (codigo, nombre, abreviatura, cod_unidadorganizacional, cod_area,cod_cliente,productos,norma,cod_personal_registro,fecha_registro,dias_auditoria,cod_estadoplantilla,cod_estadoreferencial,utilidad_minima,cantidad_auditorias,ingreso_presupuestado,anios) 
  VALUES ('".$codPlanServ."','".$nombre."','".$abrev."', '".$unidad."', '".$area."','".$cliente."','".$productos."','".$norma."','".$globalUser."','".$fechaHoraActual."','".$dias."',1,1,'".$utmin."','".$cantidadAuditorias."','".$ingresoPresupuestado."','".$anios."')";
  $stmtInsert = $dbh->prepare($sqlInsert);
  $flagSuccess=$stmtInsert->execute();

  //enviar plantillas para la actualizacion de ibnorca
  if(isset($_POST['idPerfil'])){
   $usuarioPerfil=$_POST['idPerfil'];
  }else{
    $usuarioPerfil=$globalUser;
  }
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2706;
  $idObjeto=2710; //regristado
  $obs="Registro de Plantilla TCP/TCS";
  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$usuarioPerfil,$codPlanServ,$fechaHoraActual,$obs);

  //enviar plantillas para la actualizacion de ibnorca
  $fechaHoraActual=date("Y-m-d H:i:s");
  $idTipoObjeto=2706;
  $idObjeto=2711; //regristado
  $obs="Envio de Plantilla TCP/TCS";
  actualizarEstadosObjetosIbnorca($idTipoObjeto,$idObjeto,$usuarioPerfil,$codPlanServ,$fechaHoraActual,$obs);



if($area==39){
if($anios<=3){
  $sql1="SELECT * from configuraciones_servicios where numero_anio<=$anios";
  $stmt1 = $dbh->prepare($sql1);
  $stmt1->execute();
  while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
   $codigo=$rowServ['cod_claservicio'];
   $obs="";
   $cant=1;
   $monto=0;
   $codEstadoRef=1; 
    $sql="INSERT INTO plantillas_servicios_tiposervicio (cod_plantillaservicio, cod_claservicio,observaciones,cantidad,monto,cod_estadoreferencial) 
       VALUES ('".$codPlanServ."','".$codigo."','".$obs."','".$cant."','".$monto."', 1)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  }   
}else{
  $sql1="SELECT * from configuraciones_servicios where numero_anio<=3";
  $stmt1 = $dbh->prepare($sql1);
  $stmt1->execute();
  while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
   $codigo=$rowServ['cod_claservicio'];
   $obs="";
   $cant=1;
   $monto=0;
   $codEstadoRef=1; 
    $sql="INSERT INTO plantillas_servicios_tiposervicio (cod_plantillaservicio, cod_claservicio,observaciones,cantidad,monto,cod_estadoreferencial) 
       VALUES ('".$codPlanServ."','".$codigo."','".$obs."','".$cant."','".$monto."', 1)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  }
  for ($i=4; $i <= $anios; $i++) { 
    $sql1="SELECT * from configuraciones_servicios where numero_anio=4";
    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute();
   while ($rowServ = $stmt1->fetch(PDO::FETCH_ASSOC)) {
   $codigo=$rowServ['cod_claservicio'];
   $obs="";
   $cant=1;
   $monto=0;
   $codEstadoRef=1; 
    $sql="INSERT INTO plantillas_servicios_tiposervicio (cod_plantillaservicio, cod_claservicio,observaciones,cantidad,monto,cod_estadoreferencial) 
       VALUES ('".$codPlanServ."','".$codigo."','".$obs."','".$cant."','".$monto."', 1)";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
   }
  }
}
  
}

if($q==0){
  if($flagSuccess==true){
	  showAlertSuccessError(true,"../".$urlRegister."?cod=".$codPlanServ);	
  }else{
	  showAlertSuccessError(false,"../".$urlList);
  } 
}else{
  if($flagSuccess==true){
    showAlertSuccessError(true,"../".$urlRegister."?cod=".$codPlanServ."&q=".$q."&s=".$s."&u=".$u);  
  }else{
    showAlertSuccessError(false,"../".$urlList."&q=".$q."&s=".$s."&u=".$u);
  }
}
	
}

?>

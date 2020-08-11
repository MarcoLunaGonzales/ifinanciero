<?php
require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';
error_reporting(-1);
session_start();
$globalUser=$_SESSION["globalUser"];
$dbh = new Conexion();

//RECIBIMOS LAS VARIABLES
if(isset($_POST['cantidad_items'])){
  $filas=$_POST['cantidad_items'];
  $por_defecto=$_POST['por_defecto'];
  $oferta=$_POST['oferta'];
  $simulacion=$_POST['simulacion'];
  if($por_defecto==0){//hay registros de la oferta
    $stmt = $dbh->prepare("DELETE FROM simulaciones_servicios_ofertas_complementos where cod_simulacionoferta in (SELECT codigo from simulaciones_servicios_ofertas where cod_simulacionservicio=$simulacion and codigo=$oferta) ");
    $flagSuccess=$stmt->execute();
    $codOferta=$oferta;
  }else{
    $plantilla=obtenerPlantillaCodigoSimulacionServicio($simulacion);
    $area=obtenerCodigoAreaPlantillasServicios($plantilla);
    $nombreArea=nameArea($area);
    $codOferta=obtenerCodigoSimulacionServicioOferta();
    //insertar oferta edicion poner activo
    $stmt = $dbh->prepare("INSERT INTO simulaciones_servicios_ofertas(codigo,cod_simulacionservicio,activo,nombre,abreviatura,cod_area,cod_estadoreferencial)
    VALUES($codOferta,$simulacion,1,'OFERTA - $nombreArea','OP',$area,1)");
    $flagSuccess=$stmt->execute();
  }
  for ($i=1; $i <=$filas ; $i++) { 
      $tipo=$_POST['codigo'.$i];
      $orden=$_POST['orden'.$i];
      $orden=$_POST['orden'.$i];
      $descripcion=$_POST['descripcion'.$i];
      $codOfertaDetalle=obtenerCodigoSimulacionServicioOfertaDetalle();
      $stmt = $dbh->prepare("INSERT INTO simulaciones_servicios_ofertas_complementos(codigo,cod_simulacionoferta,descripcion,descripcion_alterna,habilitado_alterna,editable,cod_tipocomplemento,cod_estadoreferencial,orden)
       VALUES($codOfertaDetalle,$codOferta,'$descripcion','$descripcion',0,1,$tipo,1,$orden)");
      $stmt->execute();   
  } 
}

if(isset($_POST['url'])){
  $url=$_POST['url'];
  showAlertSuccessError($flagSuccess,"../".$urlList.$url);  
}else{
  showAlertSuccessError($flagSuccess,"../".$urlList);
}

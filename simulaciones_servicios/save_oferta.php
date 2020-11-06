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
$simulacion=$_POST['simulacion'];
//RECIBIMOS LAS VARIABLES
if(isset($_POST['cantidad_items'])){
  $filas=$_POST['cantidad_items'];
  $por_defecto=$_POST['por_defecto'];
  $oferta=$_POST['oferta'];
  
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
      $editable=1;
      if(!isset($_POST['editable'.$i])){
        $editable=0;
      }
      $codOfertaDetalle=obtenerCodigoSimulacionServicioOfertaDetalle();
      $stmt = $dbh->prepare("INSERT INTO simulaciones_servicios_ofertas_complementos(codigo,cod_simulacionoferta,descripcion,descripcion_alterna,habilitado_alterna,editable,cod_tipocomplemento,cod_estadoreferencial,orden)
       VALUES($codOfertaDetalle,$codOferta,'$descripcion','$descripcion',0,$editable,$tipo,1,$orden)");
      $stmt->execute();   
  } 
}

if($_POST['descargar']==0){  
  if(isset($_POST['url'])){
   $url=$_POST['url'];
   showAlertSuccessError($flagSuccess,"../".$urlList.$url);  
  }else{
   showAlertSuccessError($flagSuccess,"../".$urlList);
 }
}else{
  $codAreaX=obtenerCodigoAreaPlantillasServicios(obtenerPlantillaCodigoSimulacionServicio($simulacion));
  $urlDescargar=$urlImpOferta."?cod=".$simulacion."&cod_area=".$codAreaX."&md=".$_POST['descargar'];
  $nombreOferta=nameSimulacionServicio($simulacion)." - ".abrevArea_solo($codAreaX);
  $tituloAlert="VER EN NAVEGADOR (Espere...)";
  if($_POST['descargar']==1){
    $tituloAlert="DESCARGAR";
  }
  ?>
  <script>
swal({
  title: '<?=$nombreOferta?>',
  text: 'DOCUMENTO PDF - <?=$tituloAlert?>',
  showCancelButton: false,
  showConfirmButton: false
}).then(
  function () {},
  // handling the promise rejection
  function (dismiss) {
    if (dismiss === 'timer') {
      //console.log('I was closed by the timer')
    }
  }
);
window.location.href='../<?=$urlDescargar?>';</script>
  <?php
}


<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../assets/libraries/CifrasEnLetras.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
set_time_limit(300);
$fechaActual=date("Y-m-d");

$fecha=$_POST['fecha_desde'];
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$fechaHasta=$_POST['fecha_hasta'];
$fechaTituloHasta= explode("-",$fechaHasta);
$fechaFormateadaHasta=$fechaTituloHasta[2].'/'.$fechaTituloHasta[1].'/'.$fechaTituloHasta[0];

$fecha_fac=$_POST['fecha_desde_fac'];
$fechaTituloFac= explode("-",$fecha_fac);
$fechaFormateadaFac=$fechaTituloFac[2].'/'.$fechaTituloFac[1].'/'.$fechaTituloFac[0];

$fechaHasta_fac=$_POST['fecha_hasta_fac'];
$fechaTituloHastaFac= explode("-",$fechaHasta_fac);
$fechaFormateadaHastaFac=$fechaTituloHastaFac[2].'/'.$fechaTituloHastaFac[1].'/'.$fechaTituloHastaFac[0];

$moneda=1; //$_POST["moneda"];
$entidades=$_POST['libretas'];
$StringEntidadCodigos=implode(",", $entidades);

$stringEntidades="";
foreach ($entidades as $valor ) {    
    $stringEntidades.=nameLibretas($valor).",";
}  

$periodoTitle= "Del ".$fechaFormateada.' al '.$fechaFormateadaHasta; 
$periodoTitleFac= "Del ".$fechaFormateadaFac.' al '.$fechaFormateadaHastaFac;  

$filtro=$_POST['filtro'];

/*if($filtro==1){
  $sqlFiltro="and (ce.cod_factura IS NOT NULL or ce.cod_factura!=0)";
}else{
  if($filtro==2){
    $sqlFiltro="";
  }
}*/
$sqlFiltro="";
$sqlFiltro2="and fecha_factura BETWEEN '$fecha_fac 00:00:00' and '$fechaHasta_fac 23:59:59'";
$sqlFiltroComp="and c.fecha BETWEEN '$fecha_fac 00:00:00' and '$fechaHasta_fac 23:59:59'";
// if($filtro==1){
//   $sqlFiltro="and (ce.cod_factura IS NOT NULL or ce.cod_factura!=0)";
// }
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">

                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>           
                   <h4 class="card-title text-center"><?=obtenerValorConfiguracion(57)?></h4>
                   <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como</h6></div>-->
                </div>
                <?php
                include "reporteLibretasBancariasDetalle.php";
                ?>
              </div>
            </div>
          </div>  
        </div>
    </div>

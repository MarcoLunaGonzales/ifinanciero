<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';
require_once '../assets/libraries/CifrasEnLetras.php';

setlocale(LC_TIME, "Spanish");

$dbh = new Conexion();
set_time_limit(0);
$fechaActual=date("Y-m-d");

$gestion=nameGestion($_POST['gestion']);
$mes=$_POST['cod_mes_x'];
if($mes<10){
  $mes="0".$mes;
}
$mesConta=strtoupper(nameMes($mes));
$dia=date("d",(mktime(0,0,0,$mes+1,1,$gestion)-1));

$fecha=$gestion."-".$mes."-01";
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$fechaHasta=$gestion."-".$mes."-".$dia;
$fechaTituloHasta= explode("-",$fechaHasta);
$fechaFormateadaHasta=$fechaTituloHasta[2].'/'.$fechaTituloHasta[1].'/'.$fechaTituloHasta[0];

$moneda=1; //$_POST["moneda"];
$entidades=$_POST['libretas'];
$cod_gestion_x=$_POST['gestion'];
$cod_mes_x=$_POST['cod_mes_x'];


$StringEntidadCodigos=($entidades);

$stringEntidades="";
$periodoTitle= "Del ".$fechaFormateada.' al '.$fechaFormateadaHasta; 
$stringEntidades.=nameLibretas($entidades);

$filtro=3;
$sqlFiltro="";
if($filtro==1){
  $sqlFiltro="and (ce.cod_factura IS NOT NULL or ce.cod_factura!=0)";
}else{
  if($filtro==2){
    $sqlFiltro="and (ce.cod_factura IS NULL or ce.cod_factura=0)";
  }
}
?>
<div class="content">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
              <form id="form1" class="form-horizontal" action="<?=$urlSaveContabilizacion;?>" method="post">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">

                  <div class="card-icon bg-blanco">
                    <img class="" width="40" height="40" src="../assets/img/logoibnorca.png">
                  </div>           
                   <h4 class="card-title text-center">DEPOSITOS NO FACTURADOS</h4>
                   <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como</h6></div>-->
                </div>
                <?php
                include "contabilizacionLibretasBancariasDetalle.php";
                ?>

              </div>
              <div class="card-footer fixed-bottom">
                  <button type="submit" class="btn btn-primary">GENERAR COMPROBANTE</button>
                  <a href="../<?=$urlList3?>" class="btn btn-danger">VOLVER</a>  
              </div>
            </form>
            </div>
          </div>  
        </div>
    </div>

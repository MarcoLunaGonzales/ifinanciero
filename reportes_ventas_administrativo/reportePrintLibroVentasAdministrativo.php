<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';
require_once '../assets/libraries/CifrasEnLetras.php';

$dbh = new Conexion();
// Preparamos
$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("Y-m-d");
$desdeInicioAnio="";
if($_POST["fecha_desde"]==""){
  $y=$globalNombreGestion;
  $desde=$y."-01-01";
  $hasta=$y."-12-31";
  $desdeInicioAnio=$y."-01-01";
}else{
  $porcionesFechaDesde = explode("-", $_POST["fecha_desde"]);
  $porcionesFechaHasta = explode("-", $_POST["fecha_hasta"]);

  $desdeInicioAnio=$porcionesFechaDesde[0]."-01-01";
  $desde=$porcionesFechaDesde[0]."-".$porcionesFechaDesde[1]."-".$porcionesFechaDesde[2];
  $hasta=$porcionesFechaHasta[0]."-".$porcionesFechaHasta[1]."-".$porcionesFechaHasta[2];
}

$unidadCosto=$_POST['unidad_costo'];
$areaCosto=$_POST['area_costo'];

$gestion= $_POST["gestion"];
$filtroPersonal= implode(",",$_POST["personal"]);
$formas_pago= $_POST["forma_pago"];
$filtroTiposPago= implode(",",$formas_pago);

//PONEMOS LAS VARIABLES PARA CUANDO LLAMEMOS AL REPORTE DESDE LOS MAYORES
if($gestion==null){
  $gestion=$globalGestion;
  $unidadCosto=explode(",",obtenerUnidadesReport(0));
  $areaCosto=explode(",",obtenerAreasReport(0));
}
$NombreGestion = nameGestion($gestion);
$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);


$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);

// $formas_pago_titulo="";
// foreach ($formas_pago as $valor ) {    
//     $formas_pago_titulo.=nameTipoPagoSolFac($valor).", ";
// }


$nombreCuentaTitle="";

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));


if (isset($_POST["check_formato2"])) {
  $check_formato2=$_POST["check_formato2"]; 
  if($check_formato2){    
    $sw_check="1";
  }else{
    $sw_check="0";
  }
}else{
  $sw_check="0";
}

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
                   <!--<div class="float-right col-sm-2"><h6 class="card-title">Exportar como:</h6></div>-->
                   <h4 class="card-title text-center">Detalle de Recaudaciones</h4>
                </div>
                
                <?php
                  include "reportePrintLibroVentasAdministrativoDetalle.php";
                ?>
              </div>
            </div>
          </div>  
        </div>
    </div>

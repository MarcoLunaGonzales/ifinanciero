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
$gestion=nameGestion($_POST['gestion']);
$fecha=$_POST['fecha_desde'];
$fechaTitulo= explode("-",$fecha);
$fechaFormateada=$fechaTitulo[2].'/'.$fechaTitulo[1].'/'.$fechaTitulo[0];

$fechaHasta=$_POST['fecha_hasta'];
$fechaTituloHasta= explode("-",$fechaHasta);
$fechaFormateadaHasta=$fechaTituloHasta[2].'/'.$fechaTituloHasta[1].'/'.$fechaTituloHasta[0];



$moneda=1; //$_POST["moneda"];
$entidades=$_POST['bancos'];
$StringEntidadCodigos=implode(",", $entidades);

$stringEntidades="";
foreach ($entidades as $valor ) {    
    $stringEntidades.=nameBancos($valor).",";
}  

$periodoTitle= "Del ".$fechaFormateada.' al '.$fechaFormateadaHasta;  
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
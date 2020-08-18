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
// $globalNombreGestion=$_SESSION["globalNombreGestion"];
// $globalUser=$_SESSION["globalUser"];
// $globalGestion=$_SESSION["globalGestion"];
// $globalUnidad=$_SESSION["globalUnidad"];
// $globalNombreUnidad=$_SESSION['globalNombreUnidad'];
// $globalArea=$_SESSION["globalArea"];
// $globalAdmin=$_SESSION["globalAdmin"];

// $fechaActual=date("Y-m-d");
// $desdeInicioAnio="";
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

// $nombreCuentaTitle="";

 $periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));


$dbh = new Conexion();
$sql="SELECT ld.codigo,ld.descripcion,ld.informacion_complementaria,ld.monto from libretas_bancariasdetalle ld where ld.cod_estadoreferencial= 1 and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59'";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();
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
             <h4 class="card-title text-center">Reportes Vs Estado Resultado</h4>
          </div>
          <div class="card-body">
            <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
            <!-- <h6 class="card-title">Areas: <?=$areaAbrev;?></h6> -->
            <!-- <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6> -->
            <div class="table-responsive">
            <?php
            $html='<table class="table table-bordered table-condensed" width="100%" align="center">'.
                '<thead >'.
                '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
                '<th >Ingresos</th>'.
                  '<th >Monto</th>'.
                  '<th >Area</th>'.
                  '<th >Importe Neto</th>'.                  
                '</tr>'.
               '</thead>'.
               '<tbody>';
                while ($rowComp = $listaDetalleLibretas->fetch(PDO::FETCH_ASSOC)) {
                  $cod_libretabancariadetalle=$rowComp['codigo'];
                  $monto_libreta=$rowComp['monto'];
                  $descripcion_libreta=$rowComp['informacion_complementaria'];                  

                    $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$cod_libretabancariadetalle.'</td>'.
                      '<td class="text-left font-weight-bold">'.$descripcion_libreta.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($monto_libreta).' </td>'.     
                      '<td class="text-right font-weight-bold">'.formatNumberDec($saldo_libreta).' </td>'.     
                      '<td class="text-left font-weight-bold">'.$array_facturas.'</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($monto_factura).' </td>'.     
                  '</tr>';
                  
                }                          
                $totalFactura=obtener_saldo_total_facturas();
                $html.='<tr class="bg-secondary text-white">'.
                    '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">Total Libreta</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalLibreta).' </td>'.     
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalSaldo).' </td>'.     
                      '<td class="text-left font-weight-bold">Total Factura</td>'.
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalFactura).' </td>'.     
                '</tr>';
            $html.=    '</tbody></table>';
            echo $html;
            ?>
            </div>
          </div>
        </div>
      </div>
    </div>  
  </div>
</div>

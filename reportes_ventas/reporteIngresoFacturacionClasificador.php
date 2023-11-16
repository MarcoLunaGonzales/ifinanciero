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
// $formas_pago= $_POST["forma_pago"];

//PONEMOS LAS VARIABLES PARA CUANDO LLAMEMOS AL REPORTE DESDE LOS MAYORES
if($gestion==null){
  $gestion=$globalGestion;
  $unidadCosto=explode(",",obtenerUnidadesReport(0));
  $areaCosto=explode(",",obtenerAreasReport(0));
}
$NombreGestion = nameGestion($gestion);
$unidadCostoArray=implode(",", $unidadCosto);
$areaCostoArray=implode(",", $areaCosto);
// $forma_pagoArray=implode(",", $formas_pago);
if(isset($_POST['solo_tienda'])){
 $solo_tienda=1; 
}else{
  $solo_tienda=0;
}
if(isset($_POST['solo_credito'])){
 $solo_credito=1;
}else{
  $solo_credito=0;
}

$unidadAbrev=abrevUnidad($unidadCostoArray);
$areaAbrev=abrevArea($areaCostoArray);
// $formas_pago_titulo="";
// foreach ($formas_pago as $valor ) {    
//     $formas_pago_titulo.=nameTipoPagoSolFac($valor).", ";
// }
$nombreCuentaTitle="";

$periodoTitle=" Del ".strftime('%d/%m/%Y',strtotime($desde))." al ".strftime('%d/%m/%Y',strtotime($hasta));


// Lista de Costos
$valorIVA=100-(obtenerValorConfiguracion(1));
$queryCredito="";
if($soloCredito==1){
  $queryCredito=" and f.cod_tipopago=217";
}

$sql = "SELECT
            da.cod_area,
            (SELECT a.abreviatura FROM areas a WHERE a.codigo = da.cod_area) AS area,
            SUM(((fd.cantidad * fd.precio) - fd.descuento_bob) * (da.porcentaje / 100) * ($valorIVA / 100)) AS importe_real, fd.cod_claservicio, CASE
                WHEN f.cod_solicitudfacturacion = -100 THEN 'TIENDA'
                ELSE 'SF'
            END AS origen,
            cs.Descripcion as servicio
        FROM
            facturas_venta f
            INNER JOIN facturas_ventadetalle fd ON f.codigo = fd.cod_facturaventa
            INNER JOIN facturas_venta_distribucion da ON f.codigo = da.cod_factura
            LEFT JOIN cla_servicios cs ON cs.IdClaServicio = fd.cod_claservicio
        WHERE
            f.fecha_factura BETWEEN '$desde 00:00:00' AND '$hasta 23:59:59'
            AND f.cod_estadofactura <> 2
            AND f.cod_unidadorganizacional IN ($unidadCostoArray)
            AND da.cod_area IN ($areaCostoArray)
        GROUP BY area, cod_claservicio, origen
        ORDER BY area";
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
                   <h4 class="card-title text-center">Reporte de Ingresos por Clasificador</h4>
                </div>

                <div class="card-body">
                  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
                  <h6 class="card-title">Areas: <?=$areaAbrev;?></h6>
                  <h6 class="card-title">Oficinas:<?=$unidadAbrev?></h6>
                  <!-- <h6 class="card-title">Formas Pago:<?=$formas_pago_titulo?></h6>   -->
                  <div class="table-responsive">

                    <table class="table table-bordered table-condensed" id="libro_mayor_rep" width="50%" align="center">
                      <thead class="bg-secondary text-white even">
                        <th>Area</th>
                        <th>Código</th>
                        <th>Servicio</th>
                        <th>Origen</th>
                        <th>Importe Neto</th>
                      </thead>
                      <tbody>
                        <?php
                          $totalImporte = 0;
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $totalImporte+=$row['importe_real'];
                        ?>
                          <tr role="row" class="odd">
                              <td class="text-left font-weight-bold"><?= $row['area']; ?></td>
                              <td class="text-left font-weight-bold"><?= $row['cod_claservicio']; ?></td>
                              <td class="text-left font-weight-bold"><?= $row['servicio']; ?></td>
                              <td class="text-left font-weight-bold"><?= $row['origen']; ?></td>
                              <td class="text-right font-weight-bold"><?= formatNumberDec($row['importe_real']); ?></td>
                          </tr>
                        <?php
                          }
                        ?>
                          <tr class="bg-secondary text-white even" role="row">
                              <td colspan="4" class="text-center">Importe Total</td>
                              <td class="text-right font-weight-bold small"><?= formatNumberDec($totalImporte); ?></td>
                          </tr>
                      </tbody>
                    </table>  
                  </div>

                </div>
              
              </div>
            </div>
          </div>  
        </div>
    </div>

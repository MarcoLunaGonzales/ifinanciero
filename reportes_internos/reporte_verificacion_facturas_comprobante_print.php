<?php
session_start();
set_time_limit(0);
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../functionsReportes.php';


$dbh = new Conexion();

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
$sql="SELECT cd.codigo,cd.cod_comprobante,cd.cod_cuenta,cd.debe,sum(cd.haber) as haber,cd.glosa,c.numero 
FROM comprobantes c,comprobantes_detalle cd,areas a
WHERE c.codigo =cd.cod_comprobante and cd.cod_cuenta=a.cod_cuenta_ingreso and a.cod_cuenta_ingreso is not null and c.cod_tipocomprobante=4 and c.fecha  BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and c.cod_estadocomprobante<>2 GROUP BY cod_comprobante order by c.fecha,c.numero";
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
            <div class="table-responsive">
            <?php
            $html='<table class="table table-bordered table-condensed" width="100%" align="center" id="libro_mayor_rep">'.
                '<thead >'.
                '<tr class="text-center" style="background:#40A3A8;color:#ffffff;">'.
                '<th >Cod Detalle</th>'.
                  '<th >Cod Comprobante</th>'.                  
                  '<th >Numero</th>'.
                  '<th >Cuenta</th>'.
                  '<th >Haber</th>'.
                  '<th >Glosa</th>'.
                  '<th >Monto <br>Factura</th>'.
                  '<th >Diferencia</th>'.
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $totalimportehaber=0;
              $totalimportefactura=0;
              $totalimportediferencia=0;
                while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $codigo=$rowComp['codigo'];
                  $cod_comprobante=$rowComp['cod_comprobante'];
                  $numero=$rowComp['numero'];
                  $cuenta=nameCuenta($rowComp['cod_cuenta']);
                  $haber=$rowComp['haber'];                  
                  $glosa=$rowComp['glosa'];
                  $sql="SELECT SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*(87/100))as importe_real 
                  FROM facturas_venta f, facturas_ventadetalle fd, facturas_venta_distribucion da 
                  WHERE da.cod_factura=f.codigo and f.codigo=fd.cod_facturaventa and fd.cod_facturaventa=da.cod_factura and 
                  f.cod_comprobante= $cod_comprobante and f.cod_estadofactura<>2";
                  $stmt5 = $dbh->prepare($sql);
                  $stmt5->execute();                      
                  $stmt5->bindColumn('importe_real', $importe_realX);                  
                  $monto_factura=0;
                  while ($row = $stmt5->fetch(PDO::FETCH_BOUND)) {
                    $monto_factura=$importe_realX;
                  }
                  $monto_diferencia=$haber-$monto_factura;

                  $monto_diferencia=round($monto_diferencia,2);

                  $totalimportehaber+=$haber;
                  $totalimportefactura+=$monto_factura;
                  $totalimportediferencia+=$monto_diferencia;
                  $label="";
                  if($monto_diferencia!=0){
                    $label="style='background-color:#ff0000;'";
                  }
                    $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$codigo.'</td>'.
                      '<td class="text-left font-weight-bold">'.$cod_comprobante.'</td>'.
                      '<td class="text-left font-weight-bold">'.$numero.'</td>'.
                      '<td class="text-left font-weight-bold">'.$cuenta.'</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($haber).'</td>'.
                      '<td class="text-left font-weight-bold">'.$glosa.'</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($monto_factura).'</td>'.
                      '<td class="text-left font-weight-bold"><span '.$label.' >'.formatNumberDec($monto_diferencia).'</span></td>'.
                      '</tr>';                  
                }                          
                // $totalFactura=obtener_saldo_total_facturas();
                $html.='<tr>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">TOTAL</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportehaber).'</td>'.
                      '<td class="text-left font-weight-bold">-</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportefactura).'</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($totalimportediferencia).'</td>'.
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

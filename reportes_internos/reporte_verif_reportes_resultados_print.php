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
$sql="SELECT da.cod_area, (SELECT a.abreviatura from areas a where a.codigo=da.cod_area)area, SUM(((fd.cantidad*fd.precio)-fd.descuento_bob)*(da.porcentaje/100)*(87/100))as importe_real FROM facturas_venta f, facturas_ventadetalle fd, facturas_venta_distribucion da WHERE da.cod_factura=f.codigo and f.codigo=fd.cod_facturaventa and fd.cod_facturaventa=da.cod_factura and f.fecha_factura BETWEEN '$desde 00:00:00' and '$hasta 23:59:59' and f.cod_estadofactura<>2 group by area order by area";
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
                '<th >Area</th>'.
                  '<th >importe</th>'.                  
                '</tr>'.
               '</thead>'.
               '<tbody>';
              $totalimporteArea=0;
                while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $area=$rowComp['area'];
                  $importe_real=$rowComp['importe_real'];
                  $totalimporteArea+=$importe_real;
                    $html.='<tr>'.
                      '<td class="text-left font-weight-bold">'.$area.'</td>'.
                      '<td class="text-left font-weight-bold">'.formatNumberDec($importe_real).'</td>'.                      
                  '</tr>';
                  
                }                          
                // $totalFactura=obtener_saldo_total_facturas();
                $html.='<tr class="bg-secondary text-white">'.
                      '<td class="text-left font-weight-bold">TOTAL Reportes Ventas</td>'.                      
                      '<td class="text-right font-weight-bold">'.formatNumberDec($totalimporteArea).' </td>'.                           
                '</tr>';

            $stmt4 = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.cod_padre, p.nivel, 
                                (select tc.nombre from tipos_cuenta tc where tc.codigo=p.cod_tipocuenta)cod_tipocuenta, p.cuenta_auxiliar FROM plan_cuentas p where cod_estadoreferencial=1 and p.nivel=4 and p.cod_padre='269' order by p.numero");
            $stmt4->execute();                      
            $stmt4->bindColumn('codigo', $codigo_4);
            $stmt4->bindColumn('numero', $numero_4);
            $stmt4->bindColumn('nombre', $nombre_4);
            $stmt4->bindColumn('cod_padre', $codPadre_4);
            $stmt4->bindColumn('nivel', $nivel_4);
            $stmt4->bindColumn('cod_tipocuenta', $codTipoCuenta_4);
            $stmt4->bindColumn('cuenta_auxiliar', $cuentaAuxiliar_4);
            $index_4=1;
            $total_estado_resultados=0;
            while ($row = $stmt4->fetch(PDO::FETCH_BOUND)) {
              $stmt5 = $dbh->prepare("SELECT cuentas_monto.*,p.nombre,p.numero,p.nivel,p.cod_padre from plan_cuentas p join 
             (select d.cod_cuenta,sum(debe) as total_debe,sum(haber) as total_haber 
              from comprobantes_detalle d join comprobantes c on c.codigo=d.cod_comprobante 
              join areas a on a.codigo=d.cod_area
              join unidades_organizacionales u on u.codigo=d.cod_unidadorganizacional
              join plan_cuentas p on p.codigo=d.cod_cuenta
              where c.fecha between '$desde 00:00:00' and '$hasta 23:59:59'  and c.cod_estadocomprobante<>'2' group by (d.cod_cuenta) order by d.cod_cuenta) cuentas_monto
              on p.codigo=cuentas_monto.cod_cuenta where p.cod_padre=$codigo_4 order by p.numero");
              $stmt5->execute();                      
              $stmt5->bindColumn('nombre', $nombreX);
              $stmt5->bindColumn('total_debe', $total_debe_5);
              $stmt5->bindColumn('total_haber', $total_haber_5);
              $index_4=1;
              while ($row = $stmt5->fetch(PDO::FETCH_BOUND)) {
                $montoX=(float)($total_debe_5-$total_haber_5);
                $montoX=abs($montoX);
                $total_estado_resultados+=$montoX;
                   //ACA VOLVEMOS TODO POSITIVO PARA LA RESTA FINAL
                   
                    if($montoX>0){
                      $html.='<tr>'.                           
                           '<td class="td-border-none text-left">'.$nombreX.'</td>'.                           
                           '<td class="td-border-none text-left">'.number_format($montoX, 2, '.', ',').'</td>';   
                      $html.='</tr>';      
                    }elseif($montoX<0){
                      $html.='<tr>'.                           
                           '<td class="td-border-none text-left">'.$nombreX.'</td>'.
                           '<td class="td-border-none text-left">('.number_format(abs($montoX), 2, '.', ',').')</td>';   
                      $html.='</tr>';      
                    }elseif($montoX==0){
                      $html.='<tr>'.                           
                           '<td class="td-border-none text-left">'.$nombreX.'</td>'.                           
                           '<td class="td-border-none text-left">-</td>';   
                     $html.='</tr>';      
                    }

              }
            }
            $html.='<tr class="bg-secondary text-white">'.
                      '<td class="text-left font-weight-bold">TOTAL Estado Resultados</td>'.                      
                      '<td class="text-right font-weight-bold">'.formatNumberDec($total_estado_resultados).' </td>'.                           
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

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


$codigoLibreta=8;

$nombreLibretasBancaria=nameLibretas($codigoLibreta);

$cuentaLibreta=cuentaLibreta($codigoLibreta);

$cuentaPasiva=contraCuentaLibreta($codigoLibreta);

$dbh = new Conexion();

$sql="SELECT ld.codigo, ld.fecha_hora, fv.fecha_factura, cd.cod_comprobante, cd.codigo as codCompDetalle, fv.codigo as codFactura, ld.*, fv.* from libretas_bancariasdetalle ld, facturas_venta fv, comprobantes_detalle cd, libretas_bancariasdetalle_facturas ldf
where fv.cod_comprobante=cd.cod_comprobante and ld.codigo=ldf.cod_libretabancariadetalle and ldf.cod_facturaventa=fv.codigo and 
(concat(year(fv.fecha_factura),'-',month(fv.fecha_factura))=concat(year(ld.fecha_hora),'-',month(ld.fecha_hora)))
and ld.cod_libretabancaria=$codigoLibreta 
and fv.fecha_factura BETWEEN '2021-01-01 00:00:00' and '2021-12-31 23:59:59' and fv.cod_estadofactura!=2 
and cd.cod_cuenta=$cuentaPasiva order by cd.cod_comprobante";
// echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();

?>

    <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
      <thead>
        <tr style="background:#21618C; color:#fff;">
          <td>CodigoLib</td>
          <td>FechaLib</td>
          <td>FechaFac</td>
          <td>CodComprobante</td>
          <td>Info</td>
          <td>Monto</td>
          <td>CodFactura</td>
          <td>Detalle</td>
        </tr>
      </thead>
      <body>
        <?php
          while ($rowComp = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $codigo=$rowComp['codigo']; 
            $fecha=$rowComp['fecha_hora'];
            $fechaFac=$rowComp['fecha_factura'];
            $informacion_complementaria=$rowComp['informacion_complementaria'];
            $codComprobante=$rowComp['cod_comprobante'];
            $monto=$rowComp['monto'];
            $codFactura=$rowComp['codFactura'];
            

            $sqlDetalle="SELECT pc.nombre, cd.haber, cd.debe from comprobantes_detalle cd, plan_cuentas pc where 
              cd.cod_cuenta=pc.codigo and cd.cod_comprobante=$codComprobante and cd.cod_cuenta in ($cuentaLibreta,$cuentaPasiva) order by pc.codigo;";
            $stmtDetalle = $dbh->prepare($sqlDetalle);
            $stmtDetalle->execute();
            $txtDetalle="<table border=1>";
            while ($rowDetalle = $stmtDetalle -> fetch(PDO::FETCH_ASSOC)){
                $txtDetalle.="<tr><td>".$rowDetalle['nombre']."</td><td>".$rowDetalle['debe']."</td><td>".$rowDetalle['haber']."</td></tr>";
            }
            $txtDetalle.="</table>";

            ?>
            <tr class="">
              <td><?=$codigo;?></td>
              <td><?=$fecha;?></td>
              <td><?=$fechaFac;?></td>
              <td><?=$codComprobante;?></td>
              <td class="text-left">
                <?=$informacion_complementaria?>
              </td>      
              <td class="text-right"><?=number_format($monto,2,".",",")?></td>
              <td><?=$codFactura;?></td>
              <td><?=$txtDetalle;?></td>
              </tr>
              <?php 
          }                          
            ?>

          </table>
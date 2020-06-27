<script> periodo_mayor='<?=$periodoTitle?>';
          cuenta_mayor='<?=trim($nombreCuentaTitle)?>';
          unidad_mayor='<?=$unidadGeneral?>';
 </script>
<style>
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
</style>
<div class="card-body">
  <h6 class="card-title">Periodo Libretas: <?=$periodoTitle?></h6>
  <h6 class="card-title">Periodo Facturas: <?=$periodoTitleFac?></h6>
  <h6 class="card-title">Libretas Bancarias: <?=$stringEntidades;?></h6>
  <div class="table-responsive col-sm-12">
    <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
      <thead>
        <tr style="background:#21618C; color:#fff;">
          <td>Fecha</td>
          <td>Hora</td>
          <td width="35%">Descripción</td>
          <!--<td>Información C.</td>-->
          <td>Sucursal</td>
          <td>Monto</td>
          <td width="10%">Nro Doc / Nro Ref</td>
          <!--<td width="10%"><a href="#" id="minus_tabla_lib" title="Abrir/Cerrar Facturas" class="text-white float-right"><i class="material-icons">switch_left</i></a>Estado</td>-->
          <td class="bg-success">Fecha</td>
          <td class="bg-success">Numero</td>
          <td class="bg-success">NIT</td>
          <td class="bg-success">Razon Social</td>
          <td class="bg-success">Detalle</td>
          <td class="bg-success">Monto</td>
        </tr>
      </thead> 

     <?php
    $html='<tbody>';

// Preparamos
    $sqlDetalle="(SELECT * FROM (SELECT ce.*,(SELECT fecha_factura from facturas_venta where codigo=ce.cod_factura) as fecha_fac
FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria where lb.codigo in ($StringEntidadCodigos) and ce.fecha_hora BETWEEN '$fecha 00:00:00' and '$fechaHasta 23:59:59' and  ce.cod_estadoreferencial=1 $sqlFiltro order by ce.codigo) lbd where lbd.fecha_fac BETWEEN '$fecha_fac 00:00:00' and '$fechaHasta_fac 23:59:59')
 UNION (SELECT ce.*,(SELECT fecha_factura from facturas_venta where codigo=ce.cod_factura) as fecha_fac
FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria where lb.codigo in ($StringEntidadCodigos) and ce.fecha_hora BETWEEN '$fecha 00:00:00' and '$fechaHasta 23:59:59' and  ce.cod_estadoreferencial=1 $sqlFiltro order by ce.codigo)";
$stmt = $dbh->prepare($sqlDetalle);
//echo $sqlDetalle;

// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('codigo', $codigo);
$stmt->bindColumn('descripcion', $descripcion);
$stmt->bindColumn('informacion_complementaria', $informacion_complementaria);
$stmt->bindColumn('agencia', $agencia);
$stmt->bindColumn('nro_cheque', $nro_cheque);
$stmt->bindColumn('nro_documento', $nro_documento);
$stmt->bindColumn('fecha_hora', $fecha);
$stmt->bindColumn('monto', $monto);
$stmt->bindColumn('cod_factura', $codFactura);

            $index=1;$totalMonto=0;$totalMontoFac=0;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          if($codFactura==""||$codFactura==0){
                            $tituloEstado="Sin Factura";
                            $facturaFecha="";
                            $facturaNumero="";
                            $facturaNit="";
                            $facturaRazonSocial="";
                            $facturaDetalle="";
                            $facturaMonto="";
                            $totalMontoFac+=0;
                          }else{
                            $tituloEstado="Con Factura";
                            $datos=obtenerDatosFacturaVenta($codFactura);
                            $facturaFecha=strftime('%d/%m/%Y',strtotime($datos[0]));
                            $facturaNumero=$datos[1];
                            $facturaNit=$datos[2];
                            $facturaRazonSocial=$datos[3];
                            $facturaDetalle=$datos[4];
                            $facturaMonto=number_format($datos[5],2,".",",");
                            $totalMontoFac+=$datos[5];
                          }
                          $totalMonto+=$monto;


                           
?>
                        <tr>
                          <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
                          <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                          <td class="text-left">
                            <?php 
                            if($codFactura==""||$codFactura==0){
                              ?><?=$descripcion?> info: <?=$informacion_complementaria?><?php
                            }else{
                              ?><?=$descripcion?> info: <?=$informacion_complementaria?><?php
                            ?>
                           <!--<div id="accordion<?=$index;?>" role="tablist">
                              <div class="card-collapse">
                                <div class="card-header" role="tab" id="heading<?=$index;?>">
                                  <p class="mb-0">
                                    <small>
                                       <a data-toggle="collapse" href="#collapse<?=$index;?>" aria-expanded="false" aria-controls="collapse<?=$index;?>" class="collapsed">
                                          <?=$descripcion?> info: <?=$informacion_complementaria?>
                                          <i class="material-icons">keyboard_arrow_down</i>
                                       </a>
                                    </small>
                                  </p>
                                </div>
                                <div id="collapse<?=$index;?>" class="collapse" role="tabpanel" aria-labelledby="heading<?=$index;?>" data-parent="#accordion<?=$index;?>" style="">
                                  <div class="card-body">
                                    <?php
                                          $sqlDetalleX="SELECT * FROM facturas_venta where cod_libretabancariadetalle=$codigo";                                   
                                          $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                                          $stmtDetalleX->execute();

                                          $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                                          $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                                          $stmtDetalleX->bindColumn('nit', $nitDetalle);
                                          $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                                          $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                                          $stmtDetalleX->bindColumn('importe', $impDetalle);

                                     ?>
                                      <table width="100%">
                                          <tr class="bg-success text-white">
                                            <th>Fecha</th>
                                            <th>Número</th>
                                            <th>Nit</th>
                                            <th>Razón Social</th>
                                            <th>Detalle</th>
                                            <th>Monto</th>
                                            </tr>
                                       <?php
                                        while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                                        ?>
                                         <tr>
                                             <td class="text-center small"><?=$fechaDetalle;?></td>
                                             <td class="text-left small"><?=$nroDetalle;?></td>
                                             <td class="text-left small"><?=$nitDetalle;?></td>
                                             <td class="text-left small"><?=$rsDetalle;?></td>
                                             <td class="text-left small"><?=$obsDetalle;?></td>
                                             <td class="text-left small"><?=number_format($impDetalle,2,".",",");?></td>
                                         </tr>
                                          <?php    
                                          }
                                          ?>
                                      </table>
                                   </div>
                                 </div>
                               </div>
                             </div>-->
                             <?php } ?>
                          </td>      
                          <td class="text-left"><?=$agencia?></td>
                          <td class="text-right"><?=number_format($monto,2,".",",")?></td>
                          <td class="text-right"><?=$nro_documento?></td>
                          <!--<td class="text-right font-weight-bold"><?=$tituloEstado?></td>-->
                          <td class="text-right font-weight-bold"><?=$facturaFecha?></td>
                          <td class="text-right font-weight-bold"><?=$facturaNumero?></td>
                          <td class="text-right font-weight-bold"><?=$facturaNit?></td>
                          <td class="text-right font-weight-bold"><?=$facturaRazonSocial?></td>
                          <td class="text-right font-weight-bold"><?=$facturaDetalle?></td>
                          <td class="text-right font-weight-bold"><?=$facturaMonto?></td>
                      
<?php
              $index++;
            }
            ?>
                        <tr class="font-weight-bold" style="background:#21618C; color:#fff;">
                          <td align="center" colspan="4" class="csp">Totales</td>
                          <td class="text-right"><?=number_format($totalMonto,2,".",",")?></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                          <td class="text-left"><?=number_format($totalMontoFac,2,".",",")?></td>
                        </tr>
<?php
    $html.=    '</tbody>';

    echo $html;

    ?>
        <tfoot>
            <tr style="background:#21618C; color:#fff;">
                <th>Fecha</th>
                <th>Hora</th>
                <th>Descripcion</th>
                <th>Sucursal</th>
                <th>Monto</th>
                <th>Nro Documento</th>
                <th class="bg-success">Fecha</th>
                <th class="bg-success">Numero</th>
                <th class="bg-success">NIT</th>
                <th class="bg-success">Razon Social</th>
                <th class="bg-success">Detalle</th>
                <th class="bg-success">Monto</th>
            </tr>
        </tfoot>
      </table>  
  </div>
</div>
            
<style>
.dataTables_filter{
  display: none !important;
}
</style>              
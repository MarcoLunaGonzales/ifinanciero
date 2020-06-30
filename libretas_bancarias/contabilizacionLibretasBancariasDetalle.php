<script> periodo_mayor='<?=$periodoTitle?>';
          
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
  <h6 class="card-title">Libretas Bancarias: <?=$stringEntidades;?></h6>
  <div class="table-responsive col-sm-12">
    <table id="libreta_bancaria_reporte" class="table table-condensed small" style="width:100% !important;">
      <thead>
        <tr style="background:#F36329; color:#fff;">
          <td>#</td>
          <td>Fecha</td>
          <td>Hora</td>
          <td width="35%">Descripción</td>
          <!--<td>Información C.</td>-->
          <td>Sucursal</td>
          <td>Monto</td>
          <td width="10%">Nro Doc / Nro Ref</td>
        </tr>
      </thead> 

     <?php
    $html='<tbody>';

// Preparamos
    $sqlDetalle="(SELECT * FROM (SELECT ce.*,(SELECT fecha_factura from facturas_venta where codigo=ce.cod_factura) as fecha_fac
FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria where lb.codigo in ($StringEntidadCodigos) and ce.fecha_hora BETWEEN '$fecha 00:00:00' and '$fechaHasta 23:59:59' and  ce.cod_estadoreferencial=1 and ce.cod_estado=0 $sqlFiltro order by ce.codigo) lbd)";
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
                            $estiloFac=""; 
                          }else{
                            $sqlDetalleX="SELECT * FROM facturas_venta where cod_libretabancariadetalle=$codigo";                                   
                            $stmtDetalleX = $dbh->prepare($sqlDetalleX);
                            $stmtDetalleX->execute();

                            $stmtDetalleX->bindColumn('fecha_factura', $fechaDetalle);
                            $stmtDetalleX->bindColumn('nro_factura', $nroDetalle);
                            $stmtDetalleX->bindColumn('nit', $nitDetalle);
                            $stmtDetalleX->bindColumn('razon_social', $rsDetalle);
                            $stmtDetalleX->bindColumn('observaciones', $obsDetalle);
                            $stmtDetalleX->bindColumn('importe', $impDetalle);
                            $montoAux=0;
                            while ($rowDetalleX = $stmtDetalleX->fetch(PDO::FETCH_BOUND)) {
                              $montoAux+=$impDetalle;  
                            }
                            $monto=($monto-$montoAux);
                            
                            $estiloFac="text-danger";
                          }
                       $totalMonto+=$monto;                          
?>
                        <tr>
                          <td class="text-left"><?=$index?>
                           <input type="hidden" id="cod_libretadetalle<?=$index?>" name="cod_libretadetalle<?=$index?>" value="<?=$codigo?>">
                          </td>
                          <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
                          <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                          <td class="text-left">
                            <?=$descripcion?> info: <?=$informacion_complementaria?>
                            
                          </td>      
                          <td class="text-left"><?=$agencia?></td>
                          <td class="text-right <?=$estiloFac?>"><?=number_format($monto,2,".",",")?></td>
                          <td class="text-right"><?=$nro_documento?></td>
                          </tr>
<?php
              $index++;
            }
            ?>
<?php
    $html.=    '</tbody>';

    echo $html;

    ?>
        <tfoot>
            <tr style="background:#F36329; color:#fff; font-size:20px !important;">
                <td></td> 
                <td></td>
                <td></td>
                <td>Cantidad de Registros: <?=$index-1?></td>
                <td>Total Monto: </td>
                <td><?=number_format($totalMonto,2,".",",")?></td>
                <td></td>
            </tr>
        </tfoot>
      </table>
      <br><br><br> 
       <input type="hidden" id="monto_contabilizar" name="monto_contabilizar" value="<?=$totalMonto?>">
       <input type="hidden" id="cantidad_filas" name="cantidad_filas" value="<?=($index-1)?>">
       <input type="hidden" id="mes_conta" name="mes_conta" value="<?=$mesConta?>">
       <input type="hidden" id="cod_libreta" name="cod_libreta" value="<?=$entidades?>">
       <input type="hidden" id="cod_gestion_x" name="cod_gestion_x" value="<?=$cod_gestion_x?>">
       <input type="hidden" id="cod_mes_x" name="cod_mes_x" value="<?=$cod_mes_x?>">
  </div>
</div>
                          
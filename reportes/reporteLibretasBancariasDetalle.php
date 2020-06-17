<script> periodo_mayor='<?=$periodoTitle?>';
          cuenta_mayor='<?=trim($nombreCuentaTitle)?>';
          unidad_mayor='<?=$unidadGeneral?>';
 </script>

<div class="card-body">
  <h6 class="card-title">Periodo: <?=$periodoTitle?></h6>
  <h6 class="card-title">Banco: <?=$stringEntidades;?></h6>
  <div class="table-responsive col-sm-12">
    <table id="libro_mayor_rep" class="table table-condensed" style="width:100% !important;">
      <thead>
        <tr style="background:#21618C; color:#fff;">
          <td class="text-center">#</td>
          <td>Fecha</td>
          <td>Hora</td>
          <td>Descripcion</td>
          <td>Informacion C.</td>
          <td>Sucursal</td>
          <td>Monto</td>
          <td>Nro Cheque</td>
          <td>Nro Documento</td>
          <td width="10%">Estado</td>
        </tr>
      </thead> 

     <?php
    $html='<tbody>';

// Preparamos
    $sqlDetalle="SELECT ce.*
FROM libretas_bancariasdetalle ce join libretas_bancarias lb on lb.codigo=ce.cod_libretabancaria where lb.cod_banco in ($StringEntidadCodigos) and ce.fecha_hora BETWEEN '$fecha 00:00:00' and '$fechaHasta 23:59:59' and  ce.cod_estadoreferencial=1 order by ce.codigo";
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

            $index=1;$totalMonto=0;
                        while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
                          if($codFactura==""){
                            $tituloEstado="Sin Factura";
                          }else{
                            $tituloEstado="Con Factura";
                          }
                          $totalMonto+=$monto;
                           
?>
                        <tr>
                          <td align="center"><?=$index;?></td>
                          <td class="text-center font-weight-bold"><?=strftime('%d/%m/%Y',strtotime($fecha))?></td>
                          <td class="text-center"><?=strftime('%H:%M:%S',strtotime($fecha))?></td>
                          <td class="text-left"><?=$descripcion?></td>
                          <td class="text-left"><?=$informacion_complementaria?></td>      
                          <td class="text-left"><?=$agencia?></td>
                          <td class="text-right"><?=number_format($monto,2,".","")?></td>
                          <td class="text-right"><?=$nro_cheque?></td>
                          <td class="text-left"><?=$nro_documento?></td>
                          <td class="text-right font-weight-bold"><?=$tituloEstado?></td>
                        </tr>
<?php
              $index++;
            }
            ?>
                        <tr class="font-weight-bold" style="background:#21618C; color:#fff;">
                          <td align="center" colspan="6">Totales</td>
                          <td class="d-none"></td>
                          <td class="d-none"></td>
                          <td class="d-none"></td>
                          <td class="d-none"></td>
                          <td class="d-none"></td>
                          <td class="text-right"><?=number_format($totalMonto,2,".","")?></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                          <td class="text-left"></td>
                        </tr>
<?php
    $html.=    '</tbody></table>';

    echo $html;
    ?>
  </div>
</div>
              
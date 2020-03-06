<?php
$j=1;
   $stmtUpdate = $dbh->prepare("SELECT distinct c.cod_partidapresupuestaria as codPartida, p.nombre from cuentas_simulacion c,partidas_presupuestarias p where p.codigo=c.cod_partidapresupuestaria and c.cod_simulacionservicios=$codigo and c.cod_anio=$ann");
   $stmtUpdate->execute();
    while ($rowUpdate = $stmtUpdate->fetch(PDO::FETCH_ASSOC)) {
        $codigoPartida=$rowUpdate['codPartida'];
        $nombrePartida=$rowUpdate['nombre'];

 $montoTotal=obtenerMontoPlantillaDetalleServicio($codigoPX,$codigoPartida,$ibnorcaC);
 $montoTotal=number_format($montoTotal, 2, '.', '');
 $montoEditado=obtenerMontoSimulacionCuentaServicio($codigo,$codigoPartida,$ibnorcaC);
 $montoEditado=number_format($montoEditado, 2, '.', '');


 $query="SELECT p.nombre,p.numero,c.* FROM cuentas_simulacion c, plan_cuentas p where c.cod_plancuenta=p.codigo and c.cod_simulacionservicios=$codigo and c.cod_partidapresupuestaria=$codigoPartida and c.cod_anio=$ann order by codigo";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i=1;
    ?>
    <input type="hidden" name="codigo_partida_presupuestaria<?=$ann?>QQQ<?=$j?>" id="codigo_partida_presupuestaria<?=$ann?>QQQ<?=$j?>" value="<?=$codigoPartida?>" readonly/>
    <h4 class="font-weight-bold"><center>PARTIDA: <?=$nombrePartida?></center>
    </h4>
      <div class="row">
           <input class="form-control text-right" type="hidden" name="monto_designado<?=$ann?>QQQ<?=$j?>" id="monto_designado<?=$ann?>QQQ<?=$j?>" value="<?=$montoTotal?>" readonly/>
           <input class="form-control text-right" type="hidden" name="monto_editable<?=$ann?>QQQ<?=$j?>" id="monto_editable<?=$ann?>QQQ<?=$j?>" value="<?=$montoEditado?>" readonly/>
       </div>
   <table class="table table-condensed table-bordered">
         <tr class="text-white bg-info">
           <td colspan="2"></td>
           <td colspan="2">Monto x Servicio</td>
           <td colspan="2">Monto x Persona</td>
           <td></td>
         </tr>
         <tr class="text-white bg-info">
        <td width="30%">Cuenta</td>
        <td width="25%">Detalle</td>
        <td>BOB</td>
        <td>USD</td>
        <td>BOB</td>
        <td>USD</td>
        <td class="small" width="13%">Habilitar / Deshabilitar</td>
        </tr>
    <?php
    $totalMontoDetalle=0;$totalMontoDetalleAl=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codX=$row['codigo'];
    $nomX=$row['nombre'];
    $numX=$row['numero'];
    $detallesPlantilla=obtenerDetalleSimulacionCostosPartidaServicioPeriodo($codigo,$codigoPartida,$ann);
     while ($rowDetalles = $detallesPlantilla->fetch(PDO::FETCH_ASSOC)) {
      $bandera=$rowDetalles['habilitado'];
        if($rowDetalles['cod_cuenta']==$row['cod_plancuenta']){
          $codigoCuenta=$rowDetalles['cod_cuenta'];
          $codigoDetalle=$rowDetalles['codigo'];
          $cantidadPersonalDetalle=$alumnosX;
          $cantidadPersonalDetalleE=$rowDetalles['cantidad'];
          $montoDetalle=number_format($rowDetalles['monto_total'], 2, '.', '');
          if($ibnorcaC==1){
          $montoDetalleAl=number_format($montoDetalle/$cantidadPersonalDetalleE, 2, '.', '');       
          }else{
          $montoDetalleAl=number_format($montoModX/$cantidadPersonalDetalleE, 2, '.', '');        
          } 
         if($bandera==1){
          $totalMontoDetalle+=$montoDetalle;
          $totalMontoDetalleAl+=$montoDetalleAl;        
         } 
          $montoDetalleUSD=number_format($montoDetalle/$usd, 2, '.', '');       
          $montoDetalleAlUSD=number_format($montoDetalleAl/$usd, 2, '.', '');        
          ?><tr>
              <td class="text-left small font-weight-bold"><input type="hidden" id="codigo_cuenta<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" value="<?=$codigoCuenta?>"><input type="hidden" id="codigo_fila<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" value="<?=$codX?>">[<?=$numX?>] - <?=$nomX?></td>
              <td class="text-left small font-weight-bold"><?=$rowDetalles['glosa']?></td>
              <td class="text-right"><input type="number" id="monto_mod<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" name="monto_mod<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,1)" onkeyUp="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,1)" value="<?=$montoDetalle?>" step="0.01"></td>
              <td class="text-right"><input type="number" id="monto_modUSD<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" name="monto_modUSD<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,1)" onkeyUp="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,3)" value="<?=$montoDetalleUSD?>" step="0.01"></td>
              <td class="text-right"><input type="number" id="monto_modal<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" name="monto_modal<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,2)" onkeyUp="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,2)" value="<?=$montoDetalleAl?>" step="0.01"></td>              
              <td class="text-right"><input type="number" id="monto_modalUSD<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" name="monto_modalUSD<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,2)" onkeyUp="calcularTotalPartidaGenericoServicio('<?=$ann?>',<?=$j?>,4)" value="<?=$montoDetalleAlUSD?>" step="0.01"></td>  
              <td>
                <input type="hidden" id="cantidad_personal<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" name="cantidad_personal<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" value="<?=$cantidadPersonalDetalleE?>">
                <input type="hidden" id="codigo<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" value="<?=$codigoDetalle?>">
                <div class="togglebutton">
                        <label>
                          <input type="checkbox" data-style="btn btn-primary" <?=($bandera==1)?"checked":"";?> id="habilitar<?=$ann?>QQQ<?=$j?>RRR<?=$i?>" onchange="activarInputMontoGenericoServicio('<?=$ann?>','<?=$j?>RRR<?=$i?>')">
                          <span class="toggle"></span>
                        </label>
                </div>
              
              </td>
             </tr> 
           <?php
           $i++;         
        }         
     }
    }
  ?>
  </table>
 
  <input type="hidden" id="numero_cuentas<?=$ann?>QQQ<?=$j?>" value="<?=$i?>">
  <?php 
  $j++; 
  }
  ?>
   <div id="mensaje_cuenta<?=$ann?>"></div>
  <input type="hidden" id="numero_cuentaspartida<?=$ann?>" value="<?=$j?>">
  <div class="form-group float-right">
    <button class="btn btn-success" id="guardar_cuenta<?=$ann?>" onclick="guardarCuentasSimulacionGenericoServicioPrevio('<?=$ann?>',<?=$ibnorcaC?>)">Editar Detalle por Persona</button>
  </div>
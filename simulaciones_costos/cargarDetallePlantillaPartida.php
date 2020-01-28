<?php
$j=1;
   $stmtUpdate = $dbh->prepare("SELECT distinct c.cod_partidapresupuestaria as codPartida, p.nombre from cuentas_simulacion c,partidas_presupuestarias p where p.codigo=c.cod_partidapresupuestaria and c.cod_simulacioncostos=$codigo");
   $stmtUpdate->execute();
    while ($rowUpdate = $stmtUpdate->fetch(PDO::FETCH_ASSOC)) {
        $codigoPartida=$rowUpdate['codPartida'];
        $nombrePartida=$rowUpdate['nombre'];

 $montoTotal=obtenerMontoPlantillaDetalle($codigoPX,$codigoPartida,$ibnorcaC);
 $montoTotal=number_format($montoTotal, 2, '.', '');
 $montoEditado=obtenerMontoSimulacionCuenta($codigo,$codigoPartida,$ibnorcaC);
 $montoEditado=number_format($montoEditado, 2, '.', '');


 $query="SELECT p.nombre,p.numero,c.* FROM cuentas_simulacion c, plan_cuentas p where c.cod_plancuenta=p.codigo and c.cod_simulacioncostos=$codigo and c.cod_partidapresupuestaria=$codigoPartida order by codigo";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i=1;
    ?>
    <input type="hidden" name="codigo_partida_presupuestaria<?=$j?>" id="codigo_partida_presupuestaria<?=$j?>" value="<?=$codigoPartida?>" readonly/>
    <h4 class="font-weight-bold"><center>PARTIDA: <?=$nombrePartida?></center></h4>
      <div class="row">
        <label class="col-sm-3 col-form-label">Monto x Modulo Plantilla:</label>
        <div class="col-sm-3">
         <div class="form-group">
           <input class="form-control text-right" type="number" name="monto_designado<?=$j?>" id="monto_designado<?=$j?>" value="<?=$montoTotal?>" readonly/>
         </div>
         </div>
         <label class="col-sm-3 col-form-label">Monto x Modulo Simulaci&oacute;n:</label>
        <div class="col-sm-3">
         <div class="form-group">
           <input class="form-control text-right" type="number" name="monto_editable<?=$j?>" id="monto_editable<?=$j?>" value="<?=$montoEditado?>" readonly/>
         </div>
         </div>
       </div>
   <table class="table table-condensed table-bordered">
         <tr class="text-white bg-info">
        <td>Cuenta</td>
        <td>Detalle</td>
        <td>Monto x Modulo</td>
        <td>Monto x Persona</td>
        <td class="small">Habilitar / Deshabilitar</td>
        </tr>
    <?php
    $totalMontoDetalle=0;$totalMontoDetalleAl=0;
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codX=$row['codigo'];
    $nomX=$row['nombre'];
    $numX=$row['numero'];
    $detallesPlantilla=obtenerDetallePlantillaCostosPartida($codigoPX,$codigoPartida);
     while ($rowDetalles = $detallesPlantilla->fetch(PDO::FETCH_ASSOC)) {
      $bandera=$rowDetalles['habilitado'];
        if($rowDetalles['cod_cuenta']==$row['cod_plancuenta']){
          $codigoCuenta=$rowDetalles['cod_cuenta'];
          $codigoDetalle=$rowDetalles['codigo'];
          $montoDetalle=number_format($rowDetalles['monto_total'], 2, '.', '');
          if($ibnorcaC==1){
          $montoDetalleAl=number_format($montoDetalle/$alumnosX, 2, '.', '');       
          }else{
          $montoDetalleAl=number_format($montoModX/$alumnosExternoX, 2, '.', '');        
          } 
         if($bandera==1){
          $totalMontoDetalle+=$montoDetalle;
          $totalMontoDetalleAl+=$montoDetalleAl;        
         }   
          ?><tr>
              <td class="text-left small font-weight-bold"><input type="hidden" id="codigo_cuenta<?=$j?>RRR<?=$i?>" value="<?=$codigoCuenta?>"><input type="hidden" id="codigo_fila<?=$j?>RRR<?=$i?>" value="<?=$codX?>">[<?=$numX?>] - <?=$nomX?></td>
              <td class="text-left small font-weight-bold"><?=$rowDetalles['glosa']?></td>
              <td class="text-right"><input type="number" id="monto_mod<?=$j?>RRR<?=$i?>" name="monto_mod<?=$j?>RRR<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartidaGenerico(<?=$j?>,1)" onkeyUp="calcularTotalPartidaGenerico(<?=$j?>,1)" value="<?=$montoDetalle?>" step="0.01"></td>
              <td class="text-right"><input type="number" id="monto_modal<?=$j?>RRR<?=$i?>" name="monto_modal<?=$j?>RRR<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartidaGenerico(<?=$j?>,2)" onkeyUp="calcularTotalPartidaGenerico(<?=$j?>,2)" value="<?=$montoDetalleAl?>" step="0.01"></td>
              <td><input type="hidden" id="codigo<?=$j?>RRR<?=$i?>" value="<?=$codigoDetalle?>">
                <div class="togglebutton">
                        <label>
                          <input type="checkbox" <?=($bandera==1)?"checked":"";?> onchange="activarInputMontoGenerico('<?=$j?>RRR<?=$i?>')">
                          <span class="toggle"></span>
                        </label>
                </div>
                <!--<a href="#" class="btn btn-sm btn-warning" onclick="activarInputMonto(<?=$j?>RRR<?=$i?>)">habilitar / deshabilitar</a>-->
              </td>
             </tr> 
           <?php
           $i++;         
        }         
     }
    }
  ?>
      <tr>
        <td colspan="2" class="text-center font-weight-bold">Total</td>
        <td class="text-right font-weight-bold"><?=$totalMontoDetalle?></td>
        <td class="text-right"><?=$totalMontoDetalleAl?></td>
        <td></td>
      </tr>
  </table>
 
  <input type="hidden" id="numero_cuentas<?=$j?>" value="<?=$i?>">
  <?php 
  $j++; 
  }
  ?>
   <div id="mensaje_cuenta"></div>
  <input type="hidden" id="numero_cuentaspartida" value="<?=$j?>">
  <div class="form-group float-right">
    <button class="btn btn-default" id="guardar_cuenta" onclick="guardarCuentasSimulacionGenerico(<?=$ibnorcaC?>)">Guardar</button>
  </div>
<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

if(isset($_GET["codigo"])){
 $codigo=$_GET["codigo"];
 $ibnorca=$_GET["ibnorca"];
 $codSim=$_GET["codSim"];
 $codPar=$_GET["codPar"];
 $al_i=$_GET["al_i"];
 $al_f=$_GET["al_f"];
 $montoTotal=obtenerMontoPlantillaDetalle($codPar,$codigo,$ibnorca);
 $montoTotal=number_format($montoTotal, 2, '.', '');
 $montoEditado=obtenerMontoSimulacionCuenta($codSim,$codigo,$ibnorca);
 $montoEditado=number_format($montoEditado, 2, '.', '');


 $query="SELECT p.nombre,p.numero,c.* FROM cuentas_simulacion c, plan_cuentas p where c.cod_plancuenta=p.codigo and c.cod_simulacioncostos=$codSim and c.cod_partidapresupuestaria=$codigo order by codigo";
    $stmt = $dbh->prepare($query);
    $stmt->execute();
    $i=1;
    ?>
      <div class="row">
        <label class="col-sm-3 col-form-label">Monto x Modulo Plantilla:</label>
        <div class="col-sm-3">
         <div class="form-group">
           <input class="form-control text-right" type="number" name="monto_designado" id="monto_designado" value="<?=$montoTotal?>" readonly/>
         </div>
         </div>
         <label class="col-sm-3 col-form-label">Monto x Modulo Simulaci&oacute;n:</label>
        <div class="col-sm-3">
         <div class="form-group">
           <input class="form-control text-right" type="number" name="monto_editable" id="monto_editable" value="<?=$montoEditado?>" readonly/>
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
    $codigoX=$row['codigo'];
    $nombreX=$row['nombre'];
    $numeroX=$row['numero'];
    $detallesPlantilla=obtenerDetallePlantillaCostosPartida($codPar,$codigo);
     while ($rowDetalles = $detallesPlantilla->fetch(PDO::FETCH_ASSOC)) {
      $bandera=$rowDetalles['habilitado'];
        if($rowDetalles['cod_cuenta']==$row['cod_plancuenta']){
          $codigoCuenta=$rowDetalles['cod_cuenta'];
          $codigoDetalle=$rowDetalles['codigo'];
          $montoDetalle=number_format($rowDetalles['monto_total'], 2, '.', '');
          if($ibnorca==1){
          $montoDetalleAl=number_format($montoDetalle/$al_i, 2, '.', '');       
          }else{
          $montoDetalleAl=number_format($montoModX/$al_f, 2, '.', '');        
          } 
         if($bandera==1){
          $totalMontoDetalle+=$montoDetalle;
          $totalMontoDetalleAl+=$montoDetalleAl;        
         }   
          ?><tr>
              <td class="text-left small font-weight-bold"><input type="hidden" id="codigo_cuenta<?=$i?>" value="<?=$codigoCuenta?>"><input type="hidden" id="codigo_fila<?=$i?>" value="<?=$codigoX?>">[<?=$numeroX?>] - <?=$nombreX?></td>
              <td class="text-left small font-weight-bold"><?=$rowDetalles['glosa']?></td>
              <td class="text-right"><input type="number" id="monto_mod<?=$i?>" name="monto_mod<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartida(1)" onkeyUp="calcularTotalPartida(1)" value="<?=$montoDetalle?>" step="0.01"></td>
              <td class="text-right"><input type="number" id="monto_modal<?=$i?>" name="monto_modal<?=$i?>" <?=($bandera==0)?"readonly":"";?> class="form-control text-info text-right" onchange="calcularTotalPartida(2)" onkeyUp="calcularTotalPartida(2)" value="<?=$montoDetalleAl?>" step="0.01"></td>
              <td><input type="hidden" id="codigo<?=$i?>" value="<?=$codigoDetalle?>">
                <div class="togglebutton">
                        <label>
                          <input type="checkbox" <?=($bandera==1)?"checked":"";?> onchange="activarInputMonto(<?=$i?>)">
                          <span class="toggle"></span>
                        </label>
                </div>
                <!--<a href="#" class="btn btn-sm btn-warning" onclick="activarInputMonto(<?=$i?>)">habilitar / deshabilitar</a>-->
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
  <div class="form-group float-right">
    <button class="btn btn-default" id="guardar_cuenta" onclick="guardarCuentasSimulacion(<?=$ibnorca?>)">Guardar</button>
  </div>
  <div id="mensaje_cuenta"></div>
  <input type="hidden" id="numero_cuentas" value="<?=$i?>">
  <?php      
}     
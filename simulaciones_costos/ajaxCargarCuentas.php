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
        <label class="col-sm-3 col-form-label">Monto Original:</label>
        <div class="col-sm-3">
         <div class="form-group">
           <input class="form-control text-right" type="number" name="monto_designado" id="monto_designado" value="<?=$montoTotal?>" readonly/>
         </div>
         </div>
         <label class="col-sm-3 col-form-label">Monto Editado:</label>
        <div class="col-sm-3">
         <div class="form-group">
           <input class="form-control text-right" type="number" name="monto_editable" id="monto_editable" value="<?=$montoEditado?>" readonly/>
         </div>
         </div>
       </div>
   <table class="table table-condensed table-bordered table-striped">
      <thead>
         <tr class="text-dark bg-plomo">
        <th>Cuenta</th>
        <th>Porcentaje</th>
        <th>Ibnorca</th>
        <th>Fuera</th>
        <th>Alum Ib.</th>
        <th>Alum Fu.</th>     
        </tr>
      </thead>
      <tbody>
    <?php
   while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$row['codigo'];
    $nombreX=$row['nombre'];
    $numeroX=$row['numero'];
    $montoCalX=number_format($row['monto_local'], 2, '.', '');
    $montoModX=number_format($row['monto_externo'], 2, '.', ''); 

    $montoCalAlX=number_format($montoCalX/$al_i, 0, '.', '');
    $montoModAlX=number_format($montoModX/$al_f, 0, '.', ''); 

    $porcentajeX=$row['porcentaje'];
    ?><tr><td class="text-left">[<?=$numeroX?>] - <?=$nombreX?></td>
     <td class="text-right"><?=number_format($porcentajeX, 2, '.', ',')?> %</td> 
      <?php
    if($ibnorca==1){
     ?><td class="text-right"><input type="number" id="monto_mod<?=$i?>" name="monto_mod<?=$i?>" class="form-control text-info text-right" placeholder="Monto modificado" onchange="calcularTotalPartida()" onkeyUp="calcularTotalPartida()" value="<?=$montoCalX?>" step="0.001"><input type="hidden" id="codigo<?=$i?>" value="<?=$codigoX?>"></td><td class="text-right"><?=$montoModX?></td>
     <td class="text-right"><input type="number" id="monto_modal<?=$i?>" name="monto_modal<?=$i?>" class="form-control text-danger text-right" placeholder="Monto" onchange="calcularTotalPartida()" onkeyUp="calcularTotalPartida()" value="<?=$montoCalAlX?>" step="0.01"></td><td class="text-right"><?=$montoModAlX?></td>
    <?php 
    }else{
      ?><td class="text-right"><?=$montoCalX?></td><td class="text-right has-sucess"><input type="number" id="monto_mod<?=$i?>" name="monto_mod<?=$i?>" class="form-control text-info text-right" placeholder="Monto modificado" onchange="calcularTotalPartida()" onkeyUp="calcularTotalPartida()" value="<?=$montoModX?>" step="0.001"><input type="hidden" id="codigo<?=$i?>" value="<?=$codigoX?>"></td>
      <td class="text-right"><?=$montoCalAlX?></td><td class="text-right has-sucess"><input type="number" id="monto_modal<?=$i?>" name="monto_modal<?=$i?>" class="form-control text-danger text-right" placeholder="Monto" onchange="calcularTotalPartida()" onkeyUp="calcularTotalPartida()" value="<?=$montoModAlX?>" step="0.01"></td>
    <?php 
    } 
    $i++;
    ?></tr><?php
    }
  ?>
   </tbody>
  </table>
  <div class="form-group float-right">
    <button class="btn btn-default" id="guardar_cuenta" onclick="guardarCuentasSimulacion(<?=$ibnorca?>)">Guardar</button>
  </div>
  <div id="mensaje_cuenta"></div>
  <input type="hidden" id="numero_cuentas" value="<?=$i?>">
  <?php      
}     
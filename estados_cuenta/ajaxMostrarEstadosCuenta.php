<?php

require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$fechaActual=date("d/m/Y");
$codCuenta=$_GET['cod_cuenta'];
$tipo=$_GET['tipo'];
$tipoProveedorCliente=$_GET['tipo_proveedorcliente'];
$mes=$_GET['mes'];
?>
<table class="table table-bordered table-condensed table-warning">
	<thead>
	  <tr class="">
	  	<th class="text-left"></th>
	  	<th class="text-left">Fecha</th>
      <th class="text-left">Proveedor</th>
	  	<th class="text-left">Glosa</th>
	  	<th class="text-right">D&eacute;bito</th>
	  	<th class="text-right">Cr&eacute;dito</th>
	  	<th class="text-right">Saldo</th>
	  </tr>
	</thead>
	<tbody id="tabla_estadocuenta">
<?php
  $sqlZ="SELECT e.*,d.glosa,d.haber,d.debe FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta or e.cod_plancuenta=$codCuenta or e.cod_cuentaaux=$codCuenta) and e.cod_comprobantedetalleorigen=0 order by e.fecha";
  
  $stmt = $dbh->prepare($sqlZ);
  $stmt->execute();
  $i=0;$saldo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	 $codigoX=$row['codigo'];
	 $codPlanCuentaX=$row['cod_plancuenta'];
	 $codCompDetX=$row['cod_comprobantedetalle'];
   $codProveedorX=$row['cod_proveedor'];
	 $fechaX=$row['fecha'];
	 $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
	 $montoX=$row['monto'];
	 $glosaX=$row['glosa'];
	 $debeX=$row['debe'];
	 $haberX=$row['haber'];
   $codCuentaAuxX=$row['cod_cuentaaux'];

   //SACAMOS CUANTO SE PAGO DEL ESTADO DE CUENTA.
    $sqlContra="SELECT sum(monto)as monto from estados_cuenta e where e.cod_comprobantedetalleorigen='$codCompDetX'";
    $stmtContra = $dbh->prepare($sqlContra);
    $stmtContra->execute();
    $montoContra=0;
    while ($rowContra = $stmtContra->fetch(PDO::FETCH_ASSOC)) {
      $montoContra=$rowContra['monto'];
    }
    $debeX=$montoContra;

    $saldo=$saldo+$haberX-$debeX;


   if(obtenerProveedorCuentaAux($row['cod_cuentaaux'])==""){
    $proveedorX="Sin Proveedor";
   }else{
    $proveedorX=obtenerProveedorCuentaAux($row['cod_cuentaaux']);
   }
	 if($haberX > 0){
       ?>
  	   <tr class="bg-white det-estados">
        <td>
        <input type="hidden" id="codigoCuentaAux<?=$i?>" value="<?=$codCuentaAuxX?>">
        <!-- style="display:none"-->
  	   	<?php 
          if($tipo==1 && $tipoProveedorCliente==1){ 
        ?>
            <div class="form-check">
               <label class="form-check-label">
                     <input type="radio" class="form-check-input" id="cuentas_origen_detalle<?=$i?>" name="cuentas_origen_detalle" value="<?=$codCompDetX?>####<?=$codCuentaAuxX?>####<?=$codProveedorX?>">
                    <span class="form-check-sign">
                      <span class="check"></span>
                    </span>       
               </label>
             </div>
            <?php    
  	   } ?>
  	   </td>
       <td class="text-left small font-weight-bold"><?=$fechaX?></td>
       <td class="text-left small"><?=$proveedorX?></td>
       <td class="text-left small"><?=$glosaX?></td>
       <td class="text-right small"><?=formatNumberDec($montoContra)?></td>
       <td class="text-right small"><?=formatNumberDec($montoX)?></td>
       <td class="text-right small font-weight-bold"><?=formatNumberDec($saldo);?></td>
     </tr>
  	   <?php
	 }else{
        ?>
  	   <tr class="bg-white det-estados"><td>
  	   	<?php if($tipo==2){ 
  	   } ?>
  	   </td>
       <td class="text-left font-weight-bold"><?=$fechaX?></td>
       <td class="text-left"><?=$proveedorX?></td><td class="text-left"><?=$glosaX?></td>
       <td class="text-right"><?=formatNumberDec($montoX)?></td>
       <td class="text-right"></td>
       <td class="text-right font-weight-bold"><?=formatNumberDec($saldo);?></td>
     </tr>
  	   <?php
	 }
	 $i++;
  }
?>
	</tbody>
</table>
<?php
echo "@".$saldo;
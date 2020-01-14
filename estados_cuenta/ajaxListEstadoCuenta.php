<?php

require_once '../conexion.php';
require_once '../functions.php';
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
$codCuentaAux=$_GET['cod_cuentaaux'];
$tipo=$_GET['tipo'];
$mes=$_GET['mes'];
?>
<table class="table table-bordered table-condensed table-warning">
	<thead>
	  <tr class="">
	  	<th class="text-left">Fecha</th>
	  	<th class="text-left">Glosa</th>
	  	<th class="text-right">D&eacute;bito</th>
	  	<th class="text-right">Cr&eacute;dito</th>
	  	<th class="text-right">Saldo</th>
	  </tr>
	</thead>
	<tbody id="tabla_estadocuenta">
<?php
  /*$stmt = $dbh->prepare("SELECT e.* FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and d.cod_cuenta=$codCuenta");*/
  if($codCuenta==0){
   $stmt = $dbh->prepare("SELECT e.*,d.glosa,d.haber,d.debe FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuentaauxiliar=$codCuentaAux or e.cod_cuentaaux=$codCuentaAux) order by e.fecha");
  }else{
  	$stmt = $dbh->prepare("SELECT e.*,d.glosa,d.haber,d.debe FROM estados_cuenta e,comprobantes_detalle d where e.cod_comprobantedetalle=d.codigo and (d.cod_cuenta=$codCuenta or e.cod_plancuenta=$codCuenta) order by e.fecha");
  }
  
  $stmt->execute();
  $i=0;$saldo=0;
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	 $codigoX=$row['codigo'];
	 $codPlanCuentaX=$row['cod_plancuenta'];
	 $codCompDetX=$row['cod_comprobantedetalle'];
	 $fechaX=$row['fecha'];
	 $fechaX=strftime('%d/%m/%Y',strtotime($fechaX));
	 $montoX=$row['monto'];
	 $glosaX=$row['glosa'];
	 $debeX=$row['debe'];
	 $haberX=$row['haber'];
	 $saldo=$saldo+$debeX-$haberX;
   $montoTit=number_format($montoX, 2, '.', ',');
   $saldoTit=number_format($saldo, 2, '.', ',');
	 if($haberX==0||$haberX==""){
       ?>
  	   <tr class="bg-white det-estados"><td class="text-left font-weight-bold"><?=$fechaX?></td><td class="text-left"><?=$glosaX?></td><td class="text-right"><?=$montoTit?></td><td class="text-right"></td><td class="text-right font-weight-bold"><?=$saldoTit?></td></tr>
  	   <?php
	 }else{
        ?>
  	   <tr class="bg-white det-estados"><td class="text-left font-weight-bold"><?=$fechaX?></td><td class="text-left"><?=$glosaX?></td><td class="text-right"></td><td class="text-right"><?=$montoTit?></td><td class="text-right font-weight-bold"><?=$saldoTit?></td></tr>
  	   <?php
	 }
	 
	 $i++;
  }
?>
	</tbody>
</table>
<?php
echo "@".$saldo;
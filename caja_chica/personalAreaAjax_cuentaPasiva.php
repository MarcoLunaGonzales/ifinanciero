<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_comprobante_det = $_GET["codigo_comprobante"];
//ini_set("display_errors", "1");
$db = new Conexion();

$sqlcomproDet="SELECT cod_comprobante from comprobantes_detalle where codigo=$codigo_comprobante_det";
$stmtComprobantDet = $db->prepare($sqlcomproDet);
$stmtComprobantDet->execute();
$resultComproDet=$stmtComprobantDet->fetch();
$cod_comprobante_x=$resultComproDet['cod_comprobante'];

//listamos los comprobantes_detalle del comprobante en curso
$sql="SELECT cod_cuenta from comprobantes_detalle where cod_comprobante=$cod_comprobante_x";
$stmtComprobantesDet = $db->prepare($sql);
$stmtComprobantesDet->execute();
$cod_plan_cuenta=0;
while ($rowComprobantesDet = $stmtComprobantesDet->fetch()){ 
	$cod_cuenta_x=$rowComprobantesDet['cod_cuenta'];	
	// verificamos que cuenta es de gastos
	$stmtPlanCuenta = $db->prepare("SELECT codigo,numero from plan_cuentas where codigo=$cod_cuenta_x");
	$stmtPlanCuenta->execute();
	$resultPlanCuenta=$stmtPlanCuenta->fetch();
	$codigo_cuenta=$resultPlanCuenta['codigo'];
	$numero_cuenta=$resultPlanCuenta['numero'];
	$digito = substr($numero_cuenta, 0, 1);
	
	if($digito==5)
	{
		$cod_plan_cuenta=$codigo_cuenta;
	}
}
$sql="SELECT cod_area from comprobantes_detalle where cod_comprobante=$cod_comprobante_x and cod_cuenta=$cod_plan_cuenta";
// echo $sql;
$stmtComprobant = $db->prepare($sql);
$stmtComprobant->execute();
$resultCompro=$stmtComprobant->fetch();
$cod_area=$resultCompro['cod_area'];
//finalmente listamos las areas
$sqlUO="SELECT codigo,nombre,abreviatura from areas where cod_estado=1 and centro_costos=1 ORDER BY nombre";
$stmtArea = $db->prepare($sqlUO);
$stmtArea->execute();
?>
<select name="cod_area" id="cod_area" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true">
    <?php 
    	while ($row = $stmtArea->fetch()){ 
	?>
      	 <option <?=($cod_area==$row["codigo"])?"selected":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
     <?php 
 		} 
 	?>
 </select>




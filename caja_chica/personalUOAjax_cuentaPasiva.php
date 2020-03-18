<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_comprobante_det = $_GET["codigo_comprobante"];
//ini_set("display_errors", "1");
$db = new Conexion();
//buscamos el codigo de comprobante
$stmtComprobantDet = $db->prepare("SELECT cod_comprobante from comprobantes_detalle where codigo=$codigo_comprobante_det");
$stmtComprobantDet->execute();
$resultComproDet=$stmtComprobantDet->fetch();
$cod_comprobante_x=$resultComproDet['cod_comprobante'];

//listamos los comprobantes_detalle del comprobante en curso
$sql="SELECT cod_cuenta from comprobantes_detalle where cod_comprobante=$cod_comprobante_x";
$stmtComprobantesDet = $db->prepare($sql);
$stmtComprobantesDet->execute();
while ($rowComprobantesDet = $stmtComprobantesDet->fetch()){ 
	$cod_cuenta_x=$rowComprobantesDet['cod_cuenta'];	
	// verificamos que cuenta es de gastos
	$stmtPlanCuenta = $db->prepare("SELECT codigo,numero from plan_cuentas where codigo=$cod_cuenta_x");
	$stmtPlanCuenta->execute();
	$resultPlanCuenta=$stmtPlanCuenta->fetch();
	$codigo_cuenta=$resultPlanCuenta['codigo'];
	$numero_cuenta=$resultPlanCuenta['numero'];
	$digito = substr($numero_cuenta, 0, 1);
	$cod_plan_cuenta=0;
	if($digito==5)
	{
		$cod_plan_cuenta=$codigo_cuenta;
	}
}
$sql="SELECT cod_unidadorganizacional from comprobantes_detalle where cod_comprobante=$cod_comprobante_x and cod_cuenta=$cod_plan_cuenta";
$stmtComprobant = $db->prepare($sql);
$stmtComprobant->execute();
$resultCompro=$stmtComprobant->fetch();
$cod_uo=$resultCompro['cod_unidadorganizacional'];

$sqlUO="SELECT codigo,nombre,abreviatura from unidades_organizacionales where cod_estado=1";
$stmtUO = $db->prepare($sqlUO);
$stmtUO->execute();
?>
<select name="cod_uo" id="cod_uo" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true" onChange="ajaxAreaUOCAJACHICA(this);">
    <?php 
    	while ($row = $stmtUO->fetch()){ 
	?>
      	 <option <?=($cod_uo==$row["codigo"])?"selected":"";?> data-subtext="<?=$row["codigo"];?>" value="<?=$row["codigo"];?>"><?=$row["nombre"];?>(<?=$row["abreviatura"];?>)</option>
     <?php 
 		} 
 	?>
 </select>



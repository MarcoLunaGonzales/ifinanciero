<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$result=0;


$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();



$cod_rd=$_POST["cod_rd"];
$facturas= json_decode($_POST['facturas']);
session_start();
    $sqlDeleteFactura="DELETE from facturas_detalle_cajachica where cod_cajachicadetalle=$cod_rd";
      $stmtDelFactura = $dbh->prepare($sqlDeleteFactura);
      $stmtDelFactura->execute();
      $nF=cantidadF($facturas[$cod_rd-1]);
        //echo $nF;
        $suma_importe_fac=0;
         for($j=0;$j<$nF;$j++){
         	  $nit=$facturas[$cod_rd-1][$j]->nit;
         	  $nroFac=$facturas[$cod_rd-1][$j]->nroFac;
         	  
         	  $fecha=$facturas[$cod_rd-1][$j]->fechaFac;
         	  $porciones = explode("/", $fecha);
         	  $fechaFac=$porciones[2]."-".$porciones[1]."-".$porciones[0];
         	  
         	  $razonFac=$facturas[$cod_rd-1][$j]->razonFac;
         	  $impFac=$facturas[$cod_rd-1][$j]->impFac;
         	  $exeFac=0;
         	  $autFac=$facturas[$cod_rd-1][$j]->autFac;
         	  $conFac=$facturas[$cod_rd-1][$j]->conFac;

            	$suma_importe_fac=$suma_importe_fac+$impFac;
		      $sqlDetalle2="INSERT INTO facturas_detalle_cajachica (cod_cajachicadetalle, nit, nro_factura, fecha, razon_social, importe, exento, nro_autorizacion, codigo_control) VALUES ('$cod_rd', '$nit', '$nroFac', '$fechaFac', '$razonFac', '$impFac', '$exeFac', '$autFac', '$conFac')";
		      $stmtDetalle2 = $dbh->prepare($sqlDetalle2);
		      $flagSuccessDetalle2=$stmtDetalle2->execute();
         }


 $stmtCC = $dbh->prepare("SELECT cc.codigo,cc.monto_reembolso,ccd.monto
from  caja_chicadetalle ccd,caja_chica cc
where ccd.cod_cajachica=cc.codigo and ccd.codigo=$cod_rd");
$stmtCC->execute();
$resultCC=$stmtCC->fetch();
$cod_cajachica=$resultCC['codigo'];
$monto_reembolso=$resultCC['monto_reembolso'];
$monto_a_rendir=$resultCC['monto'];
$monto_faltante=$monto_a_rendir-$suma_importe_fac;

//  //------

$monto_reembolso=$monto_reembolso+$monto_faltante;

//actualizamos el monto de reeembolso de caja chica
$stmtCCUpdate = $dbh->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cajachica");
$stmtCCUpdate->execute();

//actualizamos estado en cajachjicadetalle
$sqlCCD="UPDATE caja_chicadetalle set cod_estado=2,monto_rendicion=$suma_importe_fac where codigo=$cod_rd";
$stmtCCD = $dbh->prepare($sqlCCD);
$stmtCCD->execute();
//estado de rendicion 
$fecha_recepcion=date("Y-m-d H:i:s");
$sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$suma_importe_fac where codigo=$cod_rd";
$stmtUR = $dbh->prepare($sql);
$flagSuccess=$stmtUR->execute();

if($flagSuccess){
	showAlertSuccessError(true,"../".$urlListaRendiciones2);	
}else{
	showAlertSuccessError(false,"../".$urlListaRendiciones2);
}


?>
<?php

require_once '../layouts/bodylogin.php';
require_once '../conexion.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$result=0;


$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();






// RECIBIMOS LAS VARIABLES


// $codigo_detRendicion=$_POST['codigo_detRendicion'];

$cod_rendicion=$_POST['cod_rendicion'];
$cod_cajachicadetalle=$_POST['cod_cajachicadetalle'];
$cantidadFilas=$_POST["cantidad_filas"];


$monto_total_rendido=$_POST["monto_total"];
$monto_faltante=$_POST["monto_faltante"];


$cod_estadoreferencial=1;
for ($i=1;$i<=$cantidadFilas;$i++){
	$tipo_doc=$_POST['tipo_doc'.$i];	
	$numero_doc=$_POST['numero_doc'.$i];
	$fecha_doc=$_POST['fecha_doc'.$i];
	$monto_A=$_POST['monto_A'.$i];
	$observacionesA=$_POST['observacionesA'.$i];

	// 	echo "codigo_rendicionA: ".$cod_rendicion."<br>";
	// echo "cod_tipo_documentoA: ".$tipo_doc."<br>";
	// echo "numero_doc: ".$numero_doc."<br>";
	// echo "fecha_doc: ".$fecha_doc."<br>";
	// echo "monto_A: ".$monto_A."<br>";
	// echo "observacionesA: ".$observacionesA."<br>";
	//insertamos rendicones_detalle
	$sql="INSERT INTO rendiciones_detalle(cod_rendicion,cod_tipodoccajachica,fecha_doc,nro_doc,monto,observaciones,cod_estadoreferencial) values(:cod_rendicion,:cod_tipodoccajachica,:fecha_doc,:nro_doc,:monto,:observaciones,:cod_estadoreferencial)";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_rendicion', $cod_rendicion);
	$stmtU->bindParam(':cod_tipodoccajachica', $tipo_doc);
	$stmtU->bindParam(':fecha_doc', $fecha_doc);
	$stmtU->bindParam(':nro_doc', $numero_doc);
	$stmtU->bindParam(':monto', $monto_A);
	$stmtU->bindParam(':observaciones', $observacionesA);
	$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
	$flagSuccess=$stmtU->execute();

}
// $stmtCC = $dbhU->prepare("SELECT cc.codigo,cc.monto_reembolso,ccd.monto
// 	from  caja_chicadetalle ccd,caja_chica cc
// 	where ccd.cod_cajachica=cc.codigo and ccd.codigo=$cod_cajachicadetalle");
// 	$stmtCC->execute();
// 	$resultCC=$stmtCC->fetch();
// 	$cod_cajachica=$resultCC['codigo'];
// 	$monto_reembolso=$resultCC['monto_reembolso'];
// 	$monto_a_rendir=$resultCC['monto'];
// 	
// 	//------
	
	$monto_reembolso=$monto_reembolso+$monto_faltante;

	//actualizamos el monto de reeembolso de caja chica
	$stmtCCUpdate = $dbhU->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cajachica");
	$stmtCCUpdate->execute();

	//actualizamos estado en cajachjicadetalle
	$sqlCCD="UPDATE caja_chicadetalle set cod_estado=2,monto_rendicion=$monto_total_rendido where codigo=$cod_cajachicadetalle";
	$stmtCCD = $dbhU->prepare($sqlCCD);
	$stmtCCD->execute();
	//estado de rendicion 
	$fecha_recepcion=date("Y-m-d H:i:s");
	$sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$monto_total_rendido where codigo=$cod_rendicion";
	$stmtUR = $dbhU->prepare($sql);
	$flagSuccess=$stmtUR->execute();

if($flagSuccess){
	showAlertSuccessError(true,"../".$urlListaRendiciones2);	
}else{
	showAlertSuccessError(false,"../".$urlListaRendiciones2);
}




// echo "codigo_rendicionA: ".$codigo_rendicionA."<br>";
// echo "cod_tipo_documentoA: ".$cod_tipo_documentoA."<br>";
// echo "numero_doc: ".$numero_doc."<br>";
// echo "fecha_doc: ".$fecha_doc."<br>";
// echo "monto_A: ".$monto_A."<br>";
// echo "observacionesA: ".$observacionesA."<br>";
// echo "cod_estadoreferencial: ".$cod_estadoreferencial."<br>";

// // $fecha_recepcion=date("Y-m-d H:i:s");
// 	//cuando devuelve AF
// 	// Prepare
// if($cod_estadoreferencial==1){//insertar	rendicion
// 	$sql="INSERT INTO rendiciones_detalle(cod_rendicion,cod_tipodoccajachica,fecha_doc,nro_doc,monto,observaciones,cod_estadoreferencial) values(:cod_rendicion,:cod_tipodoccajachica,:fecha_doc,:nro_doc,:monto,:observaciones,:cod_estadoreferencial)";
// 	$stmtU = $dbhU->prepare($sql);
// 	// Bind
// 	$stmtU->bindParam(':cod_rendicion', $codigo_rendicionA);
// 	$stmtU->bindParam(':cod_tipodoccajachica', $cod_tipo_documentoA);
// 	$stmtU->bindParam(':fecha_doc', $fecha_doc);
// 	$stmtU->bindParam(':nro_doc', $numero_doc);
// 	$stmtU->bindParam(':monto', $monto_A);
// 	$stmtU->bindParam(':observaciones', $observacionesA);
// 	$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
	
// }elseif($cod_estadoreferencial==2){//actualizar rendicion
// 	$sql="UPDATE rendiciones_detalle set cod_tipodoccajachica=:cod_tipodoccajachica,fecha_doc=:fecha_doc,nro_doc=:nro_doc,monto=:monto,observaciones=:observaciones where codigo=:codigo";
// 	$stmtU = $dbhU->prepare($sql);
// 	// Bind
// 	$stmtU->bindParam(':codigo', $codigo_detRendicionE);
// 	$stmtU->bindParam(':cod_tipodoccajachica', $cod_tipo_documentoA);
// 	$stmtU->bindParam(':fecha_doc', $fecha_doc);
// 	$stmtU->bindParam(':nro_doc', $numero_doc);
// 	$stmtU->bindParam(':monto', $monto_A);
// 	$stmtU->bindParam(':observaciones', $observacionesA);


// }elseif ($cod_estadoreferencial==3) {//eliminar rendicion
// 	$sql="UPDATE rendiciones_detalle set cod_estadoreferencial=2 where codigo=:codigo";
// 	$stmtU = $dbhU->prepare($sql);
// 	// Bind	
// 	$stmtU->bindParam(':codigo', $codigo_detRendicionE);	
// }elseif($cod_estadoreferencial==4){ //guardar rendiciones

	//obtenemos codigo caja chica y monto reembolso
	// $stmtCC = $dbhU->prepare("SELECT cc.codigo,cc.monto_reembolso,ccd.monto
	// from  caja_chicadetalle ccd,caja_chica cc
	// where ccd.cod_cajachica=cc.codigo and ccd.codigo=$codigo_detRendicionE");
	// $stmtCC->execute();
	// $resultCC=$stmtCC->fetch();
	// $cod_cajachica=$resultCC['codigo'];
	// $monto_reembolso=$resultCC['monto_reembolso'];
	// $monto_a_rendir=$resultCC['monto'];
	// $monto_aux=$monto_a_rendir-$monto_A;
	// //------
	
	// $monto_reembolso=$monto_reembolso+$monto_aux;

	// //actualizamos el monto de reeembolso de caja chica
	// $stmtCCUpdate = $dbhU->prepare("UPDATE caja_chica set monto_reembolso=$monto_reembolso where codigo=$cod_cajachica");
	// $stmtCCUpdate->execute();

	// //actualizamos estado en cajachjicadetalle
	// $sqlCCD="UPDATE caja_chicadetalle set cod_estado=2,monto_rendicion=$monto_A where codigo=$codigo_detRendicionE";
	// $stmtCCD = $dbhU->prepare($sqlCCD);
	// $stmtCCD->execute();
	// //estado de rendicion 
	// $fecha_recepcion=date("Y-m-d H:i:s");
	// $sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$monto_A where codigo=$codigo_rendicionA";
	// $stmtU = $dbhU->prepare($sql);
	// $stmtU->execute();



	
	
// }



// if($stmtU->execute()){
//       $result =1;
//     }
// echo $result;
// $dbhU=null;

?>
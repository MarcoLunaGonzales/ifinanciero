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

$cod_cajachica=$_POST['cod_cajachica'];
$cod_cajachicadetalle=$_POST['cod_cajachicadetalle'];
$cantidadFilas=$_POST["cantidad_filas"];


$monto_total_rendido=$_POST["monto_total"];
$monto_faltante=$_POST["monto_faltante"];

// echo "cantidadFilas: ".$cantidadFilas."<br>";
// echo "monto_total_rendido: ".$monto_total_rendido."<br>";
// echo "monto_faltante: ".$monto_faltante."<br>";
// echo "cod_cajachica: ".$cod_cajachica."<br>";
// echo "cod_cajachicadetalle: ".$cod_cajachicadetalle."<br>";

$cod_estadoreferencial=1;
for ($i=1;$i<=$cantidadFilas;$i++){
	$tipo_doc=$_POST['tipo_doc'.$i];	
	$numero_doc=$_POST['numero_doc'.$i];
	$fecha_doc=$_POST['fecha_doc'.$i];
	$monto_A=$_POST['monto_A'.$i];
	$observacionesA=$_POST['observacionesA'.$i];


		
	// echo "cod_tipo_documentoA: ".$tipo_doc."<br>";
	// echo "numero_doc: ".$numero_doc."<br>";
	// echo "fecha_doc: ".$fecha_doc."<br>";
	// echo "monto_A: ".$monto_A."<br>";
	// echo "observacionesA: ".$observacionesA."<br>";
	//insertamos rendicones_detalle
	$sql="INSERT INTO rendiciones_detalle(cod_rendicion,cod_tipodoccajachica,fecha_doc,nro_doc,monto,observaciones,cod_estadoreferencial) values(:cod_rendicion,:cod_tipodoccajachica,:fecha_doc,:nro_doc,:monto,:observaciones,:cod_estadoreferencial)";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_rendicion', $cod_cajachica);
	$stmtU->bindParam(':cod_tipodoccajachica', $tipo_doc);
	$stmtU->bindParam(':fecha_doc', $fecha_doc);
	$stmtU->bindParam(':nro_doc', $numero_doc);
	$stmtU->bindParam(':monto', $monto_A);
	$stmtU->bindParam(':observaciones', $observacionesA);
	$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
	$flagSuccess=$stmtU->execute();
	$ultimo = $dbhU->lastInsertId();
	//ingresamos imagen
	$stmt3 = $dbhU->prepare("INSERT INTO rendicionesdetalle_imagen(codigo,imagen) values (:codigo, :imagen)");
    $stmt3->bindParam(':codigo', $ultimo);
    $stmt3->bindParam(':imagen', $_FILES['image'.$i]['name']);//la url esta poniendo      
    $archivo = __DIR__.DIRECTORY_SEPARATOR."imagenes".DIRECTORY_SEPARATOR.$_FILES['image'.$i]['name'];       
    if (move_uploaded_file($_FILES['image'.$i]['tmp_name'], $archivo))
        echo "correcto";
    else
        echo "Sin imagen".$_FILES["image".$i]["error"];//sale error 0
   	$stmt3->execute();

}
	$stmtCC = $dbhU->prepare("SELECT cc.codigo,cc.monto_reembolso,ccd.monto
	from  caja_chicadetalle ccd,caja_chica cc
	where ccd.cod_cajachica=cc.codigo and ccd.codigo=$cod_cajachicadetalle");
	$stmtCC->execute();
	$resultCC=$stmtCC->fetch();
	$cod_cajachica=$resultCC['codigo'];
	$monto_reembolso=$resultCC['monto_reembolso'];
	$monto_a_rendir=$resultCC['monto'];
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
	$sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$monto_total_rendido where codigo=$cod_cajachicadetalle";
	$stmtUR = $dbhU->prepare($sql);
	$flagSuccess=$stmtUR->execute();

if($flagSuccess){
	showAlertSuccessError(true,"../".$urlListDetalleCajaChica.'&codigo='.$cod_cajachica.'&cod_tcc='.$cod_cajachicadetalle);	
}else{
	showAlertSuccessError(false,"../".$urlListDetalleCajaChica.'&codigo='.$cod_cajachica.'&cod_tcc='.$cod_cajachicadetalle);

}



?>
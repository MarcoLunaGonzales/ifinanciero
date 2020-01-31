<?php
require_once '../conexion.php';

require_once '../functions.php';
require_once '../perspectivas/configModule.php';

$result=0;


$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();
$stmtU=false;

//RECIBIMOS LAS VARIABLES
$codigo_detRendicionE=$_POST['codigo_detRendicionE'];
$codigo_rendicionA=$_POST['codigo_rendicionA'];
$cod_tipo_documentoA=$_POST['cod_tipo_documentoA'];
$numero_doc=$_POST['numero_doc'];
$fecha_doc=$_POST['fecha_doc'];
$monto_A=$_POST['monto_A'];
$observacionesA=$_POST['observacionesA'];
$cod_estadoreferencial=$_POST['cod_estadoreferencial'];
// echo "codigo_rendicionA: ".$codigo_rendicionA."<br>";
// echo "cod_tipo_documentoA: ".$cod_tipo_documentoA."<br>";
// echo "numero_doc: ".$numero_doc."<br>";
// echo "fecha_doc: ".$fecha_doc."<br>";
// echo "monto_A: ".$monto_A."<br>";
// echo "observacionesA: ".$observacionesA."<br>";
// echo "cod_estadoreferencial: ".$cod_estadoreferencial."<br>";

// $fecha_recepcion=date("Y-m-d H:i:s");
	//cuando devuelve AF
	// Prepare
if($cod_estadoreferencial==1){//insertar	
	$sql="INSERT INTO rendiciones_detalle(cod_rendicion,cod_tipodoccajachica,fecha_doc,nro_doc,monto,observaciones,cod_estadoreferencial) values(:cod_rendicion,:cod_tipodoccajachica,:fecha_doc,:nro_doc,:monto,:observaciones,:cod_estadoreferencial)";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_rendicion', $codigo_rendicionA);
	$stmtU->bindParam(':cod_tipodoccajachica', $cod_tipo_documentoA);
	$stmtU->bindParam(':fecha_doc', $fecha_doc);
	$stmtU->bindParam(':nro_doc', $numero_doc);
	$stmtU->bindParam(':monto', $monto_A);
	$stmtU->bindParam(':observaciones', $observacionesA);
	$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
	
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE rendiciones_detalle set cod_tipodoccajachica=:cod_tipodoccajachica,fecha_doc=:fecha_doc,nro_doc=:nro_doc,monto=:monto,observaciones=:observaciones where codigo=:codigo";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':codigo', $codigo_detRendicionE);
	$stmtU->bindParam(':cod_tipodoccajachica', $cod_tipo_documentoA);
	$stmtU->bindParam(':fecha_doc', $fecha_doc);
	$stmtU->bindParam(':nro_doc', $numero_doc);
	$stmtU->bindParam(':monto', $monto_A);
	$stmtU->bindParam(':observaciones', $observacionesA);


}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE rendiciones_detalle set cod_estadoreferencial=2 where codigo=:codigo";
	$stmtU = $dbhU->prepare($sql);
	// Bind	
	$stmtU->bindParam(':codigo', $codigo_detRendicionE);	
}elseif($cod_estadoreferencial==4){

	//actualizamos estado en cajachjicadetalle
	$sqlCCD="UPDATE caja_chicadetalle set cod_estado=2 where codigo=$codigo_detRendicionE";
	$stmtCCD = $dbhU->prepare($sqlCCD);
	$stmtCCD->execute();
	//estado de rendicion 
	$fecha_recepcion=date("Y-m-d H:i:s");
	$sql="UPDATE rendiciones set fecha='$fecha_recepcion',cod_estado=2,monto_rendicion=$monto_A where codigo=$codigo_rendicionA";
	$stmtU = $dbhU->prepare($sql);




	
	
}



if($stmtU->execute()){
      $result =1;
    }
echo $result;
$dbhU=null;

?>
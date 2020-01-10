<?php
require_once '../conexion.php';
require_once '../functions.php';

$result=0;
$dbhU = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbhU->prepare($sqlX);
$stmtX->execute();

//RECIBIMOS LAS VARIABLES
$cod_contrato=$_POST['cod_contrato'];
$cod_personal=$_POST['cod_personal'];
$cod_tipocontrato=$_POST['cod_tipocontrato'];
$fecha_inicio=$_POST['fecha_inicio'];
$cod_estadoreferencial=$_POST['cod_estadoreferencial'];


$stmtContrato = $dbhU->prepare("SELECT * from tipos_contrato_personal where codigo=:codigo");
$stmtContrato->bindParam(':codigo',$cod_tipocontrato);
$stmtContrato->execute();
$resultC=$stmtContrato->fetch();
$meses_alerta=$resultC['meses_alerta'];
$nombre_tipo_contrato=$resultC['nombre'];



if($nombre_tipo_contrato=="CONTRATO INDEFINIDO"){
	$fecha_fincontrato="INDEFINIDO";
	//dividimos la fecha de inicio
	$porciones = explode("-", $fecha_inicio);
	$anio= $porciones[0]; // porción1
	$mes= $porciones[1]; // porción2
	$dia=$porciones[2];
	for ($i=0; $i < $meses_alerta; $i++) { 
		if($mes<12){
			$mes=$mes+1;
		}else{
			$mes=1;
			$anio=$anio+1;
		}
	}
	$fecha_evaluacioncontrato=$anio."-".$mes."-".$dia;

}else{
	//dividimos la fecha de inicio
	$porciones = explode("-", $fecha_inicio);
	$anio= $porciones[0]; // porción1
	$mes= $porciones[1]; // porción2
	$dia=$porciones[2];

	// echo "fecha: ".$fecha_inicio."<br>";
	// echo "añoA: ".$anio."<br>";
	// echo "mesA: ".$mes."<br>";
	// echo "duracionA: ".$meses_alerta."<br>";

	for ($i=0; $i < $meses_alerta; $i++) { 
		if($mes<12){
			$mes=$mes+1;
		}else{
			$mes=1;
			$anio=$anio+1;
		}
	}
	$fecha_fincontrato=$anio."-".$mes."-".$dia;
	$fecha_evaluacioncontrato=$fecha_fincontrato;
}


// echo "añoB: ".$anio."<br>";
// echo "mesB: ".$mes."<br>";
// $fecha_recepcion=date("Y-m-d H:i:s");
// echo "llega: ".$fecha_inicio."<br>";
	// Prepare
if($cod_estadoreferencial==1){//insertar
	$sql="INSERT INTO personal_contratos(cod_personal,cod_tipocontrato,fecha_iniciocontrato,fecha_fincontrato,fecha_evaluacioncontrato,cod_estadoreferencial) values(:cod_personal,:cod_tipocontrato,:fecha_iniciocontrato,:fecha_fincontrato,:fecha_evaluacioncontrato,:cod_estadoreferencial) ";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_personal', $cod_personal);
	$stmtU->bindParam(':cod_tipocontrato', $cod_tipocontrato);
	$stmtU->bindParam(':fecha_iniciocontrato', $fecha_inicio);
	$stmtU->bindParam(':fecha_fincontrato', $fecha_fincontrato);
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_evaluacioncontrato);
	$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE personal_contratos set cod_tipocontrato=:cod_tipocontrato,fecha_iniciocontrato=:fecha_iniciocontrato,fecha_fincontrato=:fecha_fincontrato,fecha_evaluacioncontrato=:fecha_evaluacioncontrato where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_contrato', $cod_contrato);
	$stmtU->bindParam(':cod_tipocontrato', $cod_tipocontrato);
	$stmtU->bindParam(':fecha_iniciocontrato', $fecha_inicio);
	$stmtU->bindParam(':fecha_fincontrato', $fecha_fincontrato);
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_evaluacioncontrato);


}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE personal_contratos set cod_estadoreferencial=2 where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	// Bind	
	$stmtU->bindParam(':cod_contrato', $cod_contrato);	
}
if($stmtU->execute()){
      $result =1;
    }
echo $result;
$dbhU=null;

?>
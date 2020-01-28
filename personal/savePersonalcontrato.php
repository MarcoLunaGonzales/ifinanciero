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
$observaciones=$_POST['observaciones'];


$stmtContrato = $dbhU->prepare("SELECT * from tipos_contrato_personal where codigo=:codigo");
$stmtContrato->bindParam(':codigo',$cod_tipocontrato);
$stmtContrato->execute();
$resultC=$stmtContrato->fetch();
$nombre_tipo_contrato=$resultC['nombre'];
$duracion_meses=$resultC['duracion_meses'];
//SACAMOS LOS VALORES DE CONFIGURACION
$stmtConfig = $dbhU->prepare("SELECT * from configuraciones where id_configuracion in (11,12)");
$stmtConfig->execute();
$stmtConfig->bindColumn('valor_configuracion', $valor_configuracion);	
$stmtConfig->bindColumn('id_configuracion', $id_configuracion);
while ($row = $stmtConfig->fetch(PDO::FETCH_BOUND)) {
	switch ($id_configuracion) {
		case 11:
			$val_conf_dias_alerta_def=$valor_configuracion;
			break;
		case 12:
			$val_conf_meses_alerta_indef=$valor_configuracion;
			break;
		
		default:
			# code...
			break;
	}
}

if($nombre_tipo_contrato=="CONTRATO INDEFINIDO"){
	//dividimos la fecha de inicio
	$porciones = explode("-", $fecha_inicio);
	$anio= $porciones[0]; // porción1
	$mes= $porciones[1]; // porción2
	$dia=$porciones[2];

	if($dia=28||$dia=29||$dia=30||$dia=31)
		
	for ($i=0; $i < $val_conf_meses_alerta_indef; $i++) { 
		if($mes<12){
			$mes=$mes+1;
		}else{
			$mes=1;
			$anio=$anio+1;
		}
	}
	$fecha_fincontrato="INDEFINIDO";
	$fecha_fincontrato_x=$anio."-".$mes."-01";
	switch ($mes) {
		case 2:
			if($dia==29||$dia==30||$dia==31){
				$dia=date('t',strtotime($fecha_fincontrato_x));
			}
		break;
		case 04||06||09||11:
			if($dia==30||$dia==31){
				$dia=date('t',strtotime($fecha_fincontrato_x));				
			}
		break;
		
		default:
			# code...
			break;
	}
	$fecha_evaluacioncontrato=$anio."-".$mes."-".$dia;
	// echo "añoB: ".$anio."<br>";
	// echo "mesB: ".$mes."<br>";
	 // echo "se va: ".$fecha_evaluacioncontrato."<br>";
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

	for ($i=0; $i < $duracion_meses; $i++) {
		if($mes<12){
			$mes=$mes+1;
		}else{
			$mes=1;
			$anio=$anio+1;
		}
	}
	$fecha_fincontrato=$anio."-".$mes."-01";
	switch ($mes) {
		case 2:
			if($dia==29||$dia==30||$dia==31){
				$dia=date('t',strtotime($fecha_fincontrato));
			}
		break;
		case 04||06||09||11:
			if($dia==30||$dia==31){
				$dia=date('t',strtotime($fecha_fincontrato));				
			}
		break;
		
		default:
			# code...
			break;
	}
	$fecha_fincontrato=$anio."-".$mes."-".$dia;
	$dia_a=$dia;$meses_a=$mes;$anio_a=$anio;
	
	// echo "prueba:".date('t',strtotime('2020-02-02'));
	for ($j=0; $j < $val_conf_dias_alerta_def; $j++) {
		if($dia_a>1){
			$dia_a=$dia_a-1;
		}else{
			$dia_a=date('t',strtotime($fecha_fincontrato));
			if($meses_a>1){
				$meses_a=$meses_a-1;
			}else{
				$meses_a=12;
				$anio_a=$anio_a-1;
			}		
		}
	}
	$fecha_evaluacioncontrato=$anio_a."-".$meses_a."-".$dia_a;
}
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
}elseif ($cod_estadoreferencial==4) {//actualizar fecha evaluacion
	$sql="UPDATE personal_contratos set fecha_evaluacioncontrato=:fecha_evaluacioncontrato where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$stmtU->bindParam(':cod_contrato', $cod_contrato);	
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_inicio);	
}elseif ($cod_estadoreferencial==5) {//retirar personal
	//echo "personal:".$cod_personal."- fecha :".$fecha_inicio."-cod_tipocontrato :".$cod_tipocontrato."-ober:".$observaciones;
	
	$cod_estadoreferencial=1;
	$cod_estadoreferencialPersonal=2;
	$cod_estadopersonal=3;
	$sqlpersonal="UPDATE personal set cod_estadopersonal=$cod_estadopersonal,cod_estadoreferencial=$cod_estadoreferencialPersonal where codigo=$cod_personal";
	$stmtUP = $dbhU->prepare($sqlpersonal);
	$stmtUP->execute();

	//finalizamos todos sus contratos
	$sqlBPC="SELECT codigo from personal_contratos where cod_personal=$cod_personal ORDER BY codigo desc";
	$stmtBPC = $dbhU->prepare($sqlBPC);
	$stmtBPC->execute();
	$resultBPC=$stmtBPC->fetch();
	$codigo_contrato=$resultBPC['codigo'];

	$sqlUPC="UPDATE personal_contratos set fecha_fincontrato='$fecha_inicio',fecha_evaluacioncontrato='$fecha_inicio' where codigo=$codigo_contrato";
	$stmtUPC = $dbhU->prepare($sqlUPC);
	$stmtUPC->execute();

	//insertamos el codigo de personal retirado

	$sql="INSERT INTO personal_retiros(cod_personal,cod_tiporetiro,fecha_retiro,observaciones,cod_estadoreferencial) values($cod_personal,$cod_tipocontrato,'$fecha_inicio','$observaciones',$cod_estadoreferencial)";
	$stmtU = $dbhU->prepare($sql);
}
if($stmtU->execute()){
      $result =1;
    }
echo $result;
$dbhU=null;

?>

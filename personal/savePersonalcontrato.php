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
	// $porciones = explode("-", $fecha_inicio);
	// $anio= $porciones[0]; // porción1
	// $mes= $porciones[1]; // porción2
	// $dia=$porciones[2];

	// // if($dia=28||$dia=29||$dia=30||$dia=31)
		
	// for ($i=0; $i < $val_conf_meses_alerta_indef; $i++) { 
	// 	if($mes<12){
	// 		$mes=$mes+1;
	// 	}else{
	// 		$mes=1;
	// 		$anio=$anio+1;
	// 	}
	// }
	$fecha_fincontrato="INDEFINIDO";
	// $fecha_fincontrato_x=$anio."-".$mes."-01";
	// switch ($mes) {
	// 	case 2:
	// 		if($dia==29||$dia==30||$dia==31){
	// 			$dia=date('t',strtotime($fecha_fincontrato_x));
	// 		}
	// 	break;
	// 	case 04||06||09||11:
	// 		if($dia==30||$dia==31){
	// 			$dia=date('t',strtotime($fecha_fincontrato_x));				
	// 		}
	// 	break;
		
	// 	default:
	// 		# code...
	// 		break;
	// }
	// $fecha_actual = "2020-06-01";
	// $mese_au=3;
	//sumo 1 día
	$fecha_evaluacioncontrato_x= date("Y-m-d",strtotime($fecha_inicio."+ ".$val_conf_meses_alerta_indef." month")); 
	$fecha_evaluacioncontrato = date("Y-m-d",strtotime($fecha_evaluacioncontrato_x."- 1 days")); 
	// echo $nuevafecha_x;

	// $fecha_evaluacioncontrato=$anio."-".$mes."-".$dia;
	// echo "añoB: ".$anio."<br>";
	// echo "mesB: ".$mes."<br>";
	 // echo "se va: ".$fecha_evaluacioncontrato."<br>";
}else{
	//dividimos la fecha de inicio
	// $porciones = explode("-", $fecha_inicio);
	// $anio= $porciones[0]; // porción1
	// $mes= $porciones[1]; // porción2
	// $dia=$porciones[2];

	// // echo "fecha: ".$fecha_inicio."<br>";
	// // echo "añoA: ".$anio."<br>";
	// // echo "mesA: ".$mes."<br>";
	// // echo "duracionA: ".$meses_alerta."<br>";

	// for ($i=0; $i < $duracion_meses; $i++) {
	// 	if($mes<12){
	// 		$mes=$mes+1;
	// 	}else{
	// 		$mes=1;
	// 		$anio=$anio+1;
	// 	}
	// }
	// $fecha_fincontrato=$anio."-".$mes."-01";
	// switch ($mes) {
	// 	case 2:
	// 		if($dia==29||$dia==30||$dia==31){
	// 			$dia=date('t',strtotime($fecha_fincontrato));
	// 		}
	// 	break;
	// 	case 04||06||09||11:
	// 		if($dia==30||$dia==31){
	// 			$dia=date('t',strtotime($fecha_fincontrato));				
	// 		}
	// 	break;
		
	// 	default:
	// 		# code...
	// 		break;
	// }
	// $fecha_fincontrato=$anio."-".$mes."-".$dia;
	// $dia_a=$dia;$meses_a=$mes;$anio_a=$anio;
	
	// // echo "prueba:".date('t',strtotime('2020-02-02'));
	// for ($j=0; $j < $val_conf_dias_alerta_def; $j++) {
	// 	if($dia_a>1){
	// 		$dia_a=$dia_a-1;
	// 	}else{
	// 		$dia_a=date('t',strtotime($fecha_fincontrato));
	// 		if($meses_a>1){
	// 			$meses_a=$meses_a-1;
	// 		}else{
	// 			$meses_a=12;
	// 			$anio_a=$anio_a-1;
	// 		}		
	// 	}
	// }
	// $fecha_evaluacioncontrato=$anio_a."-".$meses_a."-".$dia_a;


	$fecha_fincontrato_x= date("Y-m-d",strtotime($fecha_inicio."+ ".$duracion_meses." month")); 
	$fecha_fincontrato = date("Y-m-d",strtotime($fecha_fincontrato_x."- 1 days")); 
	
	$fecha_evaluacioncontrato= date("Y-m-d",strtotime($fecha_fincontrato."- ".$val_conf_dias_alerta_def." days")); 
}
	// Prepare
if($cod_estadoreferencial==1){//insertar
	//verificamos que no exita un contrato abierto
	$sqlControlador="SELECT codigo,cod_estadocontrato from personal_contratos where cod_personal=$cod_personal ORDER BY codigo desc";
	$stmtControlador = $dbhU->prepare($sqlControlador);
	$stmtControlador->execute();
	$resultControlador=$stmtControlador->fetch();
	$cod_contrato_aux=$resultControlador['codigo'];
	$cod_estadocontrato_aux=$resultControlador['cod_estadocontrato'];
	if($cod_estadocontrato_aux==2 || $cod_estadocontrato_aux==null){
		$cod_estadocontrato=1;
		$sql="INSERT INTO personal_contratos(cod_personal,cod_tipocontrato,fecha_iniciocontrato,fecha_fincontrato,fecha_evaluacioncontrato,cod_estadoreferencial,cod_estadocontrato) values(:cod_personal,:cod_tipocontrato,:fecha_iniciocontrato,:fecha_fincontrato,:fecha_evaluacioncontrato,:cod_estadoreferencial,:cod_estadocontrato) ";
		$stmtU = $dbhU->prepare($sql);
		// Bind
		$stmtU->bindParam(':cod_personal', $cod_personal);
		$stmtU->bindParam(':cod_tipocontrato', $cod_tipocontrato);
		$stmtU->bindParam(':fecha_iniciocontrato', $fecha_inicio);
		$stmtU->bindParam(':fecha_fincontrato', $fecha_fincontrato);
		$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_evaluacioncontrato);
		$stmtU->bindParam(':cod_estadoreferencial', $cod_estadoreferencial);
		$stmtU->bindParam(':cod_estadocontrato', $cod_estadocontrato);
		$flagsucces=$stmtU->execute();
	}else{
		$flagsucces=false;
		$result =2;

	}

	
}elseif($cod_estadoreferencial==2){//actualizar
	$sql="UPDATE personal_contratos set cod_tipocontrato=:cod_tipocontrato,fecha_iniciocontrato=:fecha_iniciocontrato,fecha_fincontrato=:fecha_fincontrato,fecha_evaluacioncontrato=:fecha_evaluacioncontrato where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	// Bind
	$stmtU->bindParam(':cod_contrato', $cod_contrato);
	$stmtU->bindParam(':cod_tipocontrato', $cod_tipocontrato);
	$stmtU->bindParam(':fecha_iniciocontrato', $fecha_inicio);
	$stmtU->bindParam(':fecha_fincontrato', $fecha_fincontrato);
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_evaluacioncontrato);
	$flagsucces=$stmtU->execute();
}elseif ($cod_estadoreferencial==3) {//eliminar
	$sql="UPDATE personal_contratos set cod_estadoreferencial=2 where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$stmtU->bindParam(':cod_contrato', $cod_contrato);
	$flagsucces=$stmtU->execute();	
}elseif ($cod_estadoreferencial==4) {//actualizar fecha evaluacion
	$sql="UPDATE personal_contratos set fecha_evaluacioncontrato=:fecha_evaluacioncontrato where codigo=:cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$stmtU->bindParam(':cod_contrato', $cod_contrato);	
	$stmtU->bindParam(':fecha_evaluacioncontrato', $fecha_inicio);
	$flagsucces=$stmtU->execute();	
}elseif ($cod_estadoreferencial==5) {//retirar personal
	//echo "personal:".$cod_personal."- fecha :".$fecha_inicio."-cod_tipocontrato :".$cod_tipocontrato."-ober:".$observaciones;
	
	
	$cod_estadoreferencial=1;
	$cod_estadoreferencialPersonal=2;
	$cod_estadopersonal=3;
	
	//verificamos si todos sus contratos estan fina,izados
	$sqlControlador="SELECT codigo,cod_estadocontrato from personal_contratos where cod_personal=$cod_personal ORDER BY codigo desc";
	$stmtControlador = $dbhU->prepare($sqlControlador);
	$stmtControlador->execute();
	$resultControlador=$stmtControlador->fetch();
	$cod_contrato_aux=$resultControlador['codigo'];
	$cod_estadocontrato_aux=$resultControlador['cod_estadocontrato'];
	if($cod_estadocontrato_aux==2){
		

		$sql="INSERT INTO personal_retiros(cod_personal,cod_tiporetiro,fecha_retiro,observaciones,cod_estadoreferencial) values($cod_personal,$cod_tipocontrato,'$fecha_inicio','$observaciones',$cod_estadoreferencial)";
		$stmtU = $dbhU->prepare($sql);
		$flagsucces=$stmtU->execute();
		if($flagsucces){
			$sqlpersonal="UPDATE personal set cod_estadopersonal=$cod_estadopersonal,cod_estadoreferencial=$cod_estadoreferencialPersonal where codigo=$cod_personal";
			$stmtUP = $dbhU->prepare($sqlpersonal);
			$stmtUP->execute();
		}
	}else{
		$flagsucces=false;
		$result=2;
	}

	//insertamos el codigo de personal retirado

	
}else{//finalizar contrato
	$cod_estadocontrato=2;
	$fecha_finalizado=date("Y-m-d H:i:s");
	$sql="UPDATE personal_contratos set cod_estadocontrato=$cod_estadocontrato,fecha_finalizado='$fecha_finalizado' where codigo=$cod_contrato";
	$stmtU = $dbhU->prepare($sql);
	$flagsucces=$stmtU->execute();
}
if($flagsucces){
      $result =1;
 }
echo $result;
$dbhU=null;

?>

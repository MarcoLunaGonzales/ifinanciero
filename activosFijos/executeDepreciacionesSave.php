<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//la ufv tengo q obtener de la funcion 

//TENGO Q AVERIGUAR EL PRIMER Y ULTIMO DIA DEL MES
//$fecha = '2010-02-04';
$fecha = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA

// First day of the month.
$fecha_primerdia = date('Y-m-01', strtotime($fecha));
// Last day of the month.
$fecha_ultimodia = date('Y-m-t', strtotime($fecha));


//$cod_empresa=$_POST["cod_empresa"];
$mes=$_POST["mes"];
$gestion=$_POST["gestion"];
//verificamos si esa fecha no se registro aun

$stmt = $dbh->prepare("SELECT codigo from mesdepreciaciones where gestion=$gestion and mes=$mes");
	//ejecutamos
	$stmt->execute();
	//bindColumn
	$result=$stmt->fetch();
	$codigo_aux=$result['codigo'];
	if($codigo_aux==null)
	{
		//verificamos si se salta algun mes
		$stmt2 = $dbh->prepare("SELECT mes,gestion from mesdepreciaciones  order by codigo desc limit 1");
		$stmt2->execute();
		$result2=$stmt2->fetch();
		$mes_aux=$result2['mes'];
		$gestion_aux=$result2['gestion'];
		
		if($mes_aux==12){
			$mes_aux=1;
		}else{
			if($mes_aux==null)
				$mes_aux=0;
			else $mes_aux=$mes_aux+1;
		}
		if($mes_aux==$mes || $mes_aux==0){//no se salto ningun mes
			//$ufvinicio=$_POST["ufvinicio"];
		$ufvinicio=obtenerUFV($fecha_primerdia);
		//$ufvfinal=$_POST["ufvfinal"];
		$ufvfinal=obtenerUFV($fecha_ultimodia);
		$estado=1;
		//Prepare
		$stmt = $dbh->prepare("call crear_depreciacion_mensual(:mes, :gestion, :ufvinicio, :ufvfinal)");
		$stmt->bindParam(':mes', $mes);
		$stmt->bindParam(':gestion', $gestion);
		$stmt->bindParam(':ufvinicio', $ufvinicio);
		$stmt->bindParam(':ufvfinal', $ufvfinal);
		$flagSuccess=$stmt->execute();
		showAlertSuccessErrorDepreciaciones($flagSuccess,$urlList7);

		}else{//se esta saltando un mes de depreciacion
			$flagSuccess=false;
			showAlertSuccessErrorDepreciaciones2($flagSuccess,$urlList7);
		}
		
		
	}else{
		$flagSuccess=false;
		showAlertSuccessErrorDepreciaciones($flagSuccess,$urlRegistrar7);
	}
	

?>

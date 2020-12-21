<?php

//require_once '../layouts/bodylogin.php';
require_once 'conexion.php';
require_once 'functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

//la ufv tengo q obtener de la funcion 
//$cod_empresa=$_POST["cod_empresa"];
$mes=$_POST["mes"];
$gestion=$_POST["gestion"];


$ufvinicio=$_POST["ufv_inicio"];
$ufvfinal=$_POST["ufv_fin"];


//verificamos si esa fecha no se registro aun

$sql="SELECT count(codigo)as contador from mesdepreciaciones where gestion=$gestion and mes=$mes";
$stmt = $dbh->prepare($sql);
// echo $sql;
	//ejecutamos
	$stmt->execute();
	//bindColumn
	$result=$stmt->fetch();
	$codigo_aux=$result['contador'];
	
	//echo "codAux : ".$codigo_aux;

	if($codigo_aux==0)
	{
		//echo "entro correcto 1";
		$stmt2 = $dbh->prepare("SELECT mes,gestion from mesdepreciaciones  order by codigo desc limit 1");
		$stmt2->execute();
		$result2=$stmt2->fetch();
		$mes_aux=$result2['mes'];
		$gestion_aux=$result2['gestion'];
		
		if($mes_aux==12){
			$mes_aux=1;
			// $mes_aux+=$mes;
		}else{
			if($mes_aux==null || $mes_aux==""){
				$mes_aux=0;
				// $fecha_depre = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA
				// $fecha_depre_ant = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA
			}else{ 
				$mes_aux=$mes_aux;
				// $fecha_depre = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA
				// $fecha_depre_ant = $gestion_aux.'-'.$mes_aux.'-01';//ARMO UNA FECHA
			};
		}
		// echo "mesAux: ".$mes_aux;
		if($mes>$mes_aux || $mes_aux==0){//no se salto ningun mes
			//TENGO Q AVERIGUAR EL PRIMER Y ULTIMO DIA DEL MES
			//$fecha = '2010-02-04';
			
			// First day of the month.			
			// $fecha_primerdia = date('Y-m-t', strtotime($fecha_depre_ant));
			// // Last day of the month.
			// $fecha_ultimodia = date('Y-m-t', strtotime($fecha_depre));

			// $ufvinicio=obtenerUFV($fecha_primerdia);
			// $ufvfinal=obtenerUFV($fecha_ultimodia);
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
		//echo "entro falso 1";
		$flagSuccess=false;
		showAlertSuccessErrorDepreciaciones($flagSuccess,$urlRegistrar7);
	}
	

?>

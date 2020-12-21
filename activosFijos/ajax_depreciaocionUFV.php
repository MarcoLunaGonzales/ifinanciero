<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

// $codigo_UO=$_GET["codigo_UO"];
// $db = new Conexion();

$dbh = new Conexion();
$stmt2 = $dbh->prepare("SELECT mes,gestion from mesdepreciaciones  order by codigo desc limit 1");
$stmt2->execute();
$result2=$stmt2->fetch();
$mes_aux=$result2['mes'];
$gestion_aux=$result2['gestion'];
if($mes_aux==null || $mes_aux==""){	
	//$fecha_depre = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA
	$fecha_depre_ant = $_GET["gestion"].'-'.$_GET["mes"].'-01';//ARMO UNA FECHA
}else{ 	
	//$fecha_depre = $_POST["gestion"].'-'.$_POST["mes"].'-01';//ARMO UNA FECHA
	$fecha_depre_ant = $gestion_aux.'-'.$mes_aux.'-01';//ARMO UNA FECHA
};



// $fecha = $_GET["gestion"].'-'.$_GET["mes"].'-01';//ARMO UNA FECHA
// $fecha_primerdia = date('Y-m-01', strtotime($fecha));
// $fecha_ultimodia = date('Y-m-t', strtotime($fecha));
$fecha_primerdia = date('Y-m-01', strtotime($fecha_depre_ant));
$fecha_aux = date('d/m/Y', strtotime($fecha_primerdia));

$ufvinicio=obtenerUFV($fecha_primerdia);    
// $ufvfinal=obtenerUFV($fecha_ultimodia);
// echo $ufvinicio."###"$ufvfinal;
?>

<input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="ufv_inicio" id="ufv_inicio" value="<?=$ufvinicio?>  (<?=$fecha_aux?>)" />
<!-- <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?=$fecha_primerdia?>" /> -->
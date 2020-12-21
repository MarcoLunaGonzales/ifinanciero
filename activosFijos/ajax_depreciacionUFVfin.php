<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once 'configModule.php';

// $codigo_UO=$_GET["codigo_UO"];
// $db = new Conexion();

$fecha = $_GET["gestion"].'-'.$_GET["mes"].'-01';//ARMO UNA FECHA
//$fecha_primerdia = date('Y-m-01', strtotime($fecha));
$fecha_ultimodia = date('Y-m-t', strtotime($fecha));
$fecha_aux = date('d/m/Y', strtotime($fecha_ultimodia));
//$ufvinicio=obtenerUFV($fecha_primerdia);    
$ufvfinal=obtenerUFV($fecha_ultimodia);
// echo $ufvinicio."###"$ufvfinal;
?>

<input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="ufv_fin" id="ufv_fin" value="<?=$ufvfinal?> (<?=$fecha_aux?>)" />
<!-- <input type="text"  readonly="readonly" style="padding-left:20px" class="form-control" name="fecha_fin" id="fecha_fin" value="<?=$fecha_ultimodia?>" /> -->
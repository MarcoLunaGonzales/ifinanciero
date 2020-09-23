<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();
$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
set_time_limit(300);

$oficina=$_GET['oficina'];
$area=$_GET['area'];

?><option disabled selected value="">--Seleccione--</option><?php   
//validacion si no retenciones
$sqlServicios="SELECT IdServicio,Descripcion from servicios where IdOficina=$oficina and IdArea=$area";
$stmt = $dbh->prepare($sqlServicios);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['IdServicio'];
	$descripcionX=$row['Descripcion'];
    ?>
    <option value="<?=$codigoX?>"><?=$descripcionX?></option>
    <?php
}


 //print_r($lista);

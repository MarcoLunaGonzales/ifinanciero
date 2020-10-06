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
$sqlServicios="SELECT IdServicio,Descripcion,Codigo,IdCliente from servicios where IdOficina=$oficina and IdArea=$area and IdEstado=204 order by Codigo";
$stmt = $dbh->prepare($sqlServicios);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$row['IdServicio'];
	$descripcionX=$row['Descripcion'];
	$codigoDesX=$row['Codigo'];
	$nombreClienteX=nameCliente($row['IdCliente']);
    ?>
    <option value="<?=$codigoX?>"><?=$codigoDesX?> - <?=strtoupper($descripcionX)?> (<?=$nombreClienteX?>)</option>
    <?php
}


 //print_r($lista);

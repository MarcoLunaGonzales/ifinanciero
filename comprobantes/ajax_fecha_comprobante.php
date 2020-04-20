<?php
require_once '../conexion.php';
require_once 'configModule.php';


$tipoComprobante=$_GET["tipo_comprobante"];
$fecha=$_GET["fecha"];

$db = new Conexion();
$sqlUO="SELECT fecha from comprobantes where cod_tipocomprobante=$tipoComprobante ORDER BY codigo desc limit 1";
$stmt = $db->prepare($sqlUO);
$stmt->execute();
while ($row = $stmt->fetch()){
	$fecha=$row['fecha'];
}
 // $fechaMin = date_format($fecha, 'Y-m-d');
$fechaMin = date("Y-m-d",strtotime($fecha."+ 1 days"));
 
?>
<input class="form-control" type="date" name="fecha" min="<?=$fechaMin?>" value="<?=$fechaMin?>" id="fecha" required/>

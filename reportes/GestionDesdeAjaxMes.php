<?php
require_once '../conexion.php';

$cod_gestion = $_GET["cod_gestion"];
$db = new Conexion();
$stmt = $db->prepare("SELECT nombre from gestiones where codigo=$cod_gestion");
$stmt->execute();
$result=$stmt->fetch();
$nombre_gestion=$result['nombre'];

$mes=date("m");
$mesActual=date("m",(mktime(0,0,0,$mes,1,$nombre_gestion)-1));
$diaActual=date("d",(mktime(0,0,0,$mes,1,$nombre_gestion)-1));
$fechaDesde=$nombre_gestion."-".$mesActual."-01";
$fechaHasta=$nombre_gestion."-".$mesActual."-".$diaActual;

?>
	<input type="date" class="form-control" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaDesde?>" required="true">	 

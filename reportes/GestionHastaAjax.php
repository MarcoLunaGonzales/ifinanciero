<?php
require_once '../conexion.php';

$cod_gestion = $_GET["cod_gestion"];
$db = new Conexion();
$stmt = $db->prepare("SELECT nombre from gestiones where codigo=$cod_gestion");
$stmt->execute();
$result=$stmt->fetch();
$nombre_gestion=$result['nombre'];

$fechaDesde=$nombre_gestion."-01-01";
$fechaHasta=$nombre_gestion."-12-31";
 ?>
	<input type="date" class="form-control" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaHasta?>" required="true">	
 

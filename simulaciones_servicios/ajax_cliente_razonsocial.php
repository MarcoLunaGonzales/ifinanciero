<?php
require_once '../conexion.php';
require_once 'configModule.php';

//header('Content-Type: application/json');

$codigo_cliente = $_GET["codigo_cliente"];
//ini_set("display_errors", "1");
$db = new Conexion();

$stmt = $db->prepare("SELECT nombre FROM  clientes WHERE codigo=$codigo_cliente");
$stmt->execute();
$result = $stmt->fetch();
$nombre_cliente = $result['nombre'];
?>
<input class="form-control" type="text" name="razon_social" id="razon_social" required="true" value="<?=strtoupper($nombre_cliente);?>" onkeyup="javascript:this.value=this.value.toUpperCase();" required="true"/>
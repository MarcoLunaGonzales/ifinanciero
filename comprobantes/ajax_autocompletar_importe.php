<?php
require_once '../conexion.php';
require_once 'configModule.php';

$importe_x = $_GET["importe"];
$db = new Conexion();
?>
<input class="form-control" type="number" name="imp_fac" id="imp_fac" value="<?=$importe_x?>"/>
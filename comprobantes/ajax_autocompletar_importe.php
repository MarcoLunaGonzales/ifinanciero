<?php
require_once '../conexion.php';
require_once 'configModule.php';

$importe_x = $_GET["importe"];
$db = new Conexion();

$valorNoValido="Valor no Válido.";
?>
<input class="form-control" type="number" step="0.01" name="imp_fac" id="imp_fac" value="<?=$importe_x?>" required="true"/>
<div class="invalid-feedback"><?=$valorNoValido;?></div>
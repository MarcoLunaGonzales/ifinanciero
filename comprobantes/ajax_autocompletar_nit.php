<?php
require_once '../conexion.php';
require_once 'configModule.php';

$nit = $_GET["nit"];
$db = new Conexion();
?>
<input class="form-control" type="hidden" name="nit_fac" id="nit_fac" required="true" value="<?=$nit?>"/>
<?php
require_once '../styles.php';


$correos=$_GET['correos'];

?>

<input type="text" name="correo_destino" id="correo_destino"  value="<?=$correos?>" class="form-control" data-role="tagsinput" data-color="info" required="true" style="background-color:#FFFFFF">  
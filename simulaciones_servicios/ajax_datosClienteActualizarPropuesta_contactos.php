<?php

require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';

$dbh = new Conexion();

$codigo_cliente=$_GET['codigo_cliente'];

?>

<select name="select_contactos" id="select_contactos" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" >
<?php 
$query_contactos = "SELECT codigo,nombre,paterno,materno,cargo,telefono,correo from clientes_contactos
  where cod_cliente=$codigo_cliente and  cod_estadoreferencial=1";
$stmtContactos = $dbh->query($query_contactos);
while ($row = $stmtContactos->fetch()){ ?>
<option value="<?=$row["codigo"];?>"><?=$row["nombre"];?> <?=$row["paterno"];?> <?=$row["materno"];?></option>
<?php } ?>
</select>

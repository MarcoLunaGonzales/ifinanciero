<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$check_var=$_GET['check_var'];
// $sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
// $stmt = $dbh->prepare($sqlUO);
// $stmt->execute();

if($check_var==1){?>	
	<input type="text" name="razon_social" id="razon_social" class="form-control" style="background-color: #cec6d6;" required="">
<?php }
?>




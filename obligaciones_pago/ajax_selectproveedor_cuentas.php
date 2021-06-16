<?php
session_start();
require_once '../conexion.php';
require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();
$cod_cuenta=$_GET['cod_cuenta'];


?>

<select class="selectpicker form-control form-control-sm"  data-live-search="true" name="proveedor" id="proveedor" data-style="btn btn-primary">
	<option selected="selected" value="####">--PROVEEDOR--</option>
	<?php 
		//$sql="SELECT e.cod_proveedor,(select p.nombre from af_proveedores p where p.codigo=e.cod_proveedor)as nombre_proveedor from estados_cuenta e where e.cod_plancuenta=$cod_cuenta and e.cod_proveedor<>0 GROUP BY e.cod_proveedor ORDER BY nombre_proveedor";
		$sql="SELECT DISTINCT p.codigo,p.nombre FROM solicitud_recursosdetalle s join af_proveedores p on s.cod_proveedor=p.codigo where s.cod_plancuenta = $cod_cuenta order by p.nombre";
	 $stmt3 = $dbh->prepare($sql);
	 $stmt3->execute();
	 while ($rowSel = $stmt3->fetch(PDO::FETCH_ASSOC)) {
	  $codigoSel=$rowSel['codigo'];
	  $nombreSelX=$rowSel['nombre'];
	  ?><option value="<?=$codigoSel;?>####<?=$nombreSelX?>"><?=$nombreSelX?></option><?php 
	 }
	?>
</select>
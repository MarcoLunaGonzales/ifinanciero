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
$cod_cliente=$_GET['cod_cliente'];
$cod_tipopago=$_GET['cod_tipopago'];
$cod_tipo_conf=obtenerValorConfiguracion(48);
if($cod_tipo_conf==$cod_tipopago){ ?>
	<select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true"  required="true">	
		<?php 
		$query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente and cod_estadoreferencial=1 order by nombre";
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigo=$row['codigo'];    
		$nombre_conatacto=$row['nombre']." ".$row['paterno']." ".$row['materno'];
		?><option value="<?=$codigo?>" class="text-right"><?=$nombre_conatacto?></option>
		<?php 
		} ?> 
	</select>
<?php }else{ ?>
	<select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true">	
		<?php 
		$query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente and cod_estadoreferencial=1 order by nombre";
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigo=$row['codigo'];    
		$nombre_conatacto=$row['nombre']." ".$row['paterno']." ".$row['materno'];
		?><option value="<?=$codigo?>" class="text-right"><?=$nombre_conatacto?></option>
		<?php 
		} ?> 
	</select>

<?php }
?>


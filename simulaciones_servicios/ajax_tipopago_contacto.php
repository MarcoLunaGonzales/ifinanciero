<?php
require_once '../conexion.php';
require_once '../functions.php';

//header('Content-Type: application/json');

$cod_tipo = $_GET["cod_tipo"];
$cod_cliente=$_GET["cod_cliente"];
//ini_set("display_errors", "1");
$dbh = new Conexion();
$cod_tipo_conf=obtenerValorConfiguracion(48);
if($cod_tipo_conf==$cod_tipo){ //de tipo credito obligado contacto?>
	<select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">	
		<?php 
		$query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		while ($rowContacto = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigo_contacto=$rowContacto['codigo'];    
		$nombre_conatacto=$rowContacto['nombre']." ".$rowContacto['paterno']." ".$rowContacto['materno'];
		?><option value="<?=$codigo_contacto?>" class="text-right"><?=$nombre_conatacto?></option>
		<?php 
		} ?> 
	</select>
<?php }else{ ?>
	<select class="selectpicker form-control form-control-sm" name="persona_contacto" id="persona_contacto" data-style="btn btn-info" data-show-subtext="true" data-live-search="true" title="Seleccione Contacto">	
		<?php 
		$query="SELECT * FROM clientes_contactos where cod_cliente=$cod_cliente order by nombre";
		$stmt = $dbh->prepare($query);
		$stmt->execute();
		while ($rowContacto = $stmt->fetch(PDO::FETCH_ASSOC)) {
		$codigo_contacto=$rowContacto['codigo'];    
		$nombre_conatacto=$rowContacto['nombre']." ".$rowContacto['paterno']." ".$rowContacto['materno'];
		?><option value="<?=$codigo_contacto?>" class="text-right"><?=$nombre_conatacto?></option>
		<?php 
		} ?> 
	</select>
<?php }
?>


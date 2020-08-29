<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../rrhh/configModule.php';

//header('Content-Type: application/json');

$codigo = $_GET["codigo"];
$admin = $_GET["admin"];
$fecha_fin = $_GET["fecha_fin"];
$cod_defecto_contrato_otros=obtenerValorConfiguracion(79);
// echo $codigo."//".$cod_defecto_contrato_otros;
if($codigo==$cod_defecto_contrato_otros){?>
	<div class="row">
      <label class="col-sm-2 col-form-label" style="color:#424242"> Fecha Fin</label>
      <div class="col-sm-8">
        <div class="form-group">
        	<?php if($admin==1){?>
          		<input class="form-control" type="date" name="fecha_finA" id="fecha_finA"/>
      		<?php }else{?>
      			<input class="form-control" type="date" name="fecha_finE" id="fecha_finE" value="<?=$fecha_fin?>"/>
      		<?php }?>
        </div>
      </div>
    </div>
<?php }else{ ?>
	<?php if($admin==1){?>
		<input class="form-control" type="hidden" name="fecha_finA" id="fecha_finA"/>
	<?php }else{?>
		<input class="form-control" type="hidden" name="fecha_finE" id="fecha_finE" />
	<?php }?>

<?php }
?>

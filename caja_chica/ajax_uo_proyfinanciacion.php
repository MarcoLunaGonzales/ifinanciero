<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../functions.php';
//header('Content-Type: application/json');

$codigo_UO = $_GET["codigo_UO"];
//ini_set("display_errors", "1");
$db = new Conexion();
$cod_uo_proy_fin=VerificarProyFinanciacion($codigo_UO);//verificamos si el codigo pertenece a algun proyecto, de ser asi obtenemos el codigo

if($cod_uo_proy_fin!=null){ 
	$lista= obtenerActividadesServicioImonitoreo($cod_uo_proy_fin);
	?>
	<select name="cod_actividad" id="cod_actividad" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" data-show-subtext="true" data-live-search="true">
	<option disabled selected value="">--SELECCIONE--</option>
	 <?php
	      foreach ($lista as $listas) {
	      	
	          echo "<option  value=".$listas->codigo.">".substr($listas->nombre, 0, 85)."</opction>";
	      }?>
	</select>        
<?php } ?>


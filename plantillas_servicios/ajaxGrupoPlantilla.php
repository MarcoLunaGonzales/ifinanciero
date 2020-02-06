<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$idFila=$_GET['idFila'];
?>
<div id="div<?=$idFila?>"class="col-md-12">
	<div class="row">

		<div class="col-sm-3">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_costo<?=$idFila;?>" id="tipo_costo<?=$idFila;?>" data-style="<?=$comboColor;?>" required>
			  	
			  	<option disabled selected="selected" value="">Tipo</option>
				<option value="1">Fijo</option>
				<option value="2">Variable</option>	
			</select>
			</div>
      	</div>
		<div class="col-sm-5">
            <div class="form-group">
            	<label for="nombre_grupo<?=$idFila;?>" class="bmd-label-floating">Nombre de grupo</label>			
          		<input class="form-control" type="text" name="nombre_grupo<?=$idFila;?>" id="nombre_grupo<?=$idFila;?>" onkeyup="mostrarDetalle(<?=$idFila;?>);" required>	
			</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="abreviatura_grupo<?=$idFila;?>" class="bmd-label-floating">Abreviatura</label>			
          		<input class="form-control" type="text" name="abreviatura_grupo<?=$idFila;?>" id="abreviatura_grupo<?=$idFila;?>" required> 	
			</div>
      	</div>
		<div class="col-sm-2">
		  <div class="btn-group">
		  	<a title="Agregar Detalles" href="#" id="boton_det<?=$idFila;?>" onclick="listDetalle(<?=$idFila;?>);" class="btn btn-just-icon btn-primary btn-link">
               <i class="material-icons">view_list</i><span id="ndet<?=$idFila;?>" class="count bg-warning">0</span>
             </a>
			<a title="Eliminar Registro" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusGrupoPlantilla('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	        <a title="Ver Detalles" href="#" id="boton_det_list<?=$idFila;?>" class="btn btn-just-icon btn-info btn-link" onclick="mostrarDetalle('<?=$idFila;?>');">
            	<i class="material-icons">remove_red_eye</i>
	        </a>
	      </div>  
		</div>

	</div>
<div class="h-divider"></div>
</div>


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

		<div class="col-sm-2">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_costo<?=$idFila;?>" id="tipo_costo<?=$idFila;?>" data-style="<?=$comboColor;?>" onchange="mostrarUnidadDetalle(<?=$idFila;?>)" required>
			  	
			  	<option disabled selected="selected" value="">Tipo</option>
				<option value="1">Fijo</option>
				<option value="2">Variable</option>	
			</select>
			</div>
      	</div>
		<div class="col-sm-4">
            <div class="form-group">
            	<label for="detalle_plantilla<?=$idFila;?>" class="bmd-label-floating">Detalle</label>			
          		<input class="form-control" type="text" name="detalle_plantilla<?=$idFila;?>" id="detalle_plantilla<?=$idFila;?>" required>	
			</div>
      	</div>

      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="cantidad_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Cantidad</label>			
          		<input class="form-control" type="number" min="0" name="cantidad_detalleplantilla<?=$idFila;?>" onkeyup="calcularTotalFilaDetalle(1,<?=$idFila?>)" id="cantidad_detalleplantilla<?=$idFila;?>" value="1" required> 	
			</div>
      	</div>
      	<div class="col-sm-2">
            <div class="form-group">
            	<label for="unidad_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Unidad</label>			
          		<input class="form-control" type="text" name="unidad_detalleplantilla<?=$idFila;?>" id="unidad_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="monto_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Precio Unit.</label>			
          		<input class="form-control" type="number" step="0.01" min="0" name="monto_detalleplantilla<?=$idFila;?>" onkeyup="calcularTotalFilaDetalle(1,<?=$idFila?>)" id="monto_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="monto_total_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Precio Total</label>			
          		<input class="form-control" type="number" step="0.01" min="0" name="monto_total_detalleplantilla<?=$idFila;?>" id="monto_total_detalleplantilla<?=$idFila;?>" onkeyup="calcularTotalFilaDetalle(2,<?=$idFila?>)" required> 	
			</div>
      	</div>
		<div class="col-sm-1">
		   <div class="btn-group">
		  	<a title="No hay cuenta asociada" href="#" id="boton_det<?=$idFila;?>" onclick="listDetallePlantilla(<?=$idFila;?>);" class="btn btn-just-icon btn-primary btn-link">
               <i class="material-icons">view_list</i><span id="ndet<?=$idFila;?>" class="bg-danger estado2"></span>
             </a>
             <input type="hidden" name="codigo_cuentadetalle<?=$idFila;?>" id="codigo_cuentadetalle<?=$idFila;?>">
             <input type="hidden" name="codigo_partidadetalle<?=$idFila;?>" id="codigo_partidadetalle<?=$idFila;?>"> 	
			<a title="Eliminar Registro" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusDetallePlantilla('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	        <!--<a title="Ver Detalles" href="#" id="boton_det_list<?=$idFila;?>" class="btn btn-just-icon btn-info btn-link" onclick="mostrarDetalle('<?=$idFila;?>');">
            	<i class="material-icons">remove_red_eye</i>
	        </a>-->
	      </div>  
		</div>

	</div>
<div class="h-divider"></div>
</div>


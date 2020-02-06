<?php
session_start();
require_once '../conexion.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalAdmin=$_SESSION["globalAdmin"];
//listado de tipo documento rendicion
$statementTipoDocRendicion = $dbh->query("SELECT td.codigo,td.nombre from tipos_documentocajachica td where td.tipo=2 order by 2");

$idFila=$_GET['idFila'];


?>
<div id="comp_row" class="col-md-12">
	<div class="row">

		<div class="col-sm-3">
            <div class="form-group">
            	<!-- <label for="debe<?=$idFila;?>" >Tipo Doc</label>			 -->

          		<select name="tipo_doc<?=$idFila;?>" id="tipo_doc<?=$idFila;?>" class="selectpicker form-control" >
          			<option disabled  value="">Tipo Doc.</option>
			          <?php while ($row = $statementTipoDocRendicion->fetch()){ ?>
			              <option value="<?=$row["codigo"];?>"><small><?=$row["nombre"];?></small></option>
			          <?php } ?>
			        </select>
			</div>
      	</div>

		<div class="col-sm-2">
            <div class="form-group">
            	<label for="haber<?=$idFila;?>" class="bmd-label-floating">Nro. Doc</label>			
          		<input class="form-control" type="number" name="numero_doc<?=$idFila;?>" id="numero_doc<?=$idFila;?>" requerid> 	
			</div>
      	</div>
      	<div class="col-sm-2">
            <div class="form-group">
            	<label for="haber<?=$idFila;?>" >Fecha Doc</label>			
          		<input class="form-control" type="date" name="fecha_doc<?=$idFila;?>" id="fecha_doc<?=$idFila;?>" requerid > 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="haber<?=$idFila;?>" class="bmd-label-floating">Monto</label>			
          		<input class="form-control" type="text" step="0.01" name="monto_A<?=$idFila;?>" id="monto_A<?=$idFila;?>" onChange="sumartotalmontoRendicion(this.id,event);" OnKeyUp="sumartotalmontoRendicion(this.id,event);" requerid > 	
			</div>
      	</div>
      	<div class="col-sm-3">
		    <div class="form-group">
          		<label for="glosa_detalle<?=$idFila;?>" class="bmd-label-static">Detalle</label>
				<textarea rows="1" class="form-control" name="observacionesA<?=$idFila;?>" id="observacionesA<?=$idFila;?>" value="" requerid></textarea>
			</div>
		</div>
		<div class="col-sm-1">
		    <div class="form-group">
        		<a rel="tooltip" title="Eliminar" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="borrarItemRendicionDetalle('<?=$idFila;?>');">
            		<i class="material-icons">remove_circle</i>
	        	</a>  		
			</div>
		</div>

		

	</div>
</div>

<div class="h-divider"></div>


<?php
require_once '../conexion.php';
require_once '../functions.php';
require_once '../styles.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

session_start();
$globalAdmin=$_SESSION["globalAdmin"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$codigo=$_GET['codigo'];
$codigoIndicador=$_GET['cod_indicador'];
$codUnidad=$_GET['cod_unidad'];
$codArea=$_GET['cod_area'];

$codUnidadHijosX=buscarHijosUO($codUnidad);

$nombreTablaClasificador=obtieneTablaClasificador($codigoIndicador,$codArea);

?>
<div class="col-md-12">
	<div class="row">

		<div class="col-sm-3">
	        <div class="form-group">
				<input type="hidden" name="codigo<?=$codigo;?>" id="codigo<?=$codigo;?>" value="0">
				<select class="selectpicker" name="norma_priorizada<?=$codigo;?>" id="norma_priorizada<?=$codigo;?>" data-style="<?=$comboColor;?>" data-live-search="true">
				  	<option value="">Norma Priorizada</option>
				  	<?php
				  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$codigoX=$row['codigo'];
						$nombreX=$row['nombre'];
					?>
					<optgroup label="<?=$nombreX;?>">
					<?php
					  	$stmtY = $dbh->prepare("SELECT n.codigo, n.nombre, n.abreviatura FROM normas n, normas_priorizadas np where n.codigo=np.codigo and n.cod_sector='$codigoX' and n.cod_estado=1 order by 2");
						$stmtY->execute();
						while ($rowY = $stmtY->fetch(PDO::FETCH_ASSOC)) {
							$codigoY=$rowY['codigo'];
							$nombreY=$rowY['nombre'];
							$nombreY=cutString($nombreY,80);
							$abreviaturaY=$rowY['abreviatura'];

					?>
							<option value="<?=$codigoY;?>" data-subtext="<?=$nombreY?>"><?=$abreviaturaY;?></option>	
					<?php
						}
					?>
					</optgroup>
					<?php	
					}
				  	?>
				</select>
			</div>
	    </div>

		<div class="col-sm-3">
	        <div class="form-group">
				<select class="selectpicker" name="norma<?=$codigo;?>" id="norma<?=$codigo;?>" data-style="<?=$comboColor;?>" data-live-search="true">
				  	<option value="">Norma</option>
				  	<?php
				  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$codigoX=$row['codigo'];
						$nombreX=$row['nombre'];
					?>
					<optgroup label="<?=$nombreX;?>">
					<?php
					  	$stmtY = $dbh->prepare("SELECT n.codigo, n.nombre, n.abreviatura FROM normas n where n.cod_sector='$codigoX' and n.cod_estado=1 order by 2");
						$stmtY->execute();
						while ($rowY = $stmtY->fetch(PDO::FETCH_ASSOC)) {
							$codigoY=$rowY['codigo'];
							$nombreY=$rowY['nombre'];
							$nombreY=cutString($nombreY,80);
							$abreviaturaY=$rowY['abreviatura'];

					?>
							<option value="<?=$codigoY;?>" data-subtext="<?=$nombreY?>"><?=$abreviaturaY;?></option>	
					<?php
						}
					?>
					</optgroup>
					<?php	
					}
				  	?>
				</select>
			</div>
	    </div>

	  	<div class="col-sm-3">
			<div class="form-group">
				<label for="producto_esperado<?=$codigo;?>" class="bmd-label-floating">Producto Esperado</label>
				<input class="form-control" type="text" name="producto_esperado<?=$codigo;?>" id="producto_esperado<?=$codigo;?>"/>
			</div>
		</div>

	  	<div class="col-sm-3">
	    	<div class="form-group">
	        <select class="selectpicker" name="clasificador<?=$codigo;?>" id="clasificador<?=$codigo;?>" data-style="<?=$comboColor;?>" data-width="200px" data-live-search="true" onChange="completaActividad(this,<?=$codigo;?>)" >
			  	<option disabled selected value="">Clasificador</option>
			  	<?php
			  	if($nombreTablaClasificador!="" && $nombreTablaClasificador!="clientes"){
				  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM $nombreTablaClasificador where cod_estado=1 order by 2");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$codigoX=$row['codigo'];
						$nombreX=$row['nombre'];
						$abrevX=$row['abreviatura'];

				?>
						<option value="<?=$codigoX;?>"><?=$abrevX."-".$nombreX;?></option>	
				<?php
					}
			  	}
			  	if($nombreTablaClasificador=="clientes"){
				  	$stmt = $dbh->prepare("SELECT c.codigo, c.nombre, u.nombre as unidad from clientes c, unidades_organizacionales u where c.cod_unidad=u.codigo 
				  		and c.cod_unidad in ($codUnidadHijosX) order by 2;");
					$stmt->execute();
					while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
						$codigoX=$row['codigo'];
						$nombreX=$row['nombre'];
						$nombreUnidad=$row['unidad'];

				?>
						<option value="<?=$codigoX;?>" data-subtext="<?=$nombreUnidad;?>"><?=$nombreX;?></option>	
				<?php
					}
			  	}
			  	?>
			</select>
			</div>
	  	</div>
	</div>
</div>

<div class="col-md-12">
	<div class="row">
	    <div class="col-sm-6">
		    <div class="form-group">
          		<label for="actividad<?=$codigo;?>" class="bmd-label-floating">Actividad</label>
				<textarea class="form-control" name="actividad<?=$codigo;?>" id="actividad<?=$codigo;?>" required="true" rows="1" value=""></textarea>
			</div>
		</div>

		<div class="col-sm-2">
	    	<div class="form-group">
	        <select class="selectpicker form-control" name="clave_indicador<?=$codigo;?>" id="clave_indicador<?=$codigo;?>" data-style="<?=$comboColor;?>">
			  	<option value="">CMI</option>
				<option value="0">No</option>
				<option value="1">Si</option>
			</select>
			</div>
	  	</div>

	  	<div class="col-sm-3">
			<div class="form-group">
				<label for="tipo_seguimiento<?=$codigo;?>" class="bmd-label-floating">Unidad de Medida</label>
				<input class="form-control" type="text" name="tipo_seguimiento<?=$codigo;?>" id="tipo_seguimiento<?=$codigo;?>"/>
			</div>
		</div>

		<div class="col-sm-1">
			<button rel="tooltip" class="btn btn-just-icon btn-danger btn-link" onclick="minusActividad('<?=$codigo;?>');">
	                              <i class="material-icons">remove_circle</i>
	        </button>
		</div>
	</div>
</div>



<div class="col-md-12">
	<div class="row">
		<div class="col-sm-6">
            <div class="form-group">
            <label for="observaciones<?=$codigo;?>" class="bmd-label-floating">Observaciones</label>			
          	<input class="form-control" type="text" name="observaciones<?=$codigo;?>" id="observaciones<?=$codigo;?>" onkeyup="javascript:this.value=this.value.toUpperCase();">	
				</div>
      	</div>

		<div class="col-sm-4">
        	<div class="form-group">
	        <select class="selectpicker form-control" name="hito<?=$codigo;?>" id="hito<?=$codigo;?>" data-style="<?=$comboColor;?>">
			  	<option disabled selected value="">Hito</option>
			  	<?php
			  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM hitos where cod_estado=1 order by 2");
				$stmt->execute();
				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					$codigoX=$row['codigo'];
					$nombreX=$row['nombre'];
					$nombreX=substr($nombreX,0,40);
					$abrevX=$row['abreviatura'];
				?>
				<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
				<?php
			  	}
			  	?>
			</select>
			</div>
      	</div>

	</div>
</div>

<div class="h-divider">

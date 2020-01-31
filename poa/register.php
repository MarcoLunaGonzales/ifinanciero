<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$globalNombreGestion=$_SESSION["globalNombreGestion"];

$codigoIndicador=$codigo;
$nombreIndicador=nameIndicador($codigoIndicador);

$dbh = new Conexion();

$table="actividades_poa";
$moduleName="Actividades POA";

?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="poa/save.php" method="post">
			<input type="hidden" name="cod_indicador" value="<?=$codigoIndicador?>">
			<div class="card ">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleName;?></h4>
				  <h6 class="card-title">Indicador: <?=$nombreIndicador;?></h4>

				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-2 col-form-label">Gestion</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="gestion" value="<?=$globalNombreGestion;?>" id="gestion" disabled="true" />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Sector Priorizado</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker" name="sector" id="sector" data-style="<?=$comboColor;?>" onChange="ajaxNormas(this,1,'div_normapri');" required><!--1 PARA NORMAS PRIORIZADAS 0 PARA TODO-->
					  	<option disabled selected value=""></option>
					  	<?php
					  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$codigoX=$row['codigo'];
							$nombreX=$row['nombre'];
						?>
						<option value="<?=$codigoX;?>"><?=$nombreX;?></option>
						<?php	
						}
					  	?>
					  </select>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Norma Priorizada</label>
				  <div class="col-sm-7">
					<div class="form-group" id="div_normapri">
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Sector</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker" name="sector" id="sector" data-style="<?=$comboColor;?>" onChange="ajaxNormas(this,0,'div_norma');" required><!--1 PARA NORMAS PRIORIZADAS 0 PARA TODO-->
					  	<option disabled selected value=""></option>
					  	<?php
					  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$codigoX=$row['codigo'];
							$nombreX=$row['nombre'];
						?>
						<option value="<?=$codigoX;?>"><?=$nombreX;?></option>
						<?php	
						}
					  	?>
					  </select>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Norma</label>
				  <div class="col-sm-7">
					<div class="form-group" id="div_norma">
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Actividad</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="actividad" id="actividad" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"/>
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Tipo de Dato</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <select class="selectpicker" name="tipo_dato" id="tipo_dato" data-style="<?=$comboColor;?>"required>
					  	<option disabled selected value=""></option>
					  	<?php
					  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_datoactividad order by 2");
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$codigoX=$row['codigo'];
							$nombreX=$row['nombre'];
						?>
						<option value="<?=$codigoX;?>"><?=$nombreX;?></option>
						<?php	
						}
					  	?>
					  </select>
					</div>
				  </div>
				</div>


				<div class="row">
				  <label class="col-sm-2 col-form-label">Producto Esperado</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="producto_esperado" id="producto_esperado"/>
					</div>
				  </div>
				</div>


			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$button;?>">Guardar</button>
				<a href="?opcion=listPOA" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>
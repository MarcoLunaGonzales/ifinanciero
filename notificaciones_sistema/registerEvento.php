<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
			<div class="card">
			  <div class="card-header card-header-info card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				
				<div class="row">
				  <label class="col-sm-2 col-form-label">Evento</label>
				  <div class="col-sm-7">
					<div class="form-group">
					 <select class="selectpicker form-control" name="evento" id="evento" data-style="btn btn-info" onchange="ponerDescripcionEvento()" required>
					 	<option selected value="">Seleccione un evento</option>	
					 	<?php
					 	$stmt = $dbh->prepare("select codigo,nombre,observaciones from eventos_sistema order by codigo");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreX=$row['nombre'];
									$observacionesX=$row['observaciones'];
								?>
								<option value="<?=$codigoX;?>@<?=$observacionesX?>@<?=$nombreX;?>"><?=$nombreX;?></option>	
								<?php
							  	}
							  	?>
					 </select>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Descripcion Evento</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <textarea readonly class="form-control" type="text" name="descripcion" id="descripcion"></textarea>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Personal</label>
				  <div class="col-sm-7">
					<div class="form-group">
					 <select class="selectpicker form-control form-control-sm" onchange="ponerCorreoPersona()" data-style="select-with-transition" data-live-search="true" title="-- Elija un personal --" name="personal" id="personal" data-style="<?=$comboColor;?>" required="true">
					 	<?php
					 	$stmt = $dbh->prepare('select p.codigo,p.email_empresa,concat(p.paterno," ",p.materno," ",p.primer_nombre," ",p.otros_nombres) as personal from personal p order by p.paterno');
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreX=$row['personal'];
									$email_empresaX=$row['email_empresa'];
								?>
								<option value="<?=$codigoX;?>$$$<?=$email_empresaX?>"><?=$nombreX;?></option>	
								<?php
							  	}
							  	?>
					 </select>
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Correo Institucional</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input readonly class="form-control" type="text" name="correo" id="correo">
					</div>
				  </div>
				</div>
				<hr>
			  </div>
			  <div class="card-footer fixed-bottom ml-auto mr-auto">
				<button class="btn btn-default" id="boton_enviocorreo" onclick="registrarCorreoEvento()"> Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>
			  </div>
			</div>
		</div>
	
	</div>
</div>
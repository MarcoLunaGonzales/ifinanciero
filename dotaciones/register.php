<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
		  <form id="form1" class="form-horizontal" action="<?=$urlSave;?>" method="post">
			<div class="card">
			  <div class="card-header <?=$colorCard;?> card-header-text">
				<div class="card-text">
				  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
				</div>
			  </div>
			  <div class="card-body ">
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nombre</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="nombre" id="nombre" required="true" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Abreviatura</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="abreviatura" id="abreviatura" required="true" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Descripción</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="text" name="descripcion" id="descripcion" required="true" />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Nro. de Meses</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="number" name="nro_meses" id="nro_meses" required="true"  />
					</div>
				  </div>
				</div>
				<div class="row">
				  <label class="col-sm-2 col-form-label">Mes de Inicio</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio" required="true"  />
					</div>
				  </div>
				</div>

				<div class="row">
				  <label class="col-sm-2 col-form-label">Mes Fin</label>
				  <div class="col-sm-7">
					<div class="form-group">
					  <input class="form-control" type="date" name="fecha_fin" id="fecha_fin" required="true" />
					</div>
				  </div>
				</div>
			  </div>
			  <div class="card-footer ml-auto mr-auto">
				<button type="submit" class="<?=$buttonNormal;?>">Guardar</button>
				<a href="<?=$urlList;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
			</div>
		  </form>
		</div>
	
	</div>
</div>
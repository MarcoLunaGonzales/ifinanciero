<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh 		= new Conexion();
$codGestion = $_SESSION['globalGestion'];
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
			<form id="form1" class="form-horizontal" action="<?=$urlSave;?>" method="post">
				<!-- DATOS FORMULARIO -->
				<div class="card">
					<div class="card-header <?=$colorCard;?> card-header-text">
						<div class="card-text">
							<h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
						</div>
					</div>
					<div class="card-body ">
						<div class="row">
							<label class="col-sm-3 col-form-label"><b><span class="text-danger">*</span> Nombre</b></label>
							<div class="col-sm-6">
								<div class="form-group">
									<input class="form-control" type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre de la configuración"/>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer ml-auto mr-auto">
						<button type="submit" class="btn btn-primary">Guardar</button>
						<a href="<?=$urlList;?>" class="btn btn-default">Volver</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
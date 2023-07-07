<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();

$cod_aprobacion = $codigo;

$sql = "SELECT acc.codigo,
			acc.nombre,
			acc.fecha_registro,
			acc.fecha_aprobacion,
			acc.cod_personal_aprobacion,
			acc.cod_estadoaprobacion 
		FROM  aprobacion_configuraciones_cargos acc 
		WHERE acc.codigo = :codigo";
$stmt = $dbh->prepare($sql);

$stmt->bindParam(':codigo', $cod_aprobacion);

$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codigo = $row['codigo'];
	$nombre = $row['nombre'];
}

?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
			<form id="form1" class="form-horizontal" action="<?=$urlSaveEdit;?>" method="post">
				<!-- CODIGO -->
				<input type="hidden" name="codigo" id="codigo" value="<?=$codigo;?>"/>
				<!-- DATOS FORMULARIO -->
				<div class="card">
					<div class="card-header <?=$colorCard;?> card-header-text">
						<div class="card-text">
							<h4 class="card-title">Editar <?=$moduleNameSingular;?></h4>
						</div>
					</div>
					<div class="card-body ">
						<div class="row">
							<label class="col-sm-3 col-form-label"><b><span class="text-danger">*</span> Nombre</b></label>
							<div class="col-sm-6">
								<div class="form-group">
									<input class="form-control" type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre de la configuración" value="<?= $nombre; ?>"/>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer ml-auto mr-auto">
						<button type="submit" class="btn btn-primary">Actualizar</button>
						<a href="<?=$urlList;?>" class="btn btn-default">Volver</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
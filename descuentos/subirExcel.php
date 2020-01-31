<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codDescuento = $codigo_descuento;
$codMes = $codigo_mes;
$codGestion = $_SESSION['globalGestion'];

$nombreGestion=$_SESSION['globalNombreGestion'];



//Mostrar tipo descuento
$stmtb = $dbh->prepare("SELECT nombre FROM $table WHERE codigo=$codDescuento");
$stmtb->execute();
$stmtb->bindColumn('nombre', $nombreDescuento);

while ($row = $stmtb->fetch(PDO::FETCH_BOUND)) {
  $nomDescuento = $nombreDescuento;
}

//Mostrar Mes
$stmtc = $dbh->prepare("SELECT nombre FROM meses WHERE codigo=$codMes");
$stmtc->execute();
$stmtc->bindColumn('nombre', $nombreMes);

while ($row = $stmtc->fetch(PDO::FETCH_BOUND)) {
  $nomMes = $nombreMes;
}


?>

<div class="content">
	<div class="container-fluid">

		<div class="col-md-12">
			<form id="form2" class="form-horizontal" action='<?= $urlSubirDescuentoExcel2; ?>' method="post" enctype="multipart/form-data">
				<div class="card">
					<div class="card-header <?= $colorCard; ?> card-header-text">
						<div class="card-text">
							<h4 class="card-title">Subir archivo Excel <?= $moduleNamePlural; ?></h4>
						</div>
						<h4 class="card-title" align="center"><?= $nomDescuento . " : " . $nomMes . " " . $nombreGestion ?></h4>

					</div>



					<div class="card-body ">
						<div class="row">
							<label class="col-sm-2 col-form-label">Opciones de cargado</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select class="selectpicker form-control" name="opcionCargar" id="opcionCargar" data-style="<?= $comboColor; ?>" required="true">
										<option disabled selected value="">Elija Opción</option>
										<option value="1">Sobreescribir los datos existentes e insertar nuevos</option>
										<option value="2">Mantener los datos existentes e insertar nuevos</option>
										<option value="3">Borrar todo y cargar de nuevo</option>
									</select>
								</div>
							</div>
						</div>
						<br>

						<div class="row">
							<label class="col-sm-2 col-form-label">Excel Formato .csv</label>
							<div class="col-sm-7">
								<input class="form-control" type="file" name="archivo" id="archivo" required="true" />
							</div>
						</div>

						<input class="form-control" type="text" hidden="true" name="codMes" id="codMes" value="<?= $codMes; ?>" />
						<input class="form-control" type="text" hidden="true" name="codDescuento" id="codDescuento" value="<?= $codDescuento; ?>" />
						<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion" value="<?= $codGestion; ?>" />

					</div>

					<div class="card-footer ml-auto mr-auto">
						<button type="submit" class="<?= $buttonNormal; ?>" name="enviar">Guardar</button>
						<a href="<?= $urlListMesPersona; ?>&cod_mes=<?= $codMes; ?>&cod_descuento=<?= $codDescuento; ?>" class="<?= $buttonCancel; ?>"> <-- Volver </a>
					    <a href="<?=$urlAyudaCSV;?>" target="_blank" class="btn btn-info"><span class="material-icons">help_outline</span> Ayuda</a>
					</div>

				</div>
			</form>

		</div>

	</div>
</div>
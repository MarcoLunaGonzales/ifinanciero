<?php

require_once 'conexion.php';
require_once 'functions.php';
require_once 'styles.php';
require_once 'configModule.php';

$dbh = new Conexion();
$codGestion = $_SESSION['globalGestion'];
$nombreGestion=$_SESSION['globalNombreGestion'];
?>

<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
			<form id="form2" class="form-horizontal" action='index.php?opcion=subirBonoExcel_global_save' method="post" enctype="multipart/form-data">
				<div class="card">
					<div class="card-header <?= $colorCard; ?> card-header-text">
						<div class="card-text">
							<h4 class="card-title">Subir archivo Excel de Bonos Y Descuentos</h4>
							<input class="form-control" type="text" hidden="true" name="codGestion" id="codGestion" value="<?= $codGestion; ?>"/>
						</div>
						<h4 class="card-title" align="center"><?="Gestión : ".$nombreGestion ?></h4>
					</div>
					<div class="card-body ">
						<div class="row">
							<label class="col-sm-2 col-form-label">Mes</label>
              <div class="col-sm-2">
                  <div class="form-group">
                      <select name="cod_mes" id="cod_mes" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                      	<option disabled selected value="">SELECCIONAR MES</option>
                          <?php 
                          $sql = "SELECT codigo,nombre from meses where cod_estado=1 order by codigo";
                          $stmt = $dbh->query($sql);
                          while ($row = $stmt->fetch()){ ?>
                              <option value="<?=$row["codigo"];?>" data-subtext="(<?=$row['codigo']?>)"><?=$row["nombre"];?></option>
                          <?php } ?>
                      </select>
                  </div>
               </div>
							<label class="col-sm-2 col-form-label">Opciones de cargado</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select class="selectpicker form-control form-control-sm" name="opcionCargar" id="opcionCargar" data-style="<?= $comboColor; ?>" required="true">
										<!-- <option disabled selected value="">Elija Opción</option>
										<option value="1">Sobreescribir los datos existentes e insertar nuevos</option>
										<option value="2">Mantener los datos existentes e insertar nuevos</option> -->
										<option value="3">Borrar todo y cargar de nuevo</option>
									</select>
								</div>
							</div>
						</div>
						<br>

						<div class="row">
							<label class="col-sm-2 col-form-label">Excel Formato .csv</label>
							<div class="col-sm-7">
								<input class="form-control" type="file" name="archivo" id="archivo" accept="csv" required="true" />
							</div>
						</div>
					</div>
					<div class="card-footer ml-auto mr-auto">
							<button type="submit" class="<?= $buttonNormal; ?>" name="enviar">Guardar</button>
							<a href="?opcion=planillasSueldoPersonal" class="<?= $buttonCancel;?>"> <-- Volver </a>
					</div>
				</div>
			</form>

		</div>

	</div>
</div>
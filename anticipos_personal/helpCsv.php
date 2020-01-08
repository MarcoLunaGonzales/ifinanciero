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
             <!-- card de ayuda import csv-->

        <div class="card">
					<div class="card-header card-header-warning card-header-text">
						<div class="card-text">
							<h4 class="card-title">Como obtener archivo CSV</h4>
						</div>
					</div>
					<div class="card-body ">
						<div class="row">
                          <div class="col-sm-4">
                            <div class="thumbnail">
                              <img class="img-circle" width="300" height="200" src="assets/import_csv/import_csv1.png">
                              <div class="caption">
                                <h3>Paso 1</h3>
                                <p>REGISTRAMOS LOS DATOS EN TRES COLUMNAS (código personal, nombres y apellidos, monto a ingresar)</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="thumbnail">
                              <img class="img-circle" width="300" height="200" src="assets/import_csv/import_csv2.png">
                              <div class="caption">
                                <h3>Paso 2</h3>
                                <p>Click izquierdo en “ARCHIVO”</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="thumbnail">
                              <img class="img-circle" width="300" height="200" src="assets/import_csv/import_csv3.png">
                              <div class="caption">
                                <h3>Paso 3</h3>
                                <p>Click izquierdo en “GUARDAR COMO”</p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <hr>
                        <div class="row">
                          <div class="col-sm-4">
                            <div class="thumbnail">
                              <img class="img-circle" width="300" height="200" src="assets/import_csv/import_csv4.png">
                              <div class="caption">
                                <h3>Paso 4</h3>
                                <p>Click izquierdo en “Tipo” (Debe poner el nombre del archivo) .</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="thumbnail">
                              <img class="img-circle" width="300" height="200" src="assets/import_csv/import_csv5.png">
                              <div class="caption">
                                <h3>Paso 5</h3>
                                <p>Seleccionar la opción CSV (Delimitado por comas)</p>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="thumbnail">
                              <img class="img-circle" width="300" height="200" src="assets/import_csv/import_csv6.png">
                              <div class="caption">
                                <h3>Paso 6</h3>
                                <p>Click izquierdo en “Guardar”.</p>
                              </div>
                            </div>
                          </div>
                        </div>
					</div>
				</div>
				<!-- fin card ayuda csv-->
		</div>

	</div>
</div>
<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';

$dbh = new Conexion();

?>
<div class="content">
	<div class="container-fluid">
		<div class="col-md-12">
			<div class="card">
			  	<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
				  		<h4 class="card-title">Reportes Facturación</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">				  	
				  		<div class="col-sm-4">
							<div class="form-group">
								<a href="<?=$urlReporteIngresoFacturacion_libretas;?>" class="btn btn-success"> Facturas Con libretas Bancarias</a>
							</div>
				  		</div>
					</div>
					<div class="row">				  	
				  		<div class="col-sm-4">
							<div class="form-group">
								<!-- <a href="<?=$urlReporteIngresoFacturacion_libretas;?>" class="btn btn-success">Lista Facturas Generadas</a> -->								
								<a class="btn btn-info" href="../index.php?opcion=listFacturasGeneradas&interno=100">				                
				                    <span class="sidebar-normal"> Facturas Generadas</span>
				                </a>
							</div>
				  		</div>
					</div>
			  	</div>
			</div>

		</div>
	</div>
</div>
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
				  		<h4 class="card-title">Reportes de Ingresos</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporteResumido;?>" class="btn btn-success" target="_blank"> Ingresos por Factura y Área</a>
							</div>
				  		</div>

				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporteResumidoArea;?>" class="btn btn-primary"  target="_blank"> Ingresos por Área</a>
							</div>
				  		</div>
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporteResumidoArea_servicios;?>" class="btn btn-warning" target="_blank"> Ingresos por Servicios</a>
							</div>
				  		</div>
				  		
					</div>
			  	</div>
			</div>

		</div>
		 <div class="card">
			  	<div class="card-header card-header-default card-header-text">
					<div class="card-text">
				  		<h4 class="card-title">Reportes de Egresos</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporteResumidoEg;?>" class="btn btn-info"  target="_blank"> Egreso Detallado</a>
							</div>
				  		</div>

				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporteResumidoAreaEg;?>" class="btn btn-defult"  target="_blank"> Egreso por Área</a>
							</div>
				  		</div>
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporteResumidoAreaCuentaEg;?>" class="btn btn-warning" target="_blank"> Egreso por Área y Cuenta</a>
							</div>
				  		</div>
					</div>
			  	</div>
			</div>


			<div class="card">
			  	<div class="card-header card-header-primary card-header-text">
					<div class="card-text">
				  		<h4 class="card-title">Reportes Administrativos</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlFacturaAdministrativo;?>" class="btn btn-default" target="_blank"> Detalle de Recaudaciones </a>
							</div>
				  		</div>

				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="../reportes_facturacion/reporte_capacitacion_filtro.php" class="btn btn-warning" target="_blank"> Recaudaciones Formación</a>
							</div>
				  		</div>

					</div>
			  	</div>
			</div>

            <div class="card">
			  	<div class="card-header card-header-rose card-header-text">
					<div class="card-text">
				  		<h4 class="card-title">Reportes Solicitudes de Recursos</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlSRAdministrativo;?>" class="btn btn-default" target="_blank"> Solicitudes de Recursos </a>
							</div>
				  		</div>
					</div>
			  	</div>
			</div> 
			<div class="card">
			  	<div class="card-header card-header-rose card-header-text">
					<div class="card-text">
				  		<h4 class="card-title">Reportes Solicitudes de Facturación</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">
				  		<div class="col-sm-3">
							<div class="form-group">
								<a href="<?=$urlReporeSF;?>" class="btn btn-default" target="_blank"> Solicitudes de Facturación</a>
							</div>
				  		</div>
					</div>
			  	</div>
			</div> 
	</div>
</div>
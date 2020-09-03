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
								<a class="btn btn-info" href="lista_de_facturas_generadas.php">				                
				                    <span class="sidebar-normal"> Información Facturas Generadas</span>
				                </a>
							</div>
				  		</div>  	
				  		<div class="col-sm-4">
							<div class="form-group">
								<a class="btn btn-warning" href="../index.php?opcion=listFacturasGeneradas&interno=100">				                
				                    <span class="sidebar-normal"> Facturas Generadas Admin</span>
				                </a>
							</div>
				  		</div>
					</div>

					<div class="row">				  	
				  		<div class="col-sm-4">
							<div class="form-group">								
								<a class="btn btn-primary" href="<?=$urlReporteCajachica_comprobante_factura?>">				                
				                    <span class="sidebar-normal"> FActuras Caja Chica Contra Caja Chica </span>
				                </a>
							</div>
				  		</div>
				  		<div class="col-sm-4">
							<div class="form-group">								
								<a class="btn btn-primary" href="../caja_chica/reporte_cajachica_filtro.php">			
				                    <span class="sidebar-normal"> Caja Chica Monto Comprobante </span>
				                </a>
							</div>
				  		</div>
				  		<div class="col-sm-4">
							<div class="form-group">								
								<a class="btn btn-info" href="lista_de_estudiantes_sw.php">				                
				                    <span class="sidebar-normal"> Control De Pago de Estudiantes WS </span>
				                </a>
							</div>
				  		</div>
				  		
					</div>
			  	</div>
			</div>
			<div class="card">
			  	<div class="card-header card-danger card-header-text">
					<div class="card-text">
				  		<h4 class="card-title">Reportes Facturación,estado de resultados,comprobantes</h4>
					</div>
			  	</div>

			  	<div class="card-body ">
					<div class="row">				
						<div class="col-sm-4">
							<div class="form-group">								
								<a class="btn btn-info" href="reporte_verif_reportes_resultados_filtro.php">				                
				                    <span class="sidebar-normal"> reportes vs estado resultados</span>
				                </a>
							</div>
				  		</div>  	
				  		<div class="col-sm-4">
							<div class="form-group">
								<a class="btn btn-warning" href="reporte_verificacion_facturas_comprobante_filtro.php">				                
				                    <span class="sidebar-normal"> Facturas vs Comprobantes</span>
				                </a>
							</div>
				  		</div>
					</div>

					
			  	</div>
			</div>

		</div>
	</div>
</div>
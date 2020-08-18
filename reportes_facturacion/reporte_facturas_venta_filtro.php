<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
$dbh = new Conexion();


$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalUser=$_SESSION["globalUser"];

$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-".$m."-".$d;

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";
?>

<div class="content">
	<div class="container-fluid">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Facturas Generadas</h4>
                </div>
                <form class="" action="reporte_facturas_venta_print.php" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
		                <label class="col-sm-2 col-form-label">Unidad</label>
		                <div class="col-sm-8">
		                	<div class="form-group">		                		
	                			<?php
								$sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
								$stmt = $dbh->prepare($sqlUO);
								$stmt->execute();
								?>
								<select class="selectpicker form-control form-control-sm" name="unidad[]" id="unidad" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
								    <?php 
								    while ($row = $stmt->fetch()){ ?>
								      	<option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" <?=($row["codigo"]==$globalUnidad)?"selected":""?> ><?=$row["abreviatura"];?></option><?php 
								 	} ?>
								</select>		                		
		                     </div>
		                </div>				             
                  	</div><!--div row-->
                  	

	                <div class="row">	                  	
						<label class="col-sm-2 col-form-label">Desde</label>
						<div class="col-sm-8">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaDesde?>">	
								</div>		                                
							 </div>
						</div>
	                </div><!--div fechas row-->
	                <div class="row">	                  	
						<label class="col-sm-2 col-form-label">Hasta</label>
						<div class="col-sm-8">
							<div class="form-group">
								<div id="div_contenedor_fechaH">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaHasta?>">
								</div>
							   
							</div>
						</div>				            
	                </div><!--div fechas row-->
	            </div><!--div fechas row-->
                <div class="card-footer fixed-bottom">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
				   <a href="../reportes_ventas/" class="<?=$buttonCancel;?>"> Volver </a>
			  </div>
               </form> 
              </div>	  
            </div>                 
	</div>
        
</div>


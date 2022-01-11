<?php
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once '../layouts/bodylogin2.php';

$globalAdmin="";
$globalUnidad="";
$globalArea="";
$globalUser="";

$dbh = new Conexion();
$fechaActual=date("m/d/Y");
$m=date("m");

$y=date("Y")-1;
$y2=date("Y");

$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y2."-".$m."-".$d;

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y2."-12-31";
?>

<div class="content">
	<div class="container-fluid">
				 	
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Recaudaciones Formación</h4>
                </div>
                <form class="" action="<?=$urlReporte_print_capacitacion?>" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
		                <label class="col-sm-1 col-form-label">Oficina</label>
		                <div class="col-sm-4">
		                	<div class="form-group">		                		
	                			<?php
								$sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo where centro_costos=1 order by 2";
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
		                <label class="col-sm-2 col-form-label">Área</label>
		                <div class="col-sm-4">
		                	<div class="form-group">		                		
	                			<?php
								$sqlUO="SELECT a.codigo, a.nombre,a.abreviatura from areas a where a.codigo in (13) order by 2";
								$stmt = $dbh->prepare($sqlUO);
								$stmt->execute();
								?>
								<select class="selectpicker form-control form-control-sm" name="areas[]" id="areas" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
								    <?php 
								    while ($row = $stmt->fetch()){ ?>
								      	<option value="<?=$row["codigo"];?>" selected><?=$row["abreviatura"];?></option><?php 
								 	} ?>
								</select>		                		
		                     </div>
		                </div>				       
                  	</div><!--div row-->
	                <div class="row">	                  	
						<label class="col-sm-1 col-form-label">Desde</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaI">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaDesde?>">	
								</div>		                                
							 </div>
						</div>
						<label class="col-sm-2 col-form-label">Hasta</label>
						<div class="col-sm-4">
							<div class="form-group">
								<div id="div_contenedor_fechaH">				                			
									<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaHasta?>">
								</div>
							   
							</div>
						</div>	
	                </div><!--div fechas row-->	                
	            </div><!--div fechas row-->
                <div class="card-footer">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
				  <a href="../reportes_ventas/" class="<?=$buttonCancel;?>"> <-- Volver </a>
			  </div>
               </form> 
              </div>	  
            </div>         
        
	</div>
        
</div>


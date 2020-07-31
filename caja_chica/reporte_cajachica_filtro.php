<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$dbh = new Conexion();

?>

<div class="content">
	<div class="container-fluid">
		<div style="overflow-y:scroll; ">			 		
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Solicitud Facturación</h4>
                </div>
                <form class="" action="reporte_cajachica_print.php" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
		                <label class="col-sm-2 col-form-label">Instancia Caja Chica</label>
		                <div class="col-sm-8">
		                	<div class="form-group">
	                			<?php
								$sqlUO="SELECT codigo,nombre From tipos_caja_chica where cod_estadoreferencial=1";
								$stmt = $dbh->prepare($sqlUO);
								$stmt->execute();
								?>
								<select class="selectpicker form-control form-control-sm" data-style="btn btn-info" name="unidad" id="unidad"  required  onchange="ajax_cajachica_intancia(this)">
									<option value="">-</option>
								    <?php 
								    while ($row = $stmt->fetch()){ ?>
								      	<option value="<?=$row["codigo"];?>"><?=$row["nombre"];?></option><?php 
								 	} ?>
								</select>		                		
		                     </div>
		                </div>				             
                  	</div><!--div row-->
                  	<div class="row">
		                <label class="col-sm-2 col-form-label">Caja Chica</label>
		                <div class="col-sm-8">
		                	<div class="form-group" id="div_caja_chica">	                			
								          		
		                     </div>
		                </div>				             
                  	</div><!--div row-->	                
				            
	            </div><!--div fechas row-->
                <div class="card-footer ">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
				  <!-- <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
               </form> 
              </div>	  
            </div>         
        </div>	
	</div>
        
</div>


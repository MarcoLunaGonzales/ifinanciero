<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION["globalUnidad"];

$globalGestion=$_SESSION["globalGestion"];
$global_mes=$_SESSION["globalMes"];
$global_mes_actual=(int)date("m");
$dbh = new Conexion();
?>

<div class="content">
	<div class="container-fluid">
		<!-- <div style="overflow-y:scroll; ">			 		 -->
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Libro Compras Edición Facturas</h4>
                </div>
                <form class="" action="<?=$urlReporteComprasProy?>" target="_blank" method="POST">
                <div class="card-body">
                	<div class="row">
		                <label class="col-sm-2 col-form-label">Estado SR</label>
		                <div class="col-sm-8">
		                	<div class="form-group">
		                		<div id="">		
		                			<?php
									$sqlUO="SELECT uo.codigo, uo.nombre from estados_solicitudrecursos uo where uo.codigo<>2 order by 2 ";
									$stmt = $dbh->prepare($sqlUO);
									$stmt->execute();
									?>
										<select class="selectpicker form-control form-control-sm" name="estado[]" id="estado" multiple data-actions-box="true" required data-live-search="true">
										    <?php 
										    	while ($row = $stmt->fetch()){ 
											?>
										      	 <option value="<?=$row["codigo"];?>" <?=($row["codigo"]==5)?"selected":""?>><?=$row["nombre"];?></option>
							    	<?php 
										 		} 
								 	?>
										</select>
		                		</div>
		                     </div>
		                </div>				             
                  	</div><!--div row-->
                	<div class="row">
		                <label class="col-sm-2 col-form-label">Gestión</label>
		                <div class="col-sm-6">
		                	<div class="form-group">
		                		<select name="gestiones" id="gestiones" onChange="ajax_mes_de_gestion_mutiple(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
                                    <option value=""></option>
                                    <?php 
                                    $query = "SELECT codigo,nombre from gestiones where cod_estado=1 ORDER BY nombre desc";
                                    $stmt = $dbh->query($query);
                                    while ($row = $stmt->fetch()){ ?>
                                        <option value="<?=$row["codigo"];?>" <?=($row["codigo"]==$globalGestion)?"selected":""?> ><?=$row["nombre"];?></option>
                                    <?php } ?>
                                </select>
		                     </div>
		                </div>				             
                  	</div><!--div row-->
              		<div class="row">
		                 <label class="col-sm-2 col-form-label">Mes</label>
		                 <div class="col-sm-6">
		                	<div class="form-group">
		                		<div id="div_contenedor_mes">		
		                			<?php $sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c where c.cod_gestion=$globalGestion";
									$stmtg = $dbh->prepare($sql);
									$stmtg->execute();
									?>
									<select name="cod_mes_x[]" id="cod_mes_x" class="selectpicker form-control form-control-sm" data-style="btn btn-primary" multiple data-actions-box="true" required data-live-search="true">
									<?php
									  
									  while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {    
									    $cod_mes=$rowg['cod_mes'];    
									    $nombre_mes=$rowg['nombre_mes'];    
									  ?>
									  <option value="<?=$cod_mes;?>" <?=($cod_mes<=$global_mes_actual)?"selected":""?> ><?=$nombre_mes;?></option>
									  <?php 
									  }
									?>
									</select>
		                			
		                		</div>		                                
		                     </div>
		                  </div>
		            </div>
		            <div class="row d-none">
		            	<label class="col-sm-2 col-form-label">Sin Solicitud</label>
		                <div class="col-sm-1">
		                	<div class="form-group">
								<div class="togglebutton">
								    <label>
										<input type="checkbox" name="check_sin_sr" id="check_sin_sr" onChange="filtroFacturasSinSolicitudRecursos()">
										<span class="toggle"></span>
								    </label>
								</div>
							</div>
						</div>
						<div class="col-sm-8">
		                	<div class="form-group d-none" id="contenedor_oficinas_reporte">
								<div class="row">
		                	      <label class="col-sm-1 col-form-label">Oficina</label>
		                	      <div class="col-sm-5">
		                	      <div class="form-group">
		                	      	<div id="">		
		                	      		<?php
							      		$sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
									    $stmt = $dbh->prepare($sqlUO);
									    $stmt->execute();
									    ?>
										   <select class="selectpicker form-control form-control-sm" name="unidad[]" id="unidad" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
										       <?php 
										       	while ($row = $stmt->fetch()){ 
										   	?>
										      	 <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" <?=($row["codigo"]==$globalUnidad)?"selected":""?> ><?=$row["abreviatura"];?></option>
							    	         <?php 
										 		} 
								 	         ?>
										</select>
		                		      </div>
		                              </div>
		                         </div>				             
                  	         </div><!--div row -->
							</div>
						</div>
		            </div>
                  	
                <div class="card-footer">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
               
				  <!-- <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
               </form> 
              </div>	  
            </div>         
        <!-- </div>	 -->
	</div>
        
</div>



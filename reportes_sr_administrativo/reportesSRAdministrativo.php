<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';

$globalAdmin=$_SESSION["globalAdmin"];
$globalUnidad=$_SESSION["globalUnidad"];

$globalGestion=$_SESSION["globalGestion"];
$global_mes=$_SESSION["globalMes"];

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
                  <h4 class="card-title">Reporte Solicitud de Recursos Administración</h4>
                </div>
                <form class="" action="<?=$urlReporteSR?>" target="_blank" method="POST">
                <div class="card-body">
                	<div class="row">
                		
		                <label class="col-sm-2 col-form-label">Estado SR</label>
		                <div class="col-sm-10">
		                	<div class="form-group">
		                		<div id="">		
		                			<?php
									$sqlUO="SELECT uo.codigo, uo.nombre from estados_solicitudrecursos uo order by 2 ";
									$stmt = $dbh->prepare($sqlUO);
									$stmt->execute();
									?>
										<select class="selectpicker form-control form-control-sm" name="estado[]" id="estado" multiple data-actions-box="true" required data-live-search="true">
										    <?php 
										    	while ($row = $stmt->fetch()){ 
											?>
										      	 <option value="<?=$row["codigo"];?>" selected><?=$row["nombre"];?></option>
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
		                <div class="col-sm-4">
		                	<div class="form-group">
		                		<select name="gestiones" id="gestiones" onChange="ajax_mes_de_gestion_reloj(this);" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  data-show-subtext="true" data-live-search="true" required="true">
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
		                 <label class="col-sm-2 col-form-label">Mes</label>
		                 <div class="col-sm-4">
		                	<div class="form-group">
		                		<div id="div_contenedor_mes">		
		                			<?php $sql="SELECT c.cod_mes,(select m.nombre from meses m where m.codigo=c.cod_mes) as nombre_mes from meses_trabajo c where c.cod_gestion=$globalGestion";
									$stmtg = $dbh->prepare($sql);
									$stmtg->execute();
									?>
									<select name="cod_mes_x" id="cod_mes_x" class="selectpicker form-control form-control-sm" data-style="btn btn-primary"  required data-live-search="true">
									<?php
									  
									  while ($rowg = $stmtg->fetch(PDO::FETCH_ASSOC)) {    
									    $cod_mes=$rowg['cod_mes'];    
									    $nombre_mes=$rowg['nombre_mes'];    
									  ?>
									  <option value="<?=$cod_mes;?>" <?=($cod_mes==$global_mes)?"selected":""?> ><?=$nombre_mes;?></option>
									  <?php 
									  }
									?>
									</select>
		                			
		                		</div>		                                
		                     </div>
		                  </div>
		            </div>
		            <div class="row">
                  	<div class="col-sm-6">
                  		<div class="row">
			                 <label class="col-sm-4 col-form-label">Centro de Costos - Oficina</label>
			                 <div class="col-sm-8">
			                	<div class="form-group">
			                		<div id="div_contenedor_oficina_costo">
				                			<?php
											$sqlUO="SELECT uo.codigo, uo.nombre,uo.abreviatura from unidades_organizacionales uo order by 2";
											$stmt = $dbh->prepare($sqlUO);
											$stmt->execute();
											?>
												<select class="selectpicker form-control form-control-sm" name="unidad_costo[]" id="unidad_costo" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
												    <?php 
												    	while ($row = $stmt->fetch()){ 
													?>
												      	 <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" <?=($row["codigo"]==$globalUnidad)?"selected":""?> ><?=$row["abreviatura"];?></option>
									    	<?php 
												 		} 
										 	?>
												</select>			                			
			                		</div>
	                              <!-- <select class="selectpicker form-control form-control-sm" name="unidad_costo[]" id="unidad_costo" data-style="select-with-transition" multiple data-actions-box="true" required>
			  	                          <?php
			  	                          $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
				                          $stmt->execute();
				                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                          	$codigoX=$row['codigo'];
				                          	$nombreX=$row['nombre'];
				                          	$abrevX=$row['abreviatura'];
				                          ?>
				                           <option value="<?=$codigoX;?>"><?=$abrevX;?></option>	
				                           <?php
			  	                           }
			  	                           ?>
			                           </select> -->
			                      </div>
			                  </div>
			             </div>
      	             </div>
                  	<div class="col-sm-6">
                  		<div class="row">
			                 <label class="col-sm-4 col-form-label">Centro de Costos - Area</label>
			                 <div class="col-sm-8">
			                	<div class="form-group">
	                              <select class="selectpicker form-control form-control-sm" name="area_costo[]" id="area_costo" data-style="select-with-transition" multiple data-actions-box="true" required>
			  	                     <?php
			  	                     $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and (centro_costos=1 or codigo=1235) order by 2");
				                     $stmt->execute();
				                     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                     	$codigoX=$row['codigo'];
				                     	$nombreX=$row['nombre'];
				                     	$abrevX=$row['abreviatura'];
				                     ?>
				                     <option value="<?=$codigoX;?>" selected><?=$abrevX;?></option>	
				                       <?php
			  	                       }
			  	                       ?>
			                       </select>
			                      </div>
			                  </div>
			              </div>
				      </div>
                  </div><!--div row-->
                  <div class="row">
					<label class="col-sm-2 col-form-label">Personal</label>
					<div class="col-sm-4">
						<div class="form-group">
						<select class="selectpicker form-control form-control-sm" name="personal[]" id="personal" data-live-search="true" data-style="select-with-transition" data-size="4" multiple data-actions-box="true" required>	
						   <?php
						 $stmt = $dbh->prepare("SELECT distinct s.cod_personal,UPPER(CONCAT(p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno)) as nombre from solicitud_recursos s
join personal p on p.codigo=s.cod_personal
where s.cod_estadoreferencial<>2;");
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$codigoX=$row['cod_personal'];
							$nombreX=$row['nombre'];
						?>
						<option value="<?=$codigoX;?>" selected><?=$nombreX;?></option>	
						<?php
						   }
						   ?>
						</select>
						</div>
					</div>

	            	<label class="col-sm-2 col-form-label">Sin Filtro</label>
	                <div class="col-sm-1">
	                	<div class="form-group">
							<div class="togglebutton">
							    <label>
									<input type="checkbox" name="check_formato2" id="check_formato2">
									<span class="toggle"></span>
							    </label>
							</div>
						</div>
					</div>						
		            
				</div>
                <div class="row">
				    <div class="col-sm-6">
	                  		<div class="row">
				                 <label class="col-sm-4 col-form-label">Cuenta</label>
				                 <div class="col-sm-8">
				                	<div class="form-group">
		                              <select class="selectpicker form-control form-control-sm" data-live-search="true" title="-- Elija una cuenta --" name="cuenta[]" id="cuenta" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true" required>
				  	                        <?php
                                                $cuentaLista=obtenerCuentasListaSolicitud(); //null para todas las iniciales del numero de cuenta obtenerCuentasLista(5,[5,4]);
                                              while ($rowCuenta = $cuentaLista->fetch(PDO::FETCH_ASSOC)) {
                                                $codigoX=$rowCuenta['codigo'];
                                                $numeroX=$rowCuenta['numero'];
                                                $nombreX=$rowCuenta['nombre'];
                                              ?>
                                              <option value="<?=$codigoX;?>" selected >[<?=$numeroX?>] <?=$nombreX;?></option>  
                                              <?php
                                                }
                                                ?>
								         </select>
				                      </div>
				                  </div>
				              </div>
					      </div>
                 </div><!--div row-->
                  	
                <div class="card-footer">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
                	<a href="../reportes_ventas/" class="<?=$buttonCancel;?>">  Volver </a>
			  </div>
               </form> 
              </div>	  
            </div>         
        <!-- </div>	 -->
	</div>
        
</div>

<div class="modal fade" id="modal_descargarTXT" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-sm" role="document">    
      <div class="modal-content">
        <div class="modal-header">          
          <h3 class="modal-title" id="myModalLabel"><b>Correcto</b></h3>
        </div>
        <div class="modal-body">                
              <center><span>El proceso se completó correctamente!</span></center>     
        </div>    
        <div id="contenedor_DescargaTxt">
          
        </div>    
      </div>
    </form>
  </div>
</div>

<?php
require_once 'conexion.php';
require_once 'comprobantes/configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
$globalAdmin=$_SESSION["globalAdmin"];

$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));

$fechaDesde="01/".$m."/".$y;
$fechaHasta=$d."/".$m."/".$y;
$dbh = new Conexion();
?>

<div class="content">
	<div class="container-fluid">
		<div style="overflow-y: scroll;">
			
	            <div class="col-md-12">
	              	<div class="card">
		                <div class="card-header <?=$colorCard;?> card-header-icon">
		                  <div class="card-icon">
		                    <i class="material-icons"><?=$iconCard;?></i>
		                  </div>
		                  <h4 class="card-title">Reporte Libro Diario</h4>
		                </div>
		                <form class="" action="<?=$urlReporteDiario?>" target="_blank" method="POST">
			                <div class="card-body">
			                	<div class="row">
				                  	<div class="col-sm-6">
				                  		<div class="row">
							                 <label class="col-sm-4 col-form-label">Entidad</label>
							                 <div class="col-sm-8">
							                	<div class="form-group">				                		
					                                <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)"> -->
					                                <select class="selectpicker form-control form-control-sm" name="entidad[]" id="entidad" required onChange="ajax_entidad_Oficina()" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true">				  	   
							  	                        <?php
							  	                        $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM entidades where cod_estadoreferencial=1 order by 2");
								                         $stmt->execute();
								                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								                          	$codigoX=$row['codigo'];
								                          	$nombreX=$row['nombre'];
								                          	$abrevX=$row['abreviatura'];
								                          ?>
								                       <option value="<?=$codigoX;?>"><?=$nombreX?></option>	
								                         <?php
							  	                         }
							  	                         ?>
							                        </select>
							                     </div>
							                  </div>
							            </div>
				      	             </div>
				                  	<div class="col-sm-6">
				                  		<div class="row">
							                 <label class="col-sm-4 col-form-label">Gestion</label>
							                 <div class="col-sm-8">
							                	<div class="form-group">
					                               <select class="selectpicker form-control form-control-sm" name="gestion" id="gestion" data-style="<?=$comboColor;?>" required onChange="AjaxGestionFechaDesde(this)">				  	   
							  	                        <?php
							  	                        $stmt = $dbh->prepare("SELECT codigo, nombre FROM gestiones where cod_estado=1 order by 2 desc");
								                         $stmt->execute();
								                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								                          	$codigoX=$row['codigo'];
								                          	$nombreX=$row['nombre'];
								                          ?>
								                       <option value="<?=$codigoX;?>"><?=$nombreX?></option>	
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
				                  	<div class="col-sm-6">	                  		
				                  		<div class="row">
							                 <label class="col-sm-4 col-form-label">Unidad Organizacional</label>
							                 <div class="col-sm-8">
							                	<div class="form-group">
							                		<div id="div_contenedor_oficina1">
							                		</div>
					                          <!--     <select class="selectpicker form-control form-control-sm" name="unidad" id="unidad" data-style="<?=$comboColor;?>" required>
							  	                      <option disabled selected="selected" value="">Seleccionar</option>
							  	                        <?php
							  	                        $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 3");
								                         $stmt->execute();
								                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
								                          	$codigoX=$row['codigo'];
								                          	$nombreX=$row['nombre'];
								                          	$abrevX=$row['abreviatura'];
								                          ?>
								                       <option value="<?=$codigoX;?>"><?=$nombreX?></option>	
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
						                <label class="col-sm-4 col-form-label">Tipo de comprobante</label>
						                <div class="col-sm-8">
						                	<div class="form-group">
				                              <select class="selectpicker form-control form-control-sm" name="tipo_comprobante[]" id="tipo_comprobante" data-style="select-with-transition" multiple data-actions-box="true" required>
						  	                        <?php
										  	         $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 order by 1");
											         $stmt->execute();
											         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
											         	$codigoX=$row['codigo'];
											         	$nombreX=$row['nombre'];
											         	$abrevX=$row['abreviatura'];
											         ?>
											         <option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
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
			                  	<div class="col-sm-6">
			                  		<div class="row">
						                 <label class="col-sm-4 col-form-label">Desde</label>
						                 <div class="col-sm-8">
						                	<div class="form-group">
						                		<div id="div_contenedor_fechaI">
							                		<input type="text" class="form-control datepicker " autocomplete="off" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaDesde?>">
							                	</div>
				                                
						                     </div>
						                  </div>
						             </div>
			      	             </div>
			                  	<div class="col-sm-6">
			                  		<div class="row">
						                 <label class="col-sm-4 col-form-label">Hasta</label>
						                 <div class="col-sm-8">
						                	<div class="form-group">
				                               <div id="div_contenedor_fechaH">
						                			<input type="text" class="form-control datepicker " autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde?>" max="<?=$fechaHasta?>" value="<?=$fechaHasta?>">
						                		</div>
						                    </div>
						                  </div>
						              </div>
							      </div>
			                  </div><!--div row-->
			                  <div class="row">
			                  	<div class="col-sm-6">
			                  		<div class="row">
						                 <label class="col-sm-4 col-form-label">Moneda Adicional</label>
						                 <div class="col-sm-8">
						                	<div class="form-group">
				                              <select class="selectpicker form-control form-control-sm" name="moneda" id="moneda" data-style="<?=$comboColor;?>" required>
						  	                 
						  	                        <?php
						  	                        $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM monedas where cod_estadoreferencial=1 order by 2");
							                         $stmt->execute();
							                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							                          	$codigoX=$row['codigo'];
							                          	$nombreX=$row['nombre'];
							                          	$abrevX=$row['abreviatura'];
							                          	// if($codigoX!=1){
			                                              ?>
							                                 <option value="<?=$codigoX;?>"><?=$nombreX?> (<?=$abrevX;?>)</option>	
							                             <?php
							                          	// }
							                          
						  	                         }
						  	                         ?>
						                         </select>
						                      </div>
						                  </div>
						             </div>
			      	             </div>
			      	             <div class="col-sm-6">
			      	             	<div class="row">
						               <label class="col-sm-4 col-form-label">Glosa completa</label>
			                           <div class="col-sm-8">
						                  <div class="form-group">
			      	             	          <div class="form-check">
			                                    <label class="form-check-label">
			                                      <input class="form-check-input" type="checkbox" id="glosa_len" name="glosa_len[]" checked value="1">
			                                      <span class="form-check-sign">
			                                        <span class="check"></span>
			                                      </span>
			                                    </label>
			                                  </div>
			                                </div>  
			                             </div>     
			                        </div>  
			      	             </div>
			      	           </div><!--div row--> 
			                </div><!--card body-->
			                <div class="card-footer fixed-bottom">
			                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
							   <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>
						    </div>
		               	</form> 
	              	</div>	  
	            </div>
	        </div>	
	
          
    </div>
</div>


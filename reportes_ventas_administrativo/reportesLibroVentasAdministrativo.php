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


$fechaActual=date("m/d/Y");
/*$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));*/
$fechaActualX=date("Y-m-d");

/*$fechaDesde=$y."-01-01";
$fechaHasta=$y."-".$m."-".$d;

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";*/

$fechaDesde=$fechaActualX;
$fechaHasta=$fechaActualX;

$fechaDesde2=$fechaActualX;
$fechaHasta2=$fechaActualX;


$dbh = new Conexion();
$stmt = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre, p.nivel from plan_cuentas p order by p.numero");
$stmt->execute();
$i=0;
  echo "<script>var array_cuenta=[];</script>";
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	 $codigoX=$row['codigo'];
	 $numeroX=$row['numero'];
	 $nombreX=$row['nombre'];
	 $nivelX=$row['nivel'];
	 $nombreCuenta=formateaPlanCuenta($numeroX." ".$nombreX,$nivelX);
	 $arrayNuevo[$i][0]=$codigoX;
	 $arrayNuevo[$i][1]=$numeroX;
	 $arrayNuevo[$i][2]=$nombreCuenta;
	 $arrayNuevo[$i][3]=$nivelX;
		$i++;
	}
?>

<div class="content">
	<div class="container-fluid">
		<!-- <div style="overflow-y:scroll; ">		 -->	 		
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Detalle de Recaudaciones</h4>
                </div>
                <form class="" action="<?=$urlReporteVentasAdministrativo?>" target="_blank" method="POST">
                	<input type="hidden" id="reporteIngreso" value="1">
                <div class="card-body">

	                <div class="row">
	                  	<div class="col-sm-6">
	                  		<div class="row">
				                 <label class="col-sm-4 col-form-label">Desde</label>
				                 <div class="col-sm-8">
				                	<div class="form-group">
				                		<div id="div_contenedor_fechaI">				                			
				                			<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" value="<?=$fechaDesde?>">	
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
				                			<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" value="<?=$fechaHasta?>">
				                		</div>
		                               
				                    </div>
				                  </div>
				              </div>
					      </div>
	                </div><!--div row-->


            	<div class="row">
					     <div class="col-sm-6">
	                  		<div class="row">
			      	             
				               <label class="col-sm-4 col-form-label">Forma de Pago</label>
	                           <div class="col-sm-8">
				                  <div class="form-group">
	      	             	           <select class="selectpicker form-control form-control-sm" name="forma_pago[]" id="forma_pago"  data-style="select-with-transition" multiple data-actions-box="true" required>				  	   
				  	                        <?php
				  	                        $stmt = $dbh->prepare("SELECT codigo, nombre FROM tipos_pago where cod_estadoreferencial=1 order by 2 desc");
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
			                 <label class="col-sm-4 col-form-label">Oficina</label>
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
			                 <label class="col-sm-4 col-form-label">Area</label>
			                 <div class="col-sm-8">
			                	<div class="form-group">
	                              <select class="selectpicker form-control form-control-sm" name="area_costo[]" id="area_costo" data-style="select-with-transition" multiple data-actions-box="true" required>
			  	                     <?php
			  	                     $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
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
							<option value="0">TIENDA</option>	
						   <?php
						 $stmt = $dbh->prepare("SELECT DISTINCT f.cod_personal,UPPER(CONCAT(p.primer_nombre,' ',p.otros_nombres,' ',p.paterno,' ',p.materno)) as nombre from facturas_venta f join personal p on p.codigo=f.cod_personal order by 2");
						$stmt->execute();
						while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
							$codigoX=$row['cod_personal'];
							$nombreX=$row['nombre'];
						?>
						<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
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
					
                </div><!--card body-->
                <div class="card-footer ">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
				   <a href="../reportes_ventas/" class="<?=$buttonCancel;?>">  Volver </a>
			  </div>
               </form> 
              </div>	  
            </div>         
        <!-- </div>	 -->
	</div>
        
</div>


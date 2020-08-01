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
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde=$y."-01-01";
$fechaHasta=$y."-".$m."-".$d;

$fechaDesde2=$y."-01-01";
$fechaHasta2=$y."-12-31";

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
		<!-- <div style="overflow-y:scroll; ">			 		 -->
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Ingresos por Área</h4>
                </div>
                <form class="" action="<?=$urlReporteIngresoFacturacion_servicio?>" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
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
				                 <label class="col-sm-4 col-form-label">Desde</label>
				                 <div class="col-sm-8">
				                	<div class="form-group">
				                		<div id="div_contenedor_fechaI">				                			
				                			<input type="date" class="form-control" autocomplete="off" name="fecha_desde" id="fecha_desde" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaDesde?>">	
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
				                			<input type="date" class="form-control" autocomplete="off" name="fecha_hasta" id="fecha_hasta" min="<?=$fechaDesde2?>" max="<?=$fechaHasta2?>" value="<?=$fechaHasta?>">
				                		</div>
		                               
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
			                 <label class="col-sm-4 col-form-label">Servicio</label>
			                 <div class="col-sm-8">
			                	<div class="form-group">
	                              <select class="selectpicker form-control form-control-sm" name="servicios[]" id="servicios" data-style="select-with-transition" multiple data-actions-box="true" required>
			  	                     <?php
			  	                     $stmt = $dbh->prepare("SELECT IdClaServicio,Codigo,descripcion_n2 from cla_servicios GROUP BY IdTipo ORDER BY descripcion_n2");
				                     $stmt->execute();
				                     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                     	$codigoX=$row['IdClaServicio'];
				                     	$nombreX=$row['descripcion_n2'];
				                     	$abrevX=$row['Codigo'];
				                     ?>
				                     <option value="<?=$codigoX;?>" selected><small><?=$nombreX?> (<?=$abrevX;?>)</small></option>	
				                       <?php
			  	                       }
			  	                       ?>
			                       </select>
			                      </div>
			                  </div>
			              </div>
				      </div>
                  </div><!--div row-->

                </div><!--card body-->
                <div class="card-footer ">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
				  <!-- <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
               </form> 
              </div>	  
            </div>         
        <!-- </div>	 -->
	</div>
        
</div>


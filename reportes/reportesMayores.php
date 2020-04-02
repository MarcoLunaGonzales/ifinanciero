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
		<div style="overflow-y:scroll; ">			 		
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Libro Mayor</h4>
                </div>
                <form class="" action="<?=$urlReporteMayor?>" target="_blank" method="POST">
                <div class="card-body">
                  	<div class="row">
	                  	<div class="col-sm-6">
	                  		<div class="row">
				                <label class="col-sm-4 col-form-label">Entidad</label>
				                <div class="col-sm-8">
				                	<div class="form-group">				                		
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
				                <label class="col-sm-4 col-form-label">Oficina</label>
				                <div class="col-sm-8">
				                	<div class="form-group">
				                		<div id="div_contenedor_oficina1">				                			
				                		</div>
				                     </div>
				                </div>
				             </div>
	      	             </div>
	                  	<div class="col-sm-6">
	                  		<div class="row">
				                 <label class="col-sm-4 col-form-label">Cuenta</label>
				                 <div class="col-sm-8">
				                	<div class="form-group">
		                              <select class="selectpicker form-control form-control-sm" data-live-search="true" title="-- Elija una cuenta --" name="cuenta[]" id="cuenta" multiple data-actions-box="true" data-style="select-with-transition" data-actions-box="true" required>
				  	                        <?php
				  	                        for ($i=0; $i < count($arrayNuevo); $i++) { 
				  	                        	?><option value="<?=$arrayNuevo[$i][0];?>@normal"><?=$arrayNuevo[$i][2]?></option>	<?php
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
			                 <label class="col-sm-4 col-form-label">Areax</label>
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
				                     <option value="<?=$codigoX;?>"><?=$abrevX;?></option>	
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
                                              ?>
				                                 <option value="<?=$codigoX;?>"><?=$nombreX?> (<?=$abrevX;?>)</option>	
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
				  <!-- <a href="?opcion=listComprobantes" class="<?=$buttonCancel;?>"> <-- Volver </a>-->
			  </div>
               </form> 
              </div>	  
            </div>         
        </div>	
	</div>
        
</div>


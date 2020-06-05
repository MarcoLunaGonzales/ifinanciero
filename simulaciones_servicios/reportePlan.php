<?php
require_once 'conexion.php';
require_once 'configModule.php';
require_once 'styles.php';
require_once 'functions.php';
require_once 'functionsGeneral.php';
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
		<div style="overflow-y:scroll; ">			 		
            <div class="col-md-12">
              <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons"><?=$iconCard;?></i>
                  </div>
                  <h4 class="card-title">Reporte Planificacion - Ejecución</h4>
                </div>
                <form class="" id="reportePlanificacion" action="<?=$urlReportePlan?>" target="_blank" method="POST">
                <div class="card-body">
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
			                 <label class="col-sm-4 col-form-label">Clientes</label>
			                 <div class="col-sm-8">
			                	<div class="form-group">
			                		<div id="div_contenedor_oficina_costo">
				                			<?php
											$sqlUO="SELECT c.codigo, c.nombre FROM clientes c where c.cod_estadoreferencial=1 order by 2";
											$stmt = $dbh->prepare($sqlUO);
											$stmt->execute();
											?>
												<select class="selectpicker form-control form-control-sm" name="tipo_curso[]" id="tipo_curso" data-style="select-with-transition" multiple data-actions-box="true" required data-live-search="true">
												    <?php 
												    	while ($row = $stmt->fetch()){ 
												    		$words = explode(" ", $row['nombre']);
                                                            $acronym = "";
                                                            foreach ($words as $w) {
                                                            	if(!(strtolower($w)=="de"||strtolower($w)=="el"||strtolower($w)=="la"||strtolower($w)=="y")){
                                                            		$acronym .= $w[0];
                                                            	}        
                                                            }
												    		$abreviatura = $acronym;
													?>
												      	 <option value="<?=$row["codigo"];?>" data-subtext="<?=$row["nombre"];?>" ><?=$abreviatura;?></option>
									    	<?php 
												 		} 
										 	?>
												</select>			                			
			                		</div>
			                      </div>
			                  </div>
			             </div>
      	             </div>
                  </div><!--div row-->
                  <div class="row">
      	             <div class="col-sm-6">
      	             	<div class="row">
			               <label class="col-sm-4 col-form-label">Resumido (Cuenta)</label>
                           <div class="col-sm-8">
			                  <div class="form-group">
      	             	          <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="resumido" name="resumido[]" checked value="1" onchange="cambiarReporteResumido()">
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
      	           <div class="row d-none" id="div_solicitados">
      	           	<div class="col-sm-6">
      	             	<div class="row">
			               <label class="col-sm-4 col-form-label">Incluir Items sin Solicitud de Recursos</label>
                           <div class="col-sm-8">
			                  <div class="form-group">
      	             	          <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="solicitados" name="solicitados[]" checked value="1">
                                      <span class="form-check-sign">
                                        <span class="check"></span>
                                      </span>
                                    </label>
                                  </div>
                                </div>  
                             </div>     
                        </div>  
      	             </div>
      	             <div class="col-sm-6">
      	             	<div class="row">
			               <label class="col-sm-4 col-form-label text-info">Incluir Costos Fijos</label>
                           <div class="col-sm-8">
			                  <div class="form-group">
      	             	          <div class="form-check">
                                    <label class="form-check-label">
                                      <input class="form-check-input" type="checkbox" id="costos_fijos" name="costos_fijos[]" value="1">
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
      	           <br>
                </div><!--card body-->
                <div class="card-footer fixed-bottom">
                	<button type="submit" class="<?=$buttonNormal;?> bg-table-primary">VER REPORTE</button>
			  </div>
               </form> 
              </div>	  
            </div>         
        </div>	
	</div>
        
</div>
<script>
$(document).ready(function() {
    $("#reportePlanificacion").submit(function(e) {
        if ($("#tipo_curso option:selected").length > 100) {
         $("#tipo_curso").val("");	
         $('.selectpicker').selectpicker("refresh"); 
         Swal.fire("Informativo!", "Solo puede seleccionar un Max 100 Clientes!", "warning");
         return false;   
        }
    });
});
</script>


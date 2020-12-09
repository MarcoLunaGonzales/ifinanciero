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
	$mensajeAlerta="";
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
                  <h4 class="card-title">Reporte Planificacion - Ejecución Cursos</h4>
                </div>
                <form id="reporte_cursos" class="" action="<?=$urlReportePlanCurso?>" method="POST">
                <div class="card-body">
                  <div class="row">
                  	<div class="col-sm-6">
                  		<div class="row">
			                 <label class="col-sm-4 col-form-label">Curso</label>
			                 <div class="col-sm-8">
			                	<div class="form-group">
			                		<div id="div_contenedor_oficina_costo">
				                			<?php
				                			$queryCurso="and s.IdCurso>0";
				                			if(isset($_GET['s'])){
                                             $queryCurso="and s.IdCurso=".$_GET['s'];
				                			}
											$sqlUO="SELECT DISTINCT s.IdCurso,ibnorca.codigo_curso(s.IdCurso) as CodigoCurso from simulaciones_costos s WHERE s.cod_estadosimulacion=3 $queryCurso;";
											$stmt = $dbh->prepare($sqlUO);
											$stmt->execute();
											$indexCurso=0;
											?>
												<select class="selectpicker form-control form-control-sm" name="tipo_curso" id="tipo_curso" data-style="btn btn-info" required>
												    <?php 
												    	while ($row = $stmt->fetch()){ 
												    		$indexCurso++;												   
												    		if(isset($_GET['s'])){
												    			if($row["IdCurso"]==$_GET['s']){
												    			  ?><option value="<?=$row["IdCurso"];?>"><?=$row["CodigoCurso"];?></option><?php 	
												    			}                                                               
												    		}else{
      															?><option value="<?=$row["IdCurso"];?>"><?=$row["CodigoCurso"];?></option><?php 
												    		}													      
												 		} 

										  if($indexCurso==0&&isset($_GET['s'])){
										  	$mensajeAlerta="No hay propuestas con modulos relacionados al curso!";
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
                  <div class="row">
                  	<div class="col-sm-6">
                  		<div class="row">
			                 <label class="col-sm-12 col-form-label text-warning font-weight-bold"><?=$mensajeAlerta?></label>
			             </div>
      	             </div>
      	           </div>
                </div><!--card body-->
                <?php 
                if($mensajeAlerta==""){
                  ?>
                 <div class="card-footer fixed-bottom">
                	<button type="submit" class="<?=$buttonNormal;?> bg-table-primary">VER REPORTE</button>
			  </div>
                  <?php  
                }
                ?>                
               </form> 
              </div>	  
            </div>         
        </div>	
	</div>
        
</div>

<?php 
if($mensajeAlerta==""&&isset($_GET['s'])){
	?>
    <script>
    $(document).ready(function() {
      $( "#resumido" ).prop( "checked", false );
      $("#reporte_cursos").submit();
    });
  </script>
	<?php
}
?>
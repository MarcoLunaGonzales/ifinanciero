<?php

session_start();

require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once 'configModule.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';


// $globalAdmin=$_SESSION["globalAdmin"];
// $globalUnidad=$_SESSION["globalUnidad"];


$fechaActual=date("m/d/Y");
$m=date("m");
$y=date("Y");
$d=date("d",(mktime(0,0,0,$m+1,1,$y)-1));
$fechaDesde="2020-01-01";
$fechaHasta=$y."-".$m."-".$d;

$fechaDesde2="2020-01-01";
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
                  <h4 class="card-title">libretas vs facturas (fechas)</h4>
                </div>
                <form class="" action="reporte_verificacion_libretas_relacion_print.php" target="_blank" method="POST">
                <div class="card-body">
               
	                <div class="row">
                      <div class="col-sm-4">
                        <div class="form-group">                            
                                  <!-- <select class="selectpicker form-control form-control-sm" name="entidad" id="entidad" data-style="<?=$comboColor;?>" required onChange="ajax_entidad_Oficina(this)">  -->
                            <select class="selectpicker form-control form-control-sm" name="libretas" id="libretas" data-style="<?=$comboColor;?>" required>                           
                            <?php
                            $stmt = $dbh->prepare("SELECT l.*,b.abreviatura as banco from libretas_bancarias l join bancos b on b.codigo=l.cod_banco where l.cod_estadoreferencial=1 order by l.nombre");
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              $codigoX=$row['codigo'];
                              $nombreX=$row['nombre'];
                              $bancoX=$row['banco'];
                              ?>
                              <option value="<?=$codigoX;?>"><?=$nombreX?> - <?=$bancoX?></option>  
                              <?php
                             }
                           ?>
                               </select>
                            </div>
                        </div> 
	                  	<div class="col-sm-4">
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
	                  	<div class="col-sm-4">
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
                  </div><!--div row-->

                </div><!--card body--> 
                <div class="card-footer ">
                	<button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>
			    </div>
               </form> 
              </div>	  
            </div>         
        <!-- </div>	 -->
	</div>
        
</div>


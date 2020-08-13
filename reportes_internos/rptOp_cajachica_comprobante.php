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
        <div class="col-md-12">
            <div class="card">
                <div class="card-header <?=$colorCard;?> card-header-icon">
                  	<div class="card-icon">
                    	<i class="material-icons"><?=$iconCard;?></i>
                  	</div>
                  	<h4 class="card-title">Reporte Caja Chica Factura</h4>
                </div>
                <form  action="reporte_cajachica_comprobante.php" target="_blank" method="POST">
	                <div class="card-body">		          
                  		<div class="row">
			                <label class="col-sm-2 col-form-label">Instancia Caja Chica</label>
			                <div class="col-sm-6">
			                	<div class="form-group">
			                		<div id="div_contenedor_fechaI">
			                			<select class="selectpicker form-control form-control-sm" name="tipo_cajachica" id="tipo_cajachica" data-style="<?=$comboColor;?>" required>				  	   
				  	                        <?php
				  	                        $stmt = $dbh->prepare("SELECT codigo,nombre From tipos_caja_chica order by nombre asc");
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
		            </div>
		            <div class="card-footer ">
		                <button type="submit" class="<?=$buttonNormal;?>">Ver Reporte</button>				  
					</div>
	            </form>
	    	</div>
		</div>        
	</div>
</div>


<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';

require_once '../functionsGeneral.php';
require_once '../functions.php';
require_once 'configModule.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalNombreUnidad=$_SESSION['globalNombreUnidad'];
$globalArea=$_SESSION["globalArea"];
$globalAdmin=$_SESSION["globalAdmin"];

setlocale(LC_TIME, "Spanish");
$fechaActual=date("Y-m-d");
$mes=strToUpper(strftime('%B',strtotime($fechaActual)));
$d=strftime('%d',strtotime($fechaActual));
$m=strftime('%m',strtotime($fechaActual));
$y=strftime('%Y',strtotime($fechaActual));

$fechaInicio=$y."-".$m."-01";
$du=date("d",(mktime(0,0,0,$m+1,1,$y)-1));

if(isset($_GET['codigo'])){
	$globalCode=$_GET['codigo'];
	//if($globalCode==1){ }
}else{
	$globalCode=0;
}
$data = obtenerMoneda($_GET['codigo']);
// bindColumn
$data->bindColumn('codigo', $codigo);
$data->bindColumn('abreviatura', $abreviatura);
$data->bindColumn('nombre', $nombre);
$data->bindColumn('cod_estadoreferencial', $codEstRef);
?>

<form id="formHistorial" class="form-horizontal" action="saveHist.php" method="post">
<div class="content">
	<div class="container-fluid">
			<input type="hidden" name="codigo_tipo_cambio" id="codigo_tipo_cambio" value="<?=$globalCode;?>">
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Editar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
					<div class="row">
				 	<?php 
                  while ($row = $data->fetch(PDO::FETCH_BOUND)) {
				 	?>
						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Gestion Actual</label>
					  			<input class="form-control" type="text" name="gestion" value="<?=$y;?>" id="gestion" readonly="true" />
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group">
						  		<label class="bmd-label-static">Moneda</label>
						  		<input class="form-control" type="text" name="moneda" value="<?=$nombre;?>" id="moneda" readonly="true" />
							</div>
						</div>

						<div class="col-sm-3">
							<div class="form-group">
						  		<label class="bmd-label-static">Mes Actual</label>
						  		<input class="form-control" type="text" name="mes" value="<?=$mes;?>" id="mes" readonly="true"/>
							</div>
						</div>
						<div class="form-group col-sm-1">
						  		<select class="selectpicker form-control form-control-sm" data-style="btn btn-info"name="sel_ano" id="sel_ano" onchange="cargarTipoCambio(<?=$globalCode?>)">
						  			<?php 
                                   for ($i=(int)$y; $i > ((int)$y-5); $i--) { 
                                   	?><option value="<?=$i?>"><?=$i?></option><?php
                                   }
						  			?>
						  		</select>
						</div>
						<div class="form-group col-sm-2">
						  		<select class="selectpicker form-control form-control-sm" data-style="btn btn-info" name="sel_mes" id="sel_mes" onchange="cargarTipoCambio(<?=$globalCode?>)">
						  			<?php 
                                   for ($i=1; $i < 13; $i++) { 
                                   	   if($i==(int)$m){
                                          ?><option selected value="<?=$i?>"><?=strftime('%B',strtotime($y."-".$i."-01"));?></option><?php
                                     	}else{
                                         ?><option value="<?=$i?>"><?=strftime('%B',strtotime($y."-".$i."-01"));?></option><?php
                                    	}     	
                                   }
						  			?>
						  		</select>
						</div>
						<div class="form-group col-sm-1">
      		              <a href="#" class="btn btn-just-icon btn-danger btn-link" onclick="cargarTipoCambio(<?=$globalCode?>)">
      		              	<i class="material-icons">search</i>
      		              </a>
          		       </div>	
					</div>
                   <?php } ?>
				</div>
			</div>	

			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h6 class="card-title">Historial</h6>
					</div>
				</div>
				<div class="card-body ">
					<fieldset id="fiel" style="width:100%;border:0;">
		                    <div class="row">
		                    	<div class="col-sm-6">
		                    		<label class="text-muted"><small>(Tabla de <b>VALORES REGISTRADOS</b> mes de <?=$mes?> - gestion <?=$y?>)</small></label>
		                    		<table class="table table-striped table-success">
		                    	 <?php
						$tipoCambio=obtenerTipoCambio($globalCode,$fechaInicio,$fechaActual);
						$idFila=1;$index=0;$dias=[];
						           ?>
                                    <thead>
                                    	<tr class="bg-dark text-white">
                                    	   <th>#</th>
                                           <th>Fecha</th>
                                           <th>Valor Bs.</th>
                                           <th>Moneda</th>
                                    	</tr>
                                    </thead>
                                    <tbody>
						           <?php
						while ($row = $tipoCambio->fetch(PDO::FETCH_ASSOC)) {
							$dias[$index]=strftime('%d',strtotime($row['fecha']));
							?>
							<?php
							$codigo=$row['id'];
							$codMoneda=$row['cod_moneda'];
							$fecha=$row['fecha'];
							$valor=$row['valor'];
							?>
                             <tr><td><?=$idFila?></td><td><?=$fecha?></td><td><?=$valor?></td><td><?=$abreviatura?></td></tr>
							<?php
                           $idFila++;
                           $index++;
                         }
						 ?>	      </tbody>
		                    	</table>
		                    	</div>
		                    	<div class="col-sm-6">
		                    		<label class="text-muted"><small>(Tabla de <b>NUEVOS REGISTROS</b> mes de <?=$mes?> - gestion <?=$y?>)</small></label>
		                    		<table class="table table-sm">
                                    <thead>
                                    	<tr class="bg-dark text-white">
                                    	   <th>#</th>
                                           <th>Fecha</th>
                                           <th>Valor Bs.</th>
                                           <th>Moneda</th>
                                    	</tr>
                                    </thead>
                                    <tbody>
						           <?php
						           $idFila=1;
                                    //$dias=(int)$du-$idFila;
                                    $dd=(int)$idFila;$count=1;
                                    for ($i=0; $i < (int)$du; $i++) {
                                    	$existe=0;
                                    	 for ($j=0; $j < $index; $j++) { 
                                    	 	if(($i+1)==(int)$dias[$j]){
                                    	 		$existe=1;
                         	                 }
                                    	 }
                                    	 if($existe==0){
                                    	 	if($idFila<10){$ddi="0".$idFila;}else{$ddi=$idFila;}
                                    	 	if((int)$ddi==(int)$d){$estilo="bg-warning";}else{$estilo="";}
                                    	 	?>
                                        <tr class="<?=$estilo?>"><td><?=$count?></td><td><input type="text" readonly value="<?=$y?>-<?=$m?>-<?=$ddi?>" class="form-control" id="fecha<?=$count?>" name="fecha<?=$count?>"></td><td><input type="number" class="form-control" id="valor<?=$count?>" placeholder="ingrese un valor" name="valor<?=$count?>" step="0.0000001"></td><td><?=$abreviatura?></td></tr>
							            <?php
                                         $count++;
							             }
							            $idFila++;
                                      }
						             ?>		    
						           </tbody>
		                    	</table>
		                    	</div>
		                    </div>
		                   <input type="text" name="cantidad_filas" id="cantidad_filas" value="<?=$count?>">		            
		              </fieldset>
		            </div>
				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>

				  	</div>
			  </div><!--card end-->
		 </div>	
      </div>
</form>
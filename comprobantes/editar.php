<?php
session_start();
set_time_limit(0);
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

$data = obtenerComprobante($_GET['codigo']);
// bindColumn
$data->bindColumn('codigo', $codigo);
$data->bindColumn('cod_gestion', $gestion);
$data->bindColumn('abreviatura', $unidad);
$data->bindColumn('fecha', $fechaComprobante);
$data->bindColumn('cod_tipocomprobante', $tipoComprobante);
$data->bindColumn('numero', $nroCorrelativo);
$data->bindColumn('glosa', $glosaComprobante);
$fechaActualModal=date("d/m/Y");
if(isset($_GET['codigo'])){
	$globalCode=$_GET['codigo'];
}else{
	$globalCode=0;
}
$cont=contarComprobantesDetalle($globalCode);
$cont->bindColumn('total', $contReg);
while ($row = $cont->fetch(PDO::FETCH_BOUND)) {
 $contadorRegistros=$contReg;
}

if($contadorRegistros<=50){
	?><script>window.location.href="edit_prueba.php?codigo="+<?=$globalCode?>;</script><?php
}
?>
<input type="hidden" id="codigo_comprobante" value="<?=$globalCode?>">
<div class="content">
	<div class="container-fluid">
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Edici&oacute;n del comprobante</h4>
					</div>
				</div>
				<div class="card-body ">
					<div class="row">
				 	<?php 
                  while ($row = $data->fetch(PDO::FETCH_BOUND)) {
				 	?>
						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Gestion</label>
					  			<input class="form-control" type="text" name="gestion" value="<?=$gestion;?>" id="gestion" readonly="true" />
							</div>
						</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad_organizacional" value="<?=$unidad;?>" id="unidad_organizacional" readonly="true" />
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Fecha</label>
						  		<input class="form-control" type="text" name="fecha" value="<?=$fechaComprobante;?>" id="fecha" readonly="true"/>
							</div>
						</div>
                        <?php
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 and codigo=$tipoComprobante order by 1");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$nombreTipoComprobanteX=$row['nombre'];
							  	}
							  	?>
						<div class="col-sm-2">
				        	<div class="form-group">
				        		<label class="bmd-label-static">Tipo</label>
						        <input class="form-control" type="text" name="tipo_comprobante" value="<?=$nombreTipoComprobanteX;?>" id="tipo_comprobante" readonly="true"/>
							</div>
				      	</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label for="nro_correlativo" class="bmd-label-static">#</label>
						  		<div id="divnro_correlativo"><input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" readonly="true" value="<?=$nroCorrelativo?>" /></div>
							</div>
						</div>
						
					    <div class="col-sm-4">
						    <div class="form-group">
				          		<label for="glosa" class="bmd-label-static">Glosa</label>
								<textarea class="form-control" name="glosa" readonly id="glosa" required="true" rows="2" value=""><?=$glosaComprobante?></textarea>
							</div>
						</div>
					</div>
                   <?php } ?>
				</div>   
			</div>	

			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h6 class="card-title">Registros (<?=$contadorRegistros?>)</h6>
					</div>
				</div>
				<div class="card-body ">
                   <div class="col-sm-8 div-center">
                   	  <table id="tablePaginatorReport" class="table table-bordered table-condensed table-striped table-sm">
                             <thead>
                                  <tr class="fondo-boton">
                                    <td>#</td>
                                    <td>Cuenta</td>
                                    <td>Numero</td>
                                    <td>Cantidad</td>
                                    <td class="small">
                                    	<button class="btn btn-success btn-sm"onclick="seleccionarTodosChecks('lista_check')">TODOS</button>
                                    	<button class="btn btn-danger btn-sm"onclick="noSeleccionarTodosChecks('lista_check')">NINGUNO</button> 
                                    </td>
                                  </tr>
                              </thead>
                              <tbody>
                                
                                <?php 
                                $iii=1;
                               $detalle=obtenerCuentasComprobantesDet($globalCode);
							  	while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {
                                    $codigoX=$row['codigo'];
									$nombreX=trim($row['nombre']);
									$numeroX=trim($row['numero']);
									$cantidadX=$row['cantidad'];
									$banderaHab=0;

                                   ?>
                                   <tr>
                                     <td><?=$iii?></td>
                                     <td class="text-left"><?=$nombreX?></td>
                                     <td class="text-left"><?=$numeroX?></td>
                                     <td class="text-right font-weight-bold"><?=$cantidadX?></td>
                                     <td>
                                       <div class="togglebutton" title="Habilitar/Deshabilitar">
                                               <label>
                                                 <input type="checkbox" value="<?=$codigoX?>" name="lista_check" <?=($banderaHab==1)?"checked":"";?>>
                                                 <span class="toggle"></span>
                                               </label>
                                       </div>
                                     </td>
                                   </tr>
                                  <?php
                                  $iii++; 
                                  } ?>
                              </tbody>
                           </table>
                   </div>
				  	<div class="card-footer fixed-bottom">
						<button onclick="filtrarCuentaComprobanteDetalle()" class="<?=$buttonMorado;?>">Listar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>"> Volver </a>

				  	</div>

				</div>
			</div>	
		
	</div>
</div>


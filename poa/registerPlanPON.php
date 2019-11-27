<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];

$codigoIndicador=$codigo;
$nombreIndicador=nameIndicador($codigoIndicador);
$nombreObjetivo=nameObjetivoxIndicador($codigoIndicador);

$dbh = new Conexion();


$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$table="actividades_poa";
$moduleName="Planificacion PON";

//sacamos la periodicidad el 
$sqlDatosAdi="SELECT i.cod_periodo, p.nombre as periodo from indicadores i, tipos_resultado t, periodos p where i.cod_periodo=p.codigo and t.codigo=i.cod_tiporesultado and i.codigo=:codigoIndicador";
$stmt = $dbh->prepare($sqlDatosAdi);
$stmt->bindParam(':codigoIndicador',$codigoIndicador);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	$codPeriodo=$row['cod_periodo'];
	$nombrePeriodo=$row['periodo'];
}

$codigoIndicadorPON=obtenerCodigoPON();

//SACAMOS EL ESTADO DEL POA PARA LA GESTION
$codEstadoPOAGestion=estadoPOAGestion($globalGestion);

?>

<div class="content">
	<div class="container-fluid">

		  <form id="form1" class="form-horizontal" action="poa/savePlan.php" method="post">
			<input type="hidden" name="cod_indicador" id="cod_indicador" value="<?=$codigoIndicador;?>">

			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Registrar <?=$moduleName;?> </h4>
					  <h6 class="card-title">Objetivo: <?=$nombreObjetivo;?></h6>
					  <h6 class="card-title">Indicador: <?=$nombreIndicador;?></h6>
					  <h6 class="card-title">Periodicidad:<?=$nombrePeriodo;?></h6>
					</div>
				</div>
				<div class="card-body ">
					<div class="row">
					  <label class="col-sm-2 col-form-label">Gestion</label>
					  <div class="col-sm-7">
						<div class="form-group">
						  <input class="form-control" type="text" name="gestion" value="<?=$globalNombreGestion;?>" id="gestion" disabled="true" />
						</div>
					  </div>
					</div>

					<?php
					$sqlLista="SELECT a.codigo, a.orden, a.nombre, a.cod_tiporesultado, a.cod_unidadorganizacional, a.cod_area
					from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 ";
					if($globalUserPON!=2){
						$sqlLista.=" and a.cod_area in ($globalArea) and a.cod_unidadorganizacional in ($globalUnidad) and a.cod_personal='$globalUser' ";	
					} 
					if($codEstadoPOAGestion==3){
						$sqlLista.=" and a.actividad_extra=1 ";
					}					
					if($globalUserPON==1){
						$sqlLista.="UNION SELECT a.codigo, a.orden, a.nombre, a.cod_tiporesultado, a.cod_unidadorganizacional, a.cod_area from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 and a.cod_personal='$globalUser'";
						if($codEstadoPOAGestion==3){
							$sqlLista.=" and a.actividad_extra=1 ";
						}
					}
					$sqlLista.=" order by cod_unidadorganizacional, cod_area, orden";
					
					//echo $sqlLista;
					
					$stmtLista = $dbh->prepare($sqlLista);
					// Ejecutamos
					$stmtLista->execute();

					// bindColumn
					$stmtLista->bindColumn('codigo', $codigo);
					$stmtLista->bindColumn('orden', $orden);
					$stmtLista->bindColumn('nombre', $nombre);
					$stmtLista->bindColumn('cod_tiporesultado', $codTipoDato);
					$stmtLista->bindColumn('cod_unidadorganizacional', $codUnidad);
					$stmtLista->bindColumn('cod_area', $codArea);

					?>

              		<div class="table-responsive">
		                <table class="table table-condensed">
		                  <thead>
		                    <tr>
		                      <th class="text-center">#</th>
		                      <th class="text-center">Area</th>
		                      <th>Nombre</th>
		                      <th>Ene</th>
		                      <th>Feb</th>
		                      <th>Mar</th>
		                      <th>Abr</th>
		                      <th>May</th>
		                      <th>Jun</th>
		                      <th>Jul</th>
		                      <th>Ago</th>
		                      <th>Sep</th>
		                      <th>Oct</th>
		                      <th>Nov</th>
		                      <th>Dic</th>
		                    </tr>
		                  </thead>
		                  <tbody>
		                  <?php
		                    $index=1;
		                  	while ($row = $stmtLista->fetch(PDO::FETCH_BOUND)) {
	                  			$abrevArea=abrevArea($codArea);
                          		$abrevUnidad=abrevUnidad($codUnidad);
		                  ?>
		                    <tr>
		                      <td class="text-center"><?=$index;?></td>
		                      <td class="text-center"><h6><p class="text-danger"><?=$abrevUnidad;?>-<?=$abrevArea;?></p></h6></td>
		                      <td class="text-left text-primary small"><?=$nombre;?></td>
		                    <?php
	                    	for($i=1;$i<=12;$i++){
	                    		$sqlRecupera="SELECT value_numerico from actividades_poaplanificacion where cod_actividad=:cod_actividad and mes=:cod_mes";
	                    		$stmtRecupera = $dbh->prepare($sqlRecupera);
								$stmtRecupera->bindParam(':cod_actividad',$codigo);
								$stmtRecupera->bindParam(':cod_mes',$i);
								$stmtRecupera->execute();
								$valueNumero=0;
								$valueString="";
								$valueBooleano=0;
								while ($rowRec = $stmtRecupera->fetch(PDO::FETCH_ASSOC)) {
									$valueNumero=$rowRec['value_numerico'];
								}
							?>
	                    		<td>
		                    		<select class="form-control" name="plan|<?=$codigo;?>|<?=$i;?>">
								  	<option disabled selected value="">-</option>
								<?php
									$stmtOpe = $dbh->prepare("SELECT ep.codigo, ep.nombre, ep.abreviatura FROM estados_pon ep where ep.cod_tipoestadopon=1 ORDER BY 1");
									$stmtOpe->execute();
									while ($rowOpe = $stmtOpe->fetch(PDO::FETCH_ASSOC)) {
										$codigoO=$rowOpe['codigo'];
										$nombreO=$rowOpe['nombre'];
										$abreviaturaO=$rowOpe['abreviatura'];
								?>
									<option value="<?=$codigoO;?>" <?=($codigoO==$valueNumero)?"selected":""?> ><?=$nombreO;?></option>
								<?php
									}
								?>
									</select>
	                    		</td>
	                    	<?php	
	                    	}
		                    ?>
		                    <input type="hidden" name="tipo_dato|<?=$codigo;?>" id="tipo_dato|<?=$codigo;?>|<?=$i;?>" value="<?=$codTipoDato;?>">
		                    </tr>
					        <?php
    							$index++;
    						}
					        ?>
		                  </tbody>
		                </table>
		              </div>

		        </div>
	            
				  <div class="card-footer ml-auto mr-auto">
					<button type="submit" class="<?=$button;?>">Guardar</button>
					<a href="?opcion=listActividadesPOA&codigo=<?=$codigoIndicador;?>&codigoPON=<?=$codigoIndicadorPON?>&area=0&unidad=0" class="<?=$buttonCancel;?>">Cancelar</a>
				  </div>
			</div>
		  </form>
	</div>
</div>
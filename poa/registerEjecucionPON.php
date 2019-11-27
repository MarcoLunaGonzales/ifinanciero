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


$codigoIndicador=$codigo;
$nombreIndicador=nameIndicador($codigoIndicador);

$codigoIndicadorPON=obtenerCodigoPON();

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();

$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];
$globalUserPON=$_SESSION["globalUserPON"];

//SACAMOS LAS FECHAS DE REGISTRO DEL MES EN CURSO
$fechaActual=date("Y-m-d");
$sqlFechaEjecucion="SELECT f.mes, f.anio, DATE_FORMAT(f.fecha_fin, '%d/%m')fecha_fin from fechas_registroejecucion f 
where f.fecha_inicio<='$fechaActual' and f.fecha_fin>='$fechaActual'";
//echo $sqlFechaEjecucion;
$stmt = $dbh->prepare($sqlFechaEjecucion);
$stmt->execute();
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $codMesX=$row['mes'];
  $codAnioX=$row['anio'];
  $fechaFinRegistroX=$row['fecha_fin'];
}
$nombreMesX=nameMes($codMesX);
//FIN FECHAS

$table="actividades_poa";
$moduleName="Registro de Ejecucion PON";

?>

<div class="content">
	<div class="container-fluid">

		  <form id="form1" enctype="multipart/form-data" class="form-horizontal" action="poa/saveEjecucion.php" method="post">
			<input type="hidden" name="cod_indicador" id="cod_indicador" value="<?=$codigoIndicador;?>">
			
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title"><?=$moduleName;?> - Mes Ejecucion: <?=$codMesX;?> Fecha Limite: <?=$fechaFinRegistroX;?></h4>
					  <h6 class="card-title">Indicador: <?=$nombreIndicador;?></h6>
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
					$sqlLista="SELECT a.codigo, a.orden, a.nombre, (SELECT s.abreviatura from comites c, sectores s where c.cod_sector=s.codigo and c.codigo=a.cod_comite)as sector, (SELECT c.nombre from comites c where c.codigo=a.cod_comite)as comite,
						(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma, a.cod_unidadorganizacional, a.cod_area, (SELECT ep.nombre from estados_pon ep where ep.codigo=a.cod_estadopon)as estadopon, (select mg.nombre from modos_generacionpon mg where mg.codigo=a.cod_modogeneracionpon)as modogeneracionpon, (SELECT p.nombre from personal p where p.codigo=a.cod_personal) as personal
					 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 ";
					if($globalAdmin==0){
						$sqlLista.=" and a.cod_area='$globalArea' and a.cod_unidadorganizacional='$globalUnidad' and a.cod_personal='$globalUser'";
					}
					if($globalUserPON==1){
						$sqlLista.=" union ";
						$sqlLista.=" SELECT a.codigo, a.orden, a.nombre, (SELECT s.abreviatura from comites c, sectores s where c.cod_sector=s.codigo and c.codigo=a.cod_comite)as sector, (SELECT c.nombre from comites c where c.codigo=a.cod_comite)as comite,
						(SELECT n.abreviatura from normas n where n.codigo=a.cod_norma)as norma, a.cod_unidadorganizacional, a.cod_area, (SELECT ep.nombre from estados_pon ep where ep.codigo=a.cod_estadopon)as estadopon, (select mg.nombre from modos_generacionpon mg where mg.codigo=a.cod_modogeneracionpon)as modogeneracionpon, (SELECT p.nombre from personal p where p.codigo=a.cod_personal) as personal
					 		from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 and a.cod_personal='$globalUser' ";	
					}
					$sqlLista.=" order by cod_unidadorganizacional, cod_area, orden";
					$stmtLista = $dbh->prepare($sqlLista);
					// Ejecutamos
					$stmtLista->execute();

					// bindColumn
					$stmtLista->bindColumn('codigo', $codigo);
					$stmtLista->bindColumn('orden', $orden);
					$stmtLista->bindColumn('nombre', $nombre);
					$stmtLista->bindColumn('cod_unidadorganizacional', $codUnidad);
					$stmtLista->bindColumn('cod_area', $codArea);

					?>

              		<div class="table-responsive">
		                <table class="table table-bordered">
		                  <thead>
		                    <tr>
		                      <th class="text-center">#</th>
		                      <th>Area</th>
		                      <th>Tema a normalizar</th>
		                      <th class="text-center table-warning">Plan</th>
		                      <th class="text-center table-success">Ej.PON</th>
		                      <th class="text-center">Descripcion<br>Logro</th>
		                      <th class="text-center">Archivo<br>Soporte</th>
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
		                      <td class="text-center"><?=$orden;?></td>
		                      <td class="text-center"><?=$abrevArea."-".$abrevUnidad;?></td>
		                      <td class="text-left small"><?=$nombre;?></td>
		                    <?php
	                    	for($i=$codMesX;$i<=$codMesX;$i++){
	                    		//SACAMOS LA PLANIFICACION
	                          $sqlRecupera="SELECT ep.nombre from actividades_poaplanificacion a, estados_pon ep where a.value_numerico=ep.codigo and a.cod_actividad='$codigo' and a.mes='$codMesX'";
	                          $stmtRecupera = $dbh->prepare($sqlRecupera);
	                          $stmtRecupera->execute();
	                          $estadoPon="";
	                          while ($rowRec = $stmtRecupera->fetch(PDO::FETCH_ASSOC)) {
	                            $estadoPon=$rowRec['nombre'];
	                          }

	                          $sqlRecupera="SELECT ep.codigo, a.descripcion from actividades_poaejecucion a, estados_pon ep where a.value_numerico=ep.codigo and a.cod_actividad='$codigo' and a.mes='$codMesX'";
	                          $stmtRecupera = $dbh->prepare($sqlRecupera);
	                          $stmtRecupera->execute();
	                          $estadoPonEj="";
	                          while ($rowRec = $stmtRecupera->fetch(PDO::FETCH_ASSOC)) {
	                            $estadoPonEj=$rowRec['codigo'];
	                            $descripcionEj=$rowRec['descripcion'];
	                          }
	                    	?>
	                    		<td class="text-center table-warning font-weight-bold">
	                    			<?=$estadoPon;?>
	                    		</td>
	                    		<td class="text-center table-success">
		                    		<select class="form-control" name="plan|<?=$codigo;?>|<?=$i;?>" data-width="250px" >
									<?php
										$stmtOpe = $dbh->prepare("SELECT ep.codigo, ep.nombre, ep.abreviatura, ep.cod_tipoestadopon FROM estados_pon ep ORDER BY 4,1");
										$stmtOpe->execute();
										while ($rowOpe = $stmtOpe->fetch(PDO::FETCH_ASSOC)) {
											$codigoO=$rowOpe['codigo'];
											$nombreO=$rowOpe['nombre'];
											$abreviaturaO=$rowOpe['abreviatura'];
									?>
									<option value="<?=$codigoO;?>" <?=($codigoO==$estadoPonEj)?"selected":"";?> ><?=$nombreO;?></option>
									<?php
										}
									?>
									</select> 
	                    		</td>
	                    		<td class="text-center">
	                    			<input class="form-control input-sm" type="text" name="explicacion|<?=$codigo;?>|<?=$i;?>" value="<?=$descripcionEj;?>"> 
	                    		</td>
	                    		<td class="text-center">
	                    			<input class="form-control-file" type="file" name="file|<?=$codigo;?>|<?=$i;?>">
	                    		</td>
	                    	<?php
	                    	}
		                    ?>
		                    	<input type="hidden" name="tipo_dato|<?=$codigo;?>" id="tipo_dato|<?=$codigo;?>|<?=$i;?>" value="1">
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
					<a href="?opcion=listActividadesPOAEjecucion&codigo=<?=$codigoIndicador;?>&codigoPON=<?=$codigoIndicadorPON?>&unidad=0&area=0" class="<?=$buttonCancel;?>">Cancelar</a>
				  </div>
			</div>
		  </form>
	</div>
</div>
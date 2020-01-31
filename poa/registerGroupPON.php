<?php

require_once 'conexion.php';
require_once 'styles.php';
require_once 'functions.php';

$dbh = new Conexion();

$sqlX="SET NAMES 'utf8'";
$stmtX = $dbh->prepare($sqlX);
$stmtX->execute();


$globalNombreGestion=$_SESSION["globalNombreGestion"];
$globalUser=$_SESSION["globalUser"];
$globalGestion=$_SESSION["globalGestion"];
$globalUnidad=$_SESSION["globalUnidad"];
$globalArea=$_SESSION["globalArea"];

$globalAdmin=$_SESSION["globalAdmin"];
$globalUserPON=$_SESSION["globalUserPON"];

$codigoIndicador=$codigo;
$areaUnidad=$areaUnidad;

$nombreAreaX="-";
$nombreUnidadX="-";
$codUnidadX=0;
$codAreaX=0;
if($areaUnidad!=0){
	list($codUnidadX,$codAreaX)=explode("|", $areaUnidad);
	$nombreAreaX=abrevArea($codAreaX);
	$nombreUnidadX=abrevUnidad($codUnidadX);
}

$codUnidadHijosX=buscarHijosUO($codUnidadX);

$nombreIndicador=nameIndicador($codigoIndicador);
$nombreObjetivo=nameObjetivoxIndicador($codigoIndicador);

//SACAMOS EL ESTADO DEL POA PARA LA GESTION
$codEstadoPOAGestion=estadoPOAGestion($globalGestion);

$table="actividades_poa";
$moduleName="Actividades PON";

$sqlCount="";
if($globalAdmin==1){
	$sqlCount="SELECT count(*)as nro_registros FROM actividades_poa where cod_indicador='$codigoIndicador' and cod_estado=1";	
}else{
	//$sqlCount="SELECT count(*)as nro_registros FROM actividades_poa where cod_indicador='$codigoIndicador' and cod_area='$globalArea' and cod_unidadorganizacional='$globalUnidad' and cod_estado=1";	
	$sqlCount="SELECT count(*)as nro_registros FROM actividades_poa where cod_indicador='$codigoIndicador' and cod_personal='$globalUser' and cod_estado=1";	
}
$stmtX = $dbh->prepare($sqlCount);
$stmtX->execute();
while ($row = $stmtX->fetch(PDO::FETCH_ASSOC)) {
	$contadorRegistros=$row['nro_registros'];
}

?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
	console.log("filas e items: "+numFilas+" "+cantidadItems);
</script>

<div class="content">
	<div class="container-fluid">

		<form id="form1" class="form-horizontal" action="poa/saveGroupPON.php" method="post">
			<input type="hidden" name="cod_indicador" id="cod_indicador" value="<?=$codigoIndicador?>">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="codigoUnidad" id="codigoUnidad" value="<?=$codUnidadX;?>">
			<input type="hidden" name="codigoArea" id="codigoArea" value="<?=$codAreaX;?>">


			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Registrar <?=$moduleName;?></h4>
					  <h6 class="card-title">Objetivo: <?=$nombreObjetivo;?></h6>
					  <h6 class="card-title">Indicador: <?=$nombreIndicador;?></h6>
					</div>
				</div>
				<div class="card-body ">
					<div class="row">
					  <label class="col-sm-2 col-form-label">Gestion</label>
					  <div class="col-sm-2">
						<div class="form-group">
						  <input class="form-control" type="text" name="gestion" value="<?=$globalNombreGestion;?>" id="gestion" disabled="true" />
						</div>
					  </div>
					  <label class="col-sm-2 col-form-label">Area</label>
					  <div class="col-sm-2">
						<div class="form-group">
						  <input class="form-control" type="text" name="gestion" value="<?=$nombreAreaX;?> - <?=$nombreUnidadX;?>" id="gestion" disabled="true" />
						</div>
					  </div>
					</div>

					<?php
					$sqlLista="SELECT a.codigo, a.orden, a.nombre, a.cod_comite as comite, a.cod_norma, a.cod_estadopon as estadopon, a.cod_modogeneracionpon as modogeneracionpon, a.producto_esperado, a.cod_tiposeguimiento, a.cod_tiporesultado, a.cod_unidadorganizacional, a.cod_area, a.cod_personal, a.cod_tiposolicitante, a.solicitante
					 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 and a.cod_unidadorganizacional in ('$codUnidadX') and a.cod_area in ('$codAreaX') "; 
					if($globalUserPON!=2){
						$sqlLista.=" and a.cod_personal='$globalUser' ";	
					} 
					if($codEstadoPOAGestion==3){
						$sqlLista.=" and a.actividad_extra=1 ";
					}
					if($globalUserPON==1){
						$sqlLista.=" union SELECT a.codigo, a.orden, a.nombre, a.cod_comite as comite, a.cod_norma, a.cod_estadopon as estadopon, a.cod_modogeneracionpon as modogeneracionpon, a.producto_esperado, a.cod_tiposeguimiento, a.cod_tiporesultado, a.cod_unidadorganizacional, a.cod_area, a.cod_personal, a.cod_tiposolicitante, a.solicitante
					 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1  and a.cod_personal='$globalUser' ";
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
					$stmtLista->bindColumn('comite', $comite);
					$stmtLista->bindColumn('cod_norma', $norma);
					$stmtLista->bindColumn('estadopon', $estadopon);
					$stmtLista->bindColumn('modogeneracionpon', $modogeneracionpon);
					$stmtLista->bindColumn('producto_esperado', $producto_esperado);
					$stmtLista->bindColumn('cod_tiposeguimiento', $codTipoSeguimiento);
					$stmtLista->bindColumn('cod_tiporesultado', $codTipoResultado);
					$stmtLista->bindColumn('cod_unidadorganizacional', $codUnidad);
					$stmtLista->bindColumn('cod_area', $codArea);
					$stmtLista->bindColumn('cod_personal', $codPersonal);
					$stmtLista->bindColumn('cod_tiposolicitante', $codTipoSolicitante);
					$stmtLista->bindColumn('solicitante', $solicitante);

					?>
					<fieldset id="fiel" style="width:100%;border:0;">
						<button type="button" name="add" value="add" class="btn btn-danger btn-round btn-fab" onClick="addActividadPON(this,<?=$codigoIndicador;?>,<?=$codUnidadX;?>)" accesskey="a">
		                              <i class="material-icons">add</i>
		                </button>						
					        	<?php
			                        $index=1;
			                      	while ($rowLista = $stmtLista->fetch(PDO::FETCH_BOUND)) {
              							//echo $codUnidad." ----- ".$codArea." ".$norma;

			                    ?>

						<div id="div<?=$index;?>">	
	                    
		                    <div class="col-md-12">
								<div class="row">
									<div class="col-sm-3">
				                        <div class="form-group">
            	            			<input type="hidden" name="codigo<?=$index;?>" id="codigo<?=$index;?>" value="<?=$codigo;?>">
				                        <select class="selectpicker form-control" name="comite<?=$index;?>" id="comite<?=$index;?>" data-style="<?=$comboColor;?>" data-live-search="true">
									  		<option value="">Comite</option>
										  	<?php
										  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
											$stmt->execute();
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												$codigoX=$row['codigo'];
												$nombreX=$row['nombre'];
											?>
											<optgroup label="<?=$nombreX;?>">
											<?php
											  	$stmtY = $dbh->prepare("SELECT c.codigo, c.nombre, c.abreviatura FROM comites c where c.cod_sector='$codigoX' and cod_estado=1 order by 2");
												$stmtY->execute();
												while ($rowY = $stmtY->fetch(PDO::FETCH_ASSOC)) {
													$codigoY=$rowY['codigo'];
													$nombreY=$rowY['nombre'];
													$nombreY=cutString($nombreY,80);
													$abreviaturaY=$rowY['abreviatura'];

											?>
													<option value="<?=$codigoY;?>" data-subtext="<?=$abreviaturaY?>" <?=($codigoY==$comite)?"selected":"";?>  ><?=$nombreY;?></option>	
											<?php
												}
											?>
											</optgroup>
											<?php	
											}
										  	?>
										</select>
										</div>
			                        </div>

			                        <div class="col-sm-3">
			                        	<div class="form-group">
								        <select class="selectpicker form-control" name="norma<?=$index;?>" id="norma<?=$index;?>" data-style="<?=$comboColor;?>" data-live-search="true">
										  	<option value="">Norma Referencia</option>
										  	<?php
										  	$stmt = $dbh->prepare("SELECT codigo, nombre FROM sectores where cod_estado=1 order by 2");
											$stmt->execute();
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												$codigoX=$row['codigo'];
												$nombreX=$row['nombre'];
											?>
											<optgroup label="<?=$nombreX;?>">
											<?php
											  	$stmtY = $dbh->prepare("SELECT n.codigo, n.nombre, n.abreviatura FROM normas n where n.cod_sector='$codigoX' and n.cod_estado=1 order by 2");
												$stmtY->execute();
												while ($rowY = $stmtY->fetch(PDO::FETCH_ASSOC)) {
													$codigoY=$rowY['codigo'];
													$nombreY=$rowY['nombre'];
													$nombreY=cutString($nombreY,80);
													$abreviaturaY=$rowY['abreviatura'];

											?>
													<option value="<?=$codigoY;?>" data-subtext="<?=$nombreY?>" <?=($codigoY==$norma)?"selected":"";?>  ><?=$abreviaturaY;?></option>	
											<?php
												}
											?>
											</optgroup>
											<?php	
											}
										  	?>
										</select>
										</div>
		                          	</div>

			                        <div class="col-sm-2">
			                        	<div class="form-group">
								        <select class="selectpicker form-control" name="modogeneracion<?=$index;?>" id="modogeneracion<?=$index;?>" data-style="<?=$comboColor;?>" data-live-search="true">
										  	<option value="">Modo Generacion</option>
										  	<?php
										  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM modos_generacionpon where cod_estado=1 order by 2");
											$stmt->execute();
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												$codigoX=$row['codigo'];
												$nombreX=$row['nombre'];
												$abreviaturaX=$row['abreviatura'];
											?>
												<option value="<?=$codigoX;?>" data-subtext="<?=$abreviaturaX?>" <?=($codigoX==$modogeneracionpon)?"selected":"";?>  ><?=$nombreX;?></option>	
											<?php
												}
											?>
										</select>
										</div>
		                          	</div>

		                          	<div class="col-sm-4">
					                    <div class="form-group">
					                    <label for="actividad<?=$index;?>" class="bmd-label-floating">Tema a Normalizar</label>			
			                          	<textarea class="form-control" name="actividad<?=$index;?>" id="actividad<?=$index;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();"><?=$nombre;?></textarea>	
		 								</div>
		                          	</div>


	                      		</div>
							</div>

							<div class="col-md-12">
								<div class="row">

			                        <div class="col-sm-3">
			                        	<div class="form-group">
								        <select class="selectpicker form-control" name="tipo_solicitante<?=$index;?>" id="tipo_solicitante<?=$index;?>" data-style="<?=$comboColor;?>" required="true">
										  	<option value="">Sector Solicitante</option>
										  	<?php
										  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_solicitante where cod_estado=1 order by 3");
											$stmt->execute();
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												$codigoX=$row['codigo'];
												$nombreX=$row['nombre'];
												$abreviaturaX=$row['abreviatura'];
											?>
												<option value="<?=$codigoX;?>" data-subtext="<?=$abreviaturaX?>" <?=($codigoX==$codTipoSolicitante)?"selected":"";?>  ><?=$nombreX;?></option>	
											<?php
												}
											?>
										</select>
										</div>
		                          	</div>

		                          	<div class="col-sm-4">
					                    <div class="form-group">
					                    <label for="solicitante<?=$index;?>" class="bmd-label-floating">Solicitante</label>			
			                          	<input type="text" class="form-control" name="solicitante<?=$index;?>" id="solicitante<?=$index;?>" value="<?=$solicitante;?>" required="true" onkeyup="javascript:this.value=this.value.toUpperCase();">	
		 								</div>
		                          	</div>

			                        <div class="col-sm-4">
			                        	<div class="form-group">
								        <select class="selectpicker" name="personal<?=$index;?>" id="personal<?=$index;?>" data-style="<?=$comboColor;?>">
										  	<option value="">Responsable</option>
										  	<?php
										  	$stmt = $dbh->prepare("SELECT distinct(p.codigo)as codigo,CONCAT_WS(' ',p.paterno,p.materno,p.primer_nombre)as nombre FROM personal p, personal_unidadesorganizacionales pu, personal_datosadicionales pd where p.codigo=pd.cod_personal and pd.cod_estado=1 order by 2");
											$stmt->execute();
											while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
												$codigoX=$row['codigo'];
												$nombreX=$row['nombre'];
											?>
												<option value="<?=$codigoX;?>" <?=($codigoX==$codPersonal)?"selected":"";?>  ><?=$nombreX;?></option>	
											<?php
												}
											?>
										</select>
										</div>
		                          	</div>

									<div class="col-sm-1">
										<button rel="tooltip" class="btn btn-just-icon btn-danger btn-link" onclick="minusActividad('<?=$index;?>');">
						                              <i class="material-icons">remove_circle</i>
						                </button>
					            	</div>
				            	</div>
			            	
			            	</div>
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>

					            <?php
        							$index++;
        						}
        						?>
		            </fieldset>
		    
	            
				  	<div class="card-body">
						<button type="submit" class="<?=$button;?>">Guardar</button>
						<a href="#" class="btn" data-toggle="modal" data-target="#myModal">
                        	Cambiar Area
	                    </a>
						<a href="?opcion=listPOA" class="<?=$buttonCancel;?>"> <-- Volver </a>

				  	</div>

				</div>
			</div>	
		</form>
	</div>
</div>

<!-- Classic Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Seleccionar Area para registrar actividades</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="text-align:center;">
	  	<select class="selectpicker" name="areaModal" id="areaModal" data-style="<?=$comboColor;?>" required>
		  	<option disabled selected value="">Area</option>
		  	<?php
		  	$sqlAreas="SELECT i.cod_indicador, u.codigo as codigoUnidad, u.nombre as nombreUnidad, u.abreviatura as abrevUnidad, a.codigo as codigoArea, a.nombre as nombreArea, a.abreviatura as abrevArea from indicadores_unidadesareas i, unidades_organizacionales u, areas a where i.cod_indicador='$codigoIndicador' and i.cod_area=a.codigo and i.cod_unidadorganizacional=u.codigo";
		  	if($globalAdmin==0 && $globalUserPON==0){
		  		$sqlAreas.=" and i.cod_unidadorganizacional in ($globalUnidad) and i.cod_area in ($globalArea) ";
		  	}
		  	$sqlAreas.=" order by 3,6";
		  	//echo $sqlAreas;
		  	$stmt = $dbh->prepare($sqlAreas);
			$stmt->execute();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoU=$row['codigoUnidad'];
				$nombreU=$row['nombreUnidad'];
				$abrevU=$row['abrevUnidad'];
				$codigoA=$row['codigoArea'];
				$nombreA=$row['nombreArea'];
				$abrevA=$row['abrevArea'];

			?>
			<option value="<?=$codigoU;?>|<?=$codigoA;?>" data-subtext="<?=$nombreU;?>-<?=$nombreA?>"><?=$abrevU;?> - <?=$abrevA;?></option>
			<?php	
			}
		  	?>
	  	</select>	
      </div>
      <div class="modal-footer">
        <button type="button" class="<?=$button;?>" onclick="enviarAreaPOAPON(<?=$codigoIndicador;?>);">Aceptar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--  End Modal -->

<?php
if($areaUnidad==0){
?>
<script>
	verificaModalArea();
</script>
<?php
}
?>

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


$table="actividades_poa";
$moduleName="Asignar Actividades a Personal";

$sqlCount="";
if($globalAdmin==1){
	$sqlCount="SELECT count(*)as nro_registros FROM actividades_poa where cod_indicador='$codigoIndicador' and cod_estado=1";	
}else{
	$sqlCount="SELECT count(*)as nro_registros FROM actividades_poa where cod_indicador='$codigoIndicador' and cod_area='$globalArea' and cod_unidadorganizacional='$globalUnidad' and cod_estado=1";	
}
$stmtX = $dbh->prepare($sqlCount);
$stmtX->execute();
while ($row = $stmtX->fetch(PDO::FETCH_ASSOC)) {
	$contadorRegistros=$row['nro_registros'];
}

//SACAMOS LA TABLA RELACIONADA
$sqlClasificador="SELECT c.tabla FROM indicadores i, clasificadores c where i.codigo='$codigoIndicador' and i.cod_clasificador=c.codigo";
$stmtClasificador = $dbh->prepare($sqlClasificador);
$stmtClasificador->execute();
$nombreTablaClasificador="";
while ($rowClasificador = $stmtClasificador->fetch(PDO::FETCH_ASSOC)) {
	$nombreTablaClasificador=$rowClasificador['tabla'];
}

?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;

	//verificaModalArea();
</script>

<div class="content">
	<div class="container-fluid">

		<form id="form1" class="form-horizontal" action="poa/saveAsignarPOAI.php" method="post">
			<input type="hidden" name="cod_indicador" id="cod_indicador" value="<?=$codigoIndicador?>">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="codigoUnidad" id="codigoUnidad" value="<?=$codUnidadX;?>">
			<input type="hidden" name="codigoArea" id="codigoArea" value="<?=$codAreaX;?>">


			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title"><?=$moduleName;?></h4>
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
					$sqlLista="SELECT a.codigo, a.orden, a.nombre, a.cod_normapriorizada,
					(SELECT s.codigo from normas n, sectores s where n.cod_sector=s.codigo and n.codigo=a.cod_normapriorizada)as sectorpriorizado, a.cod_norma,
					(SELECT s.codigo from normas n, sectores s where n.cod_sector=s.codigo and n.codigo=a.cod_norma)as sector, a.producto_esperado, a.cod_tiposeguimiento, a.cod_tiporesultado, a.cod_unidadorganizacional, a.cod_area, a.cod_datoclasificador, a.cod_personal
					 from actividades_poa a where a.cod_indicador='$codigoIndicador' and a.cod_estado=1 and a.cod_unidadorganizacional='$codUnidadX' and a.cod_area='$codAreaX' ";

					 $sqlLista.=" order by a.cod_unidadorganizacional, a.cod_area, a.orden";
					//echo $sql;
					$stmtLista = $dbh->prepare($sqlLista);
					// Ejecutamos
					$stmtLista->execute();

					// bindColumn
					$stmtLista->bindColumn('codigo', $codigo);
					$stmtLista->bindColumn('orden', $orden);
					$stmtLista->bindColumn('nombre', $nombre);
					$stmtLista->bindColumn('cod_normapriorizada', $normaPriorizada);
					$stmtLista->bindColumn('sectorpriorizado', $sectorPriorizado);
					$stmtLista->bindColumn('cod_norma', $norma);
					$stmtLista->bindColumn('sector', $sector);
					$stmtLista->bindColumn('producto_esperado', $productoEsperado);
					$stmtLista->bindColumn('cod_tiposeguimiento', $codTipoSeguimiento);
					$stmtLista->bindColumn('cod_tiporesultado', $codTipoResultado);
					$stmtLista->bindColumn('cod_unidadorganizacional', $codUnidad);
					$stmtLista->bindColumn('cod_area', $codArea);
					$stmtLista->bindColumn('cod_datoclasificador',$codDatoClasificador);
					$stmtLista->bindColumn('cod_personal',$codPersonal);
					?>
					<fieldset id="fiel" style="width:100%;border:0;">
				    	<?php
	                        $index=1;
	                      	while ($rowLista = $stmtLista->fetch(PDO::FETCH_BOUND)) {
      							//echo $codUnidad." ----- ".$codArea." ".$norma;

	                    ?>
					<div id="div<?=$index;?>">	           
						<div class="col-md-12">
							<div class="row">
								<div class="col-sm-8">
				                    <div class="form-group">
				                    <label for="actividad<?=$index;?>" class="bmd-label-floating">Actividad</label>			
		                          	<textarea class="form-control" type="text" name="actividad<?=$index;?>" id="actividad<?=$index;?>" readonly><?=$nombre;?>
		                          	</textarea>	
	 								</div>
	                          	</div>
		                          	
								<div class="col-sm-3">
			                        <div class="form-group">
		                        	<input type="hidden" name="codigo<?=$index;?>" id="codigo<?=$index;?>" value="<?=$codigo;?>">
			                        <select class="selectpicker" name="personal<?=$index;?>" id="personal<?=$index;?>" data-style="<?=$comboColor;?>" data-live-search="true">
			                        	<?php
									  	$sql="SELECT codigo, nombre FROM personal2 p, personal_unidadesorganizacionales pu, personal_datosadicionales pd where p.codigo=pd.cod_personal and pd.cod_estado=1 and p.codigo=pu.cod_personal and pu.cod_unidad='$codUnidad' order by 2";
										  	?>
								  		<option value="">Personal</option>
									  	<?php
									  	$stmt = $dbh->prepare($sql);
										$stmt->execute();
										while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
											$codigoX=$row['codigo'];
											$nombreX=$row['nombre'];
										?>
											<option value="<?=$codigoX;?>"><?=($codigoX==$codPersonal)?"selected":"";?><?=$nombreX;?></option>	
										<?php	
										}
									  	?>
									</select>
									</div>
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
						<a href="?opcion=listPOA" class="<?=$buttonCancel;?>">Cancelar</a>

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
        <h5 class="modal-title" id="exampleModalLabel">Seleccionar Area para asignar POAI</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" style="text-align:center;">
	  	<select class="selectpicker" name="areaModal" id="areaModal" data-style="<?=$comboColor;?>" required>
		  	<option disabled selected value="">Area</option>
		  	<?php
		  	$sqlAreas="SELECT i.cod_indicador, u.codigo as codigoUnidad, u.nombre as nombreUnidad, u.abreviatura as abrevUnidad, a.codigo as codigoArea, a.nombre as nombreArea, a.abreviatura as abrevArea from indicadores_unidadesareas i, unidades_organizacionales u, areas a where i.cod_indicador='$codigoIndicador' and i.cod_area=a.codigo and i.cod_unidadorganizacional=u.codigo";
		  	if($globalAdmin==0){
		  		$sqlAreas.=" and i.cod_unidadorganizacional='$globalUnidad' and i.cod_area='$globalArea' ";
		  	}
		  	$sqlAreas.=" order by 3,6";
		  	echo $sqlAreas;
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
        <button type="button" class="<?=$button;?>" onclick="enviarAsignarPOA(<?=$codigoIndicador;?>);">Aceptar</button>
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

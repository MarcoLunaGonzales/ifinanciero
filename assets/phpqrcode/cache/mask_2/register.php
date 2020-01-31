<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
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

$contadorRegistros=0;
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
	var distribucionPor=[];
	var configuracionCentro=[];
	var configuraciones=[];
</script>
<?php
            //configuraciones
			$stmt = $dbh->prepare("SELECT id_configuracion, valor_configuracion, descripcion_configuracion FROM configuraciones");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoX=$row['id_configuracion'];
				$valorX=$row['valor_configuracion'];
				$descripcionX=$row['descripcion_configuracion'];
			 ?>
			 <script>configuraciones.push({codigo:<?=$codigoX?>,valor:<?=$valorX?>,descripcion:'<?=$descripcionX?>'});</script>
		    <?php
			 }
		    ?>

		  	<?php
			$stmt = $dbh->prepare("SELECT codigo, cod_unidadorganizacional, porcentaje FROM distribucion_gastosporcentaje");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoX=$row['codigo'];
				$unidadX=$row['cod_unidadorganizacional'];
				$porcentajeX=$row['porcentaje'];
			 ?>
			 <script>distribucionPor.push({codigo:<?=$codigoX?>,cod_unidad:<?=$unidadX?>,porcent:<?=$porcentajeX?>});</script>
		    <?php
			 }
		    ?>
		    <?php
			$stmt = $dbh->prepare("SELECT cod_unidadorganizacional,cod_grupocuentas,fijo,cod_area FROM configuracion_centrocostoscomprobantes");
			$stmt->execute();
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codUnidadX=$row['cod_unidadorganizacional'];
				$codGrupoX=$row['cod_grupocuentas'];
				$fijoX=$row['fijo'];
				$codAreaX=$row['cod_area'];
			 ?>
			 <script>configuracionCentro.push({cod_unidad:<?=$codUnidadX?>,cod_grupo:<?=$codGrupoX?>,fijo:<?=$fijoX?>,cod_area:<?=$codAreaX?>});</script>
		    <?php
			 }
		    ?>
<?php
$fechaActual=date("Y-m-d");
$dbh = new Conexion();

// Preparamos
$stmt = $dbh->prepare("SELECT codigo,nombre,abreviatura,cod_estadoreferencial from monedas");
// Ejecutamos
$stmt->execute();
// bindColumn
$stmt->bindColumn('cod_estadoreferencial', $codEstadoRef);
$stmt->bindColumn('codigo', $codigoMon);
$stmt->bindColumn('abreviatura', $abreviaturaMon);
$stmt->bindColumn('nombre', $nombreMon);
?>
<form id="formRegComp" class="form-horizontal" action="save.php" method="post" enctype="multipart/form-data">
<div class="content">
	<div class="container-fluid">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">

			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
<?php
$contMonedas=0;
while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {
    if($codigoMon!=1){
      $valorTipo=obtenerValorTipoCambio($codigoMon,$fechaActual);
      if($valorTipo==0){
      	$contMonedas++;
       }
     }
 }

 if($contMonedas!=0){
 	?>
     <p>No hay registros del tipo de cambio para hoy <?=strftime('%d de %B del %Y',strtotime($fechaActual))?></p>
     <a href="../index.php?opcion=tipoDeCambio" class="btn btn-warning">registrar</a>
 	<?php
 }else{
 	 ?>
    <div class="row">
					
						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Gestion</label>
					  			<input class="form-control" type="text" name="gestion" value="<?=$globalNombreGestion;?>" id="gestion" readonly="true" />
							</div>
						</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad_organizacional" value="<?=$globalNombreUnidad;?>" id="unidad_organizacional" readonly="true" />
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Fecha</label>
						  		<input class="form-control" type="date" name="fecha" value="<?=$fechaActual;?>" id="fecha" readonly="true"/>
							</div>
						</div>

						<div class="col-sm-2">
				        	<div class="form-group">
						        <select class="selectpicker form-control form-control-sm" name="tipo_comprobante" id="tipo_comprobante" data-style="<?=$comboColor;?>" onChange="ajaxCorrelativo(this);">
								  	<option disabled selected value="">Tipo</option>
							  	<?php
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 order by 1");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreX=$row['nombre'];
									$abrevX=$row['abreviatura'];
								?>
								<option value="<?=$codigoX;?>"><?=$nombreX;?></option>	
								<?php
							  	}
							  	?>
							</select>
							</div>
				      	</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label for="nro_correlativo" class="bmd-label-static">#</label>
						  		<div id="divnro_correlativo"><input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" readonly="true" /></div>
							</div>
						</div>
						
					    <div class="col-sm-3">
						    <div class="form-group">
				          		<label for="glosa" class="bmd-label-static">Glosa</label>
								<textarea class="form-control" name="glosa" id="glosa" required="true" rows="1" value=""></textarea>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="btn-group">
                              <a title="Copiar Glosa (shift+g)" href="#modalCopy" data-toggle="modal" data-target="#modalCopy" class="<?=$buttonCeleste?> btn-fab btn-sm">
                      		        <i class="material-icons"><?=$iconCopy?></i>
		                        </a>
                               <a title="Subir Archivos Respaldo (shift+r)" href="#modalFile" data-toggle="modal" data-target="#modalFile" class="btn btn-default btn-fab btn-sm">
                      		        <i class="material-icons"><?=$iconFile?></i><span id="narch" class="bg-warning"></span>
		                        </a>
		                        <a title="Cargar Plantilla (shift+p)"  href="#" onclick="cargarPlantillas()" class="btn btn-warning btn-fab btn-sm">
                      		        <i class="material-icons">post_add</i>
		                        </a>
		                        <a  title="Guardar como Plantilla (shift+s)" href="#" onclick="modalPlantilla()"class="btn btn-danger btn-fab btn-sm">
                      		        <i class="material-icons">favorite</i>
		                        </a>
                            </div>
						</div>
					</div>

				</div>
			</div>	

			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h6 class="card-title">Detalle</h6>
					</div>
				</div>
				<div class="card-body ">

					<?php
					//$sqlDetalle="";
					//$stmtLista = $dbh->prepare($sqlDetalle);
					//$stmtLista->execute();

					/*$stmtLista->bindColumn('cod_tiposeguimiento', $codTipoSeguimiento);
					$stmtLista->bindColumn('cod_tiporesultado', $codTipoResultado);
					$stmtLista->bindColumn('cod_unidadorganizacional', $codUnidad);
					$stmtLista->bindColumn('cod_area', $codArea);
					$stmtLista->bindColumn('cod_datoclasificador',$codDatoClasificador);
					$stmtLista->bindColumn('clave_indicador',$claveIndicador);
					$stmtLista->bindColumn('observaciones',$observaciones);
					$stmtLista->bindColumn('cod_hito',$codHito);
					*/
					?>
					<fieldset id="fiel" style="width:100%;border:0;">
							<button title="Agregar (alt+a)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addCuentaContable(this)">
                      		  <i class="material-icons">add</i>
		                    </button>

		                <div class="col-sm-1 float-right">
							<a title="Copiar Unidad - Area (shift+u)" href="#modalCopySel" data-toggle="modal" data-target="#modalCopySel" class="<?=$buttonDelete?> btn-fab">
                      		  <i class="material-icons"><?=$iconCopy?></i>
		                    </a>
		                </div>  
		              						
			        	<?php
    	                //$index=1;
                      	//while ($rowLista = $stmtLista->fetch(PDO::FETCH_BOUND)) {
	                    ?>
						<div id="div<?=$index;?>">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
			            <?php
						//	$index++;
						//}
						?>
		            </fieldset>
							
							<div class="row">
								<div class="col-sm-5">
						      	</div>
								<div class="col-sm-2">
						            <div class="form-group">	
						          		<input class="form-control" type="number" name="totaldeb" placeholder="0" id="totaldeb" readonly="true">	
									</div>
						      	</div>
								<div class="col-sm-2">
						            <div class="form-group">
						            	<input class="form-control" type="number" name="totalhab" placeholder="0" id="totalhab" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-3">
								</div>
							</div>

				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>"> <-- Volver </a>

				  	</div>
 	 <?php
 }
?>

					

				</div>
			</div>	
	</div>
</div>
<!-- small modal -->
<div class="modal fade modal-primary" id="modalFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
      	<i class="material-icons" data-notify="icon"><?=$iconFile?></i>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="material-icons">clear</i></button>
      </div>
      <div class="modal-body">
        <p>Cargar archivos de respaldo.</p> 
           <div class="fileinput fileinput-new col-md-12" data-provides="fileinput">
           	<div class="row">
           		<div class="col-md-9">
           			<div class="border" id="lista_archivos">Ningun archivo seleccionado</div>
           		</div>
           		<div class="col-md-3">
           			<span class="btn btn-info btn-round btn-file">
                      <span class="fileinput-new">Buscar</span>
                      <span class="fileinput-exists">Cambiar</span>
                      <input type="file" name="archivos[]" id="archivos" multiple="multiple"/>
                   </span>
                <a href="#" class="btn btn-danger btn-round fileinput-exists" onclick="archivosPreview(1)" data-dismiss="fileinput"><i class="material-icons">clear</i> Quitar</a>
           		</div>
           	</div>
           </div>
           <p class="text-danger">Los archivos se subir&aacute;n al servidor cuando se GUARDE el comprobante</p>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="" class="btn btn-link" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>
<!--    end small modal -->
</form>
<?php require_once 'modal.php';?>
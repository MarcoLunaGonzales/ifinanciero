<?php
session_start();
require_once '../layouts/bodylogin2.php';
require_once '../conexion.php';
require_once '../styles.php';
require_once '../functions.php';
require_once '../functionsGeneral.php';
require_once 'configModule.php';


setlocale(LC_TIME, "Spanish");
set_time_limit(0);

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
$codMesActiva=$_SESSION['globalMes'];
$contadorRegistros=0;
$nombreCompletoUnidad=nameUnidad($globalUnidad);
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
	var distribucionPor=[];
	var configuracionCentro=[];
	var configuraciones=[];
	var estado_cuentas=[];
	var itemCuentas=[];
	var itemCuentasAux=[];
</script>
<?php
  $i=0;
  echo "<script>var array_cuenta_numeros=[],array_cuenta_nombres=[],imagen_cuenta=[];</script>";
   $stmtCuenta = $dbh->prepare("SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 order by p.numero");
   $stmtCuenta->execute();
   while ($rowCuenta = $stmtCuenta->fetch(PDO::FETCH_ASSOC)) {
    $codigoX=$rowCuenta['codigo'];
    $numeroX=$rowCuenta['numero'];
    $nombreX=$rowCuenta['nombre'];
    ?>
    <script>
     var obtejoLista={
       label:'<?=trim($numeroX)?>',
       value:'<?=$codigoX?>'};
     var obtejoLista2={
       label:'<?=trim($nombreX)?>',
       value:'<?=$codigoX?>'};  
       array_cuenta_numeros[<?=$i?>]=obtejoLista;
       array_cuenta_nombres[<?=$i?>]=obtejoLista2;
       imagen_cuenta[<?=$i?>]='../assets/img/calc.jpg';
    </script> 
    <?php
    $i=$i+1;
  }
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

            //ESTADO DE CUENTAS
			$stmt = $dbh->prepare("SELECT * FROM configuracion_estadocuentas");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoX=$row['codigo'];
				$codPlanCuentaX=$row['cod_plancuenta'];
				$codCuentaAuxX=$row['cod_cuentaaux'];
				$tipoX=$row['tipo'];
				$tipoEstadoCuentaX=$row['cod_tipoestadocuenta'];

			 ?>
			 <script>estado_cuentas.push({codigo:<?=$codigoX?>,cod_cuenta:<?=$codPlanCuentaX?>,cod_cuentaaux:<?=$codCuentaAuxX?>,tipo:<?=$tipoX?>,tipo_estado_cuenta:<?=$tipoEstadoCuentaX?>});</script>
		    <?php
			 }
		    ?>

		  	<?php
			$stmt = $dbh->prepare("SELECT d.codigo, d.cod_unidadorganizacional, d.porcentaje FROM distribucion_gastosporcentaje_detalle d join distribucion_gastosporcentaje p on p.codigo=d.cod_distribucion_gastos where p.estado=1");
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
$anioActual=date("Y");
$mesActual=date("m");
$diaActual=date("d");

$month = $globalNombreGestion."-".$codMesActiva;
$aux = date('Y-m-d', strtotime("{$month} + 1 month"));
$diaUltimo = date('d', strtotime("{$aux} - 1 day"));

if((int)$globalNombreGestion<(int)$anioActual){
  $fechaActual=$globalNombreGestion."-".$codMesActiva."-23";
  $fechaActualModal=$diaUltimo."/".$codMesActiva."/".$globalNombreGestion;
}else{
	if((int)$mesActual==(int)$codMesActiva){
      $fechaActual=date("Y-m-d");
      $fechaActualModal=date("d/m/Y");
	}else{
	  $fechaActual=$globalNombreGestion."-".$codMesActiva."-23";
      $fechaActualModal=$diaUltimo."/".$codMesActiva."/".$globalNombreGestion;     
	}	
}


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

			<div class="card" id="cabecera_scroll">
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
	    <label class="col-sm-1 col-form-label" style="text-align: center;">-</label>
	    <label class="col-sm-1 col-form-label" style="text-align: center;">Gestion</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Oficina</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Fecha</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Tipo Comprobante</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Nro. Comprobante</label>
	    <label class="col-sm-1 col-form-label" style="text-align: center;">-</label>
 	</div>
                       <div class="row">
						<div class="col-sm-1">							
						</div>
						<div class="col-sm-1">
							<div class="form-group">
						  		<!--label class="bmd-label-static">Gestion</label-->
					  			<input class="form-control" type="text" name="gestion" value="<?=$globalNombreGestion;?>" id="gestion" readonly="true" style="background-color:#E3CEF6;text-align: left"/>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--label class="bmd-label-static">Oficina</label-->
						  		<input class="form-control" type="text" name="unidad_organizacional" value="<?=$nombreCompletoUnidad;?> - <?=$globalNombreUnidad;?>" id="unidad_organizacional" readonly="true" style="background-color:#E3CEF6;text-align: left"/>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--label class="bmd-label-static">Fecha</label-->
						  		<input class="form-control datepicker" type="text" name="fecha" value="<?=$fechaActualModal?>" id="fecha" required/>
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

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--label for="nro_correlativo" class="bmd-label-static">Nro. Comprobante</label-->
						  		<div id="divnro_correlativo"><input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" readonly="true" style="background-color:#E3CEF6;text-align: left"/></div>
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
					<h4 class="card-title" align="right">
						<div class="row">							
							<div class="col-sm-9">
							    <div class="form-group">
					          		<label for="glosa" class="bmd-label-static">Glosa</label>
									<input class="form-control" name="glosa" id="glosa" required="true" rows="1" value=""/>
								</div>
							</div>	
							<div class="col-sm-3" align="right">
			                   <div class="form-group">                                
			                        <a href="#" style="background-color: #0489B1" class="btn btn-round btn-fab btn-sm" onclick="cargarDatosRegistroComprobantes()">
			                        	<i class="material-icons" title="Add Proveedor">add</i>
			                        </a>
			                        <a href="#" class="btn btn-round btn-fab btn-sm" onclick="actualizarRegistroProveedorComprobante()">
			                        	<i class="material-icons" title="Actualizar Proveedor">update</i>
			                        </a> 
			                   </div>
			                </div> 

						</div>
						
					</h4>

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
					<div class="row menu">
	                    	<div class="col-sm-1">
	                    		<button title="Agregar (alt+a)" type="button" id="add_boton" name="add" class="btn btn-warning btn-fab btn-round btn-sm" onClick="addCuentaContable(this)">
	                  		  <i class="material-icons x-s">add</i>
		                    </button>	
	                    	</div>
	                    	
		                    <label class="col-sm-1 col-form-label text-white" style="text-align: center;">Centro Costos</label>
		                    <label class="col-sm-4 col-form-label text-white" style="text-align: center;">Cuenta</label>
		                    <label class="col-sm-1 col-form-label text-white" style="text-align: center;">Debe</label>
		                    <label class="col-sm-1 col-form-label text-white" style="text-align: center;">Haber</label>
		                    <label class="col-sm-3 col-form-label text-white" style="text-align: center;">Glosa</label>
			                <div class="col-sm-1" align="right">
			                	<a title="Copiar Glosa (shift+g)" id="segundo_copy" href="#modalCopy" data-toggle="modal" data-target="#modalCopy" class="<?=$buttonCeleste?> btn-fab btn-sm d-none">
                      		        <i class="material-icons"><?=$iconCopy?></i>
		                        </a>
								<a title="Copiar Unidad - Area (shift+u)" href="#modalCopySel" data-toggle="modal" data-target="#modalCopySel" class="<?=$buttonDelete?> btn-fab btn-sm">
	                      		  <i class="material-icons"><?=$iconCopy?></i>
			                    </a>
			                </div>
			             
	                    </div>
	                  <div class="wrapper_caja">
					<fieldset id="fiel" style="width:100%;border:0;">				
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
								<div class="col-sm-6">
						      	</div>
								<div class="col-sm-1">
						            <div class="form-group">	
						          		<input class="form-control" type="number" step=".01" name="totaldeb" placeholder="0" id="totaldeb" readonly="true">	
									</div>
						      	</div>
								<div class="col-sm-1">
						            <div class="form-group">
						            	<input class="form-control" type="number" step=".01" name="totalhab" placeholder="0" id="totalhab" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-4">
								</div>
							</div>
	                  	
	                  </div>  
							

				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>

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

<div class="cargar">
  	<div class="div-loading text-center">
     	<h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     	<p class="text-white">Aguard&aacute; un momento por favor</p>  
  	</div>
</div>
<div class="cargar-ajax d-none">
  	<div class="div-loading text-center">
     	<h4 class="text-warning font-weight-bold" id="texto_ajax_titulo">Procesando Datos</h4>
     	<p class="text-white">Aguard&aacute; un momento por favor</p>  
  	</div>
</div>
<div class="modal fade modal-arriba modal-primary" id="modalAgregarProveedor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
    	<div class="modal-content card">
            <div class="card-header card-header-warning card-header-icon">
                <div class="card-icon">
                    <i class="material-icons text-dark">ballot</i>
                </div>
                 <h4 class="card-title">Proveedor</h4>
            </div>
            <div class="card-body">
                <div id="datosProveedorNuevo">
                   
                </div> 
                <div class="form-group float-right">
                        <button type="button" onclick="guardarDatosProveedorComprobante()" class="btn btn-info btn-round">Agregar</button>
                </div>
          	</div>
      	</div>  
    </div>
</div>


<?php 
$dbh = new Conexion();

$sqlBusqueda="SELECT p.codigo, p.numero, p.nombre from plan_cuentas p where p.nivel=5 ";
$sqlBusqueda.=" order by p.numero";


$stmt = $dbh->prepare($sqlBusqueda);
$stmt->execute();
$stmt->bindColumn('codigo', $codigoCuenta);
$stmt->bindColumn('numero', $numeroCuenta);
$stmt->bindColumn('nombre', $nombreCuenta);
		$cont=0;$contAux=0;
		while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {

			$numeroCuenta=trim($numeroCuenta);
			$nombreCuenta=trim($nombreCuenta);

			$sqlCuentasAux="SELECT codigo, nombre FROM cuentas_auxiliares where cod_cuenta='$codigoCuenta' order by 2";
			$stmtAux = $dbh->prepare($sqlCuentasAux);
			$stmtAux->execute();
			$stmtAux->bindColumn('codigo', $codigoCuentaAux);
			$stmtAux->bindColumn('nombre', $nombreCuentaAux);
			while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {
				?><script>itemCuentasAux.push({codigo:"<?=$codigoCuentaAux?>",nombre:"<?=$nombreCuentaAux?>",codCuenta:"<?=$codigoCuenta?>"});</script><?php
				$contAux++;
			}  	
		 ?><script>
		    itemCuentas.push({codigo:"<?=$codigoCuenta?>",numero:"<?=$numeroCuenta?>",nombre:"<?=$nombreCuenta?>",cod_aux:"0",nom_aux:""});
		 </script><?php	
		$cont++;
		}?>

<script>$('.selectpicker').selectpicker("refresh");</script>
<?php require_once 'modal.php';?>

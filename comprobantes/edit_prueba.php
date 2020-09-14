<?php
session_start();
set_time_limit(0);

setlocale(LC_TIME, "Spanish");


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
$globalMesActivo=$_SESSION['globalMes'];

$codPadreArchivos=obtenerValorConfiguracion(84);

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


$cod_cuenta_configuracion_iva=obtenerValorConfiguracion(3);//cuenta iva
$cod_sis_configuracion=obtenerValorConfiguracion(16);//codigo de proyecto sis
//
if(isset($_GET['codigo'])){
	$globalCode=$_GET['codigo'];
}else{
	$globalCode=0;
}

if(isset($_GET['cuentas'])){
   $cuenta_get=json_decode($_GET['cuentas']);

   $cont=contarComprobantesDetalleCuenta($globalCode,$cuenta_get);
}else{
 $cont=contarComprobantesDetalle($globalCode);
}

$cont->bindColumn('total', $contReg);
while ($row = $cont->fetch(PDO::FETCH_BOUND)) {
 $contadorRegistros=$contReg;
}

//totales debe haber 
$totalesDebeHaber=obtenerTotalesDebeHaberComprobante($globalCode);

$totalDebeComp=number_format($totalesDebeHaber[0],2,".","");
$totalHaberComp=number_format($totalesDebeHaber[1],2,".","");
$totalesDif=number_format($totalesDebeHaber[0]-$totalesDebeHaber[1],2,".","");
//unidad organizacional
//configuraciones
$stmtUnidades = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
$stmtUnidades->execute();
$un=0;
 while ($rowUnidades = $stmtUnidades->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$rowUnidades['codigo'];
	$nombreX=$rowUnidades['nombre'];
	$abrevX=$rowUnidades['abreviatura'];
    
    $arrayUnidadOrganizacional[$un]['codigo']=$codigoX;
    $arrayUnidadOrganizacional[$un]['nombre']=$nombreX;
    $arrayUnidadOrganizacional[$un]['abreviatura']=$abrevX;
    $un++; 
 }
//areas 
$stmtAreas = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
$stmtAreas->execute();
$un=0;
 while ($rowAreas = $stmtAreas->fetch(PDO::FETCH_ASSOC)) {
	$codigoX=$rowAreas['codigo'];
	$nombreX=$rowAreas['nombre'];
	$abrevX=$rowAreas['abreviatura'];
    
    $arrayAreas[$un]['codigo']=$codigoX;
    $arrayAreas[$un]['nombre']=$nombreX;
    $arrayAreas[$un]['abreviatura']=$abrevX;
    $un++; 
 }

//cuentas auxliliares
 $un=0;
 $sqlCuentasAux="SELECT codigo, nombre,cod_cuenta FROM cuentas_auxiliares order by 2";
	$stmtAux = $dbh->prepare($sqlCuentasAux);
	$stmtAux->execute();
	$stmtAux->bindColumn('codigo', $codigoCuentaAux);
	$stmtAux->bindColumn('nombre', $nombreCuentaAux);
	$stmtAux->bindColumn('cod_cuenta', $codigoCuentaPlan);
  while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {  
  	  $arrayCuentasAux[$un]['cod_cuenta']=$codigoCuentaPlan;
      $arrayCuentasAux[$un]['codigo']=$codigoCuentaAux;
      $arrayCuentasAux[$un]['nombre']=$nombreCuentaAux;
      $un++;
	}

//facturas
 $un=0;
$stmt = $dbh->prepare("SELECT * FROM facturas_compra");
$stmt->execute();
$arrayFacturasGenerales=[];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  	 $arrayFacturasGenerales[$un]['cod_comprobantedetalle']=$row['cod_comprobantedetalle'];
	 $arrayFacturasGenerales[$un]['nit']=$row['nit'];
	 $arrayFacturasGenerales[$un]['nro_factura']=$row['nro_factura'];
	 $arrayFacturasGenerales[$un]['fecha']=$row['fecha'];
	 $arrayFacturasGenerales[$un]['razon_social']=$row['razon_social'];
	 $arrayFacturasGenerales[$un]['importe']=$row['importe'];
	 $arrayFacturasGenerales[$un]['nro_autorizacion']=$row['nro_autorizacion'];
	 $arrayFacturasGenerales[$un]['codigo_control']=$row['codigo_control'];
	 $arrayFacturasGenerales[$un]['exento']=$row['exento'];
	 $arrayFacturasGenerales[$un]['ice']=$row['ice'];
	 $arrayFacturasGenerales[$un]['tipo_compra']=$row['tipo_compra'];
	 $arrayFacturasGenerales[$un]['tasa_cero']=$row['tasa_cero'];
	$un++; 
	}
//estados de Cuentas
$un=0;
$stmt = $dbh->prepare("SELECT * FROM estados_cuenta");
$stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  	 $arrayEstadosCuentas[$un]['cod_comprobantedetalle']=$row['cod_comprobantedetalle'];
	 $arrayEstadosCuentas[$un]['cod_plancuenta']=$row['cod_plancuenta'];
	 $arrayEstadosCuentas[$un]['cod_comprobantedetalleorigen']=$row['cod_comprobantedetalleorigen'];
	 $arrayEstadosCuentas[$un]['monto']=$row['monto'];
	$un++; 
	}
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
	var distribucionPor=[];
	var distribucionPorArea=[];
	var configuracionCentro=[];
	var configuraciones=[];
	var estado_cuentas=[];
	var libretas_bancarias=[];
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
            
             //LIBRETAS BANCARIAS DETALLE CARGAR
             $stmt = $dbh->prepare("SELECT p.nombre as banco,dc.* FROM libretas_bancarias dc join bancos p on dc.cod_banco=p.codigo WHERE dc.cod_estadoreferencial=1");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoX=$row['codigo'];
				$bancoX=$row['banco'];
				$cod_banco=$row['cod_banco'];
				$cod_cuenta=$row['cod_cuenta'];
				$cod_contracuenta=$row['cod_contracuenta'];
				$nombreX=$row['nombre'];
				$nombreBan=nameBancos($cod_banco);
                if($nombreBan==""){
                  $nombreBan=$Banco." - ".$nombreX;
                }else{
                  $nombreBan=$nombreBan." - ".$nombreX;  
                }
			?>
			 <script>libretas_bancarias.push({codigo:<?=$codigoX?>,cod_cuenta:<?=$cod_cuenta?>,cod_contracuenta:<?=$cod_contracuenta?>,nombre_libreta:'<?=$nombreBan?>'});</script>
		    <?php
			 }

            //ESTADO DE CUENTAS
			$stmt = $dbh->prepare("SELECT * FROM configuracion_estadocuentas where cod_estadoreferencial=1");
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
			$stmt = $dbh->prepare("SELECT d.codigo, d.cod_unidadorganizacional, d.porcentaje FROM distribucion_gastosporcentaje_detalle d join distribucion_gastosporcentaje p on p.codigo=d.cod_distribucion_gastos where p.estado=1 and d.porcentaje>0");
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
			$stmt = $dbh->prepare("SELECT d.codigo, d.cod_area, d.porcentaje FROM distribucion_gastosarea_detalle d join distribucion_gastosarea p on p.codigo=d.cod_distribucionarea where p.estado=1 and d.porcentaje>0 and p.cod_uo='$globalUnidad'");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoX=$row['codigo'];
				$areaX=$row['cod_area'];
				$porcentajeX=$row['porcentaje'];
			 ?>
			 <script>distribucionPorArea.push({codigo:<?=$codigoX?>,cod_area:<?=$areaX?>,porcent:<?=$porcentajeX?>});</script>
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

			$cod_cuenta_configuracion_iva=obtenerValorConfiguracion(3);//cuenta iva
		    ?>

<form id="formRegComp" class="form-horizontal" action="saveEdit.php" method="post" enctype="multipart/form-data">
	
	<input type="hidden" name="edicion" id="edicion" value="1">
	<input type="hidden" name="cod_cuenta_configuracion_iva" id="cod_cuenta_configuracion_iva" value="<?=$cod_cuenta_configuracion_iva;?>">
	<input type="hidden" name="cod_sis_configuracion" id="cod_sis_configuracion" value="<?=$cod_sis_configuracion;?>">
	<?php 
 if(isset($_GET['cuentas'])){
 	?><input type="hidden" name="incompleto" id="incompleto" value="1"><?php
 }
	?>
<div class="content">
	<div class="container-fluid">
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
		   //ESTADO DE CUENTAS
			$stmt = $dbh->prepare("SELECT * FROM configuracion_estadocuentas");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoX=$row['codigo'];
				$codPlanCuentaX=$row['cod_plancuenta'];
				$codCuentaAuxX=$row['cod_cuentaaux'];
				$tipoX=$row['tipo'];
			 ?>
			 <script>estado_cuentas.push({codigo:<?=$codigoX?>,cod_cuenta:<?=$codPlanCuentaX?>,cod_cuentaaux:<?=$codCuentaAuxX?>,tipo:<?=$tipoX?>});</script>
		    <?php
			 }
		    
			$stmt = $dbh->prepare("SELECT codigo, cod_unidadorganizacional, porcentaje FROM distribucion_gastosporcentaje");
			$stmt->execute();
			$i=0;
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				$codigoS=$row['codigo'];
				$unidadS=$row['cod_unidadorganizacional'];
				$porcentajeS=$row['porcentaje'];
			 ?>
			 <script>distribucionPor.push({codigo:<?=$codigoS?>,cod_unidad:<?=$unidadS?>,porcent:<?=$porcentajeS?>});</script>
		    <?php
			 }
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
		    <input type="hidden" name="codigo_iva_direfido" id="codigo_iva_direfido" value="<?=obtenerValorConfiguracion(67)?>">
			<input type="hidden" name="cod_cuenta_configuracion_iva" id="cod_cuenta_configuracion_iva" value="<?=$cod_cuenta_configuracion_iva;?>">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="codigo_comprobante" id="codigo_comprobante" value="<?=$globalCode;?>">

			<input type="hidden" name="global_gestion" id="global_gestion" value="<?=$globalNombreGestion;?>">
			<input type="hidden" name="global_mes" id="global_mes" value="<?=$globalMesActivo;?>">

			<div class="card" id="cabecera_scroll">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Editar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
					<div class="row">
	    <label class="col-sm-1 col-form-label" style="text-align: center;">-</label>
	    <label class="col-sm-1 col-form-label" style="text-align: center;">Gestion</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Oficina</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Tipo Comprobante</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Nro. Comprobante</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Fecha</label>
	    <label class="col-sm-1 col-form-label" style="text-align: center;">-</label>
 	</div>
					<div class="row">
						<div class="col-sm-1">							
						</div>
				 	<?php 
                  while ($row = $data->fetch(PDO::FETCH_BOUND)) {
                  	$fechaComprobanteModal=$fechaComprobante;
					$fechaComprobanteModal = date("Y-m-d",strtotime($fechaComprobanteModal));
                  	
                  	$vectorFechas=buscarFechasMinMaxComprobante($tipoComprobante, $nroCorrelativo, $globalUnidad, $globalNombreGestion, $globalMesActivo,$_GET['codigo']);
                  	
                  	$cadenaVectorFechas=implode(",",$vectorFechas);
                  	list($fechaInicioDefault, $fechaFinalDefault)=explode(",", $cadenaVectorFechas);
                  	//echo $fechaInicioDefault." ".$fechaFinalDefault;
                  	//$fechaMaxima=buscarFechaMaxima($tipoComprobante, $nroCorrelativo, $globalUnidad);
				 	?>
						<div class="col-sm-1">
							<div class="form-group">
						  		<!--<label class="bmd-label-static">Gestion</label>-->
					  			<input style="background-color:#E3CEF6;text-align: left" class="form-control" type="text" name="gestion" value="<?=$gestion;?>" id="gestion" readonly="true" />
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--<label class="bmd-label-static">Unidad</label>-->
						  		<input style="background-color:#E3CEF6;text-align: left" class="form-control" type="text" name="unidad_organizacional" value="<?=$unidad;?>" id="unidad_organizacional" readonly="true" />
							</div>
						</div>

						<div class="col-sm-2">
				        	<div class="form-group">
						        <select class="selectpicker form-control form-control-sm" name="tipo_comprobante" id="tipo_comprobante" data-style="<?=$comboColor;?>" onChange="ajaxCorrelativo(this);">
								  	
							  	<?php
							  	if($tipoComprobante==0){
                                   ?><option disabled selected value="">Tipo</option><?php  
							  	}else{
                                   ?><option disabled value="">Tipo</option><?php
							  	}
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 and codigo='$tipoComprobante' order by 1");
								$stmt->execute();
								while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
									$codigoX=$row['codigo'];
									$nombreX=$row['nombre'];
									$abrevX=$row['abreviatura'];
									if($codigoX==$tipoComprobante){
                                     ?><option selected value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
									}else{
                                     ?><option value="<?=$codigoX;?>"><?=$nombreX;?></option><?php
									}
							  	}
							  	?>
							</select>
							</div>
				      	</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--<label for="nro_correlativo" class="bmd-label-static">#</label>-->
						  		<div id="divnro_correlativo"><input style="background-color:#E3CEF6;text-align: left" class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" readonly="true" value="<?=$nroCorrelativo?>" /></div>
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--<label class="bmd-label-static">Fecha</label>-->
						  		<input class="form-control" type="date" name="fecha" value="<?=$fechaComprobanteModal;?>" id="fecha" min="<?=$fechaInicioDefault;?>" max="<?=$fechaFinalDefault;?>" />
							</div>
						</div>
						
					    <!--<div class="col-sm-4">
						    <div class="form-group">
				          		<label for="glosa" class="bmd-label-static">Glosa</label>
								<textarea class="form-control" name="glosa" id="glosa" required="true" rows="2" value=""><?=$glosaComprobante?></textarea>
							</div>
						</div>-->
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
		                         <a  title="Pegar Datos Excel" href="#" onclick="modalPegarDatosComprobante()" class="btn btn-primary btn-fab btn-sm">
	                      		        <i class="material-icons">content_paste</i>
			                     </a>
                            </div>
						</div>
					</div>
                   <?php
                    $glosaMadre=$glosaComprobante;
                    } ?>
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
									<!-- <input class="form-control" name="glosa" id="glosa" required="true" rows="1" value="<?=$glosaMadre?>"/> -->
									<textarea class="form-control" name="glosa" id="glosa" required="true"><?=$glosaMadre?></textarea>
								</div>
							</div>
							<div class="col-sm-3" align="right">
			                    <div class="form-group">                                
			                        <!-- <a href="#"  class="btn btn-round btn-fab btn-sm" onclick="cargarDatosRegistroComprobantes()">
			                        	<i class="material-icons" title="Add Proveedor">add</i>
			                        </a> -->
			                        <a href="#" id="boton_fac_cabecera" class="btn btn-round btn-info btn-fab btn-sm" onclick="mostrarOcultarFacturasComprobante();return false;">
			                        	<i class="material-icons" title="Mostrar / Ocultar Facturas Secundarias">featured_play_list</i><span class="bg-warning estado"></span> 
			                        </a>
			                        <button style="background-color: #0489B1" title="Registrar Cuenta Auxiliar" class="btn btn-round btn-fab btn-sm" type="button" data-toggle="modal" data-target="#modalRegisterCuentasAux">
	                                    <i class="material-icons text-info">add</i>
	                                </button>
			                        <!-- <a href="#" class="btn btn-round btn-fab btn-sm" onclick="actualizarRegistroProveedorComprobante()">
			                        	<i class="material-icons" title="Actualizar Proveedor">update</i>
			                        </a>  -->
			                    </div>
			                </div>	
						</div>
					</h4>
				</div>
				<div class="card-body ">
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
					<?php
					//buscar detalles del comprobante
					?>
					<div class="wrapper_caja">
					<fieldset id="fiel" style="width:100%;border:0;">
						     
		                <div id="div">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
			            <?php
			            if(isset($_GET['cuentas'])){	
                         $detalle=obtenerComprobantesDetCuenta($globalCode,$cuenta_get); 
			            }else{
			             $detalle=obtenerComprobantesDet($globalCode);	
			            }
						
						$idFila=1;$totaldebDet=0;$totalhabDet=0;
						while ($row = $detalle->fetch(PDO::FETCH_ASSOC)) {

							$codDet=$row['cod_det'];
							$unidadDet=$row['cod_unidadorganizacional'];
							$areaDet=$row['cod_area'];
							$debeDet=number_format($row['debe'],2,'.','');
							$haberDet=number_format($row['haber'],2,'.','');
							$glosaDet=$row['glosa'];
							$numeroDet=$row['numero'];
							$nombreDet=$row['nombre'];
							$cuentaAuxDet=$row['cuenta_auxiliar'];
							$codCuentaAuxDet=$row['cod_cuentaauxiliar'];
							$totaldebDet+=$row['debe'];$totalhabDet+=$row['haber'];
							$codigoCuenta=$row['codigo'];

							//echo $unidadDet." ".$areaDet;
                            $codDetalleSolicitudSis=obtenerCodigoSolicitudRecursoSisComprobante($codDet);
                            $estiloSolicitudRecurso="";
							if($codDetalleSolicitudSis!=0){
							 $estiloSolicitudRecurso="estado";
							}

							$codActividadProyecto=obtenerCodigoActividadSisComprobante($codDet);
                            $estiloActividadProyecto="";
							if($codActividadProyecto!=0){
							 $estiloActividadProyecto="estado";
							}

							$codAccNum=obtenerCodigoAccNumSisComprobante($codDet);
                            /*$estiloActividadProyecto="";
							if($codAccNum!=0){
							 $estiloActividadProyecto="estado";
							}*/


							$codDetalleLibreta=obtenerCodigoLibretaDetalleComprobante($codDet);
							$descripcionDetalleLibreta=obtenerDescripcionLibretaDetalleComprobante($codDet);
							$estiloLibreta="";
							if($codDetalleLibreta!=0){
							 $estiloLibreta="estado";
							}

						 ?>
                         <div id="div<?=$idFila?>">               	         
                             <div class="col-md-12">
                             	<div class="row">                     
		                          <div class="col-sm-1">
                                  	<div class="form-group">
                                  	<span id="numero_fila<?=$idFila?>" style="position:absolute;left:-15px; font-size:16px;font-weight:600; color:#386D93;"><?=$idFila?></span>
	                                  <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="<?=$comboColor;?>" onChange="relacionSolicitudesSIS(<?=$idFila;?>)">  
			  	                         <?php
			  	                         if($unidadDet==0){
			  	                         ?><option disabled selected="selected" value="">Unidad</option><?php	
			  	                         }else{
			  	                         	?><option disabled value="">Unidad</option><?php
			  	                         }
			  	                         for ($i=0; $i < count($arrayUnidadOrganizacional) ; $i++) {
			  	                             $codigoX=$arrayUnidadOrganizacional[$i]['codigo'];
			  	                             $abrevX=$arrayUnidadOrganizacional[$i]['abreviatura'];
			  	                         	if($codigoX==$unidadDet){
                                             ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
				                           	}else{
                                              ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
				                            }
			  	                         }
			  	                          ?>
			                          </select>
			                         </div>
      	                          </div>

		                         <div class="col-sm-1">
                                 	<div class="form-group">
	                                 <select class="selectpicker form-control form-control-sm" name="area<?=$idFila;?>" id="area<?=$idFila;?>" data-style="<?=$comboColor;?>" >
			  	                        
			  	                        
			  	                        <?php
			  	                        if($areaDet==0){
			  	                         ?><option disabled selected="selected" value="">Area</option><?php	
			  	                         }else{
			  	                         	?><option disabled value="">Area</option><?php
			  	                         }
			  	                         for ($i=0; $i < count($arrayAreas) ; $i++) {
			  	                             $codigoX=$arrayAreas[$i]['codigo'];
			  	                             $abrevX=$arrayAreas[$i]['abreviatura'];
			  	                         	if($codigoX==$areaDet){
                                             ?><option value="<?=$codigoX;?>" selected><?=$abrevX;?></option><?php
				                           	}else{
                                              ?><option value="<?=$codigoX;?>"><?=$abrevX;?></option><?php
				                            }
			  	                         }
			  	                         ?>
			                         </select>
			                       </div>
      	                         </div>
      	                         <div class="col-sm-4">
      	                        	<input type="hidden" name="cuenta<?=$idFila;?>" id="cuenta<?=$idFila;?>" value="">
      	                        	<input type="hidden" name="codigo_detalle<?=$idFila;?>" id="codigo_detalle<?=$idFila;?>" value="<?=$codDet?>">
    	                        	<input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" id="cuenta_auxiliar<?=$idFila;?>" value="">
                                	<div class="row">	
    			                        <div class="col-sm-8">
    			                        	<div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
    			                        
        	                                </div>
    			                        </div>
    			                        <div class="col-sm-4">
    			                        	<div class="btn-group">
                                             <div class="btn-group dropdown">
                    	                        <button type="button" class="btn btn-sm btn-info btn-fab dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="MAYORES">
                    	                        <i class="material-icons">list</i>
                                                </button>
                                                <div class="dropdown-menu">
                                                <a title="Mayores" href="#" id="mayor<?=$idFila?>" onclick="mayorReporteComprobante(<?=$idFila?>)" class="dropdown-item"><span class="material-icons text-info">list</span> Ver Reporte Mayor</a>	  		  
                                                <a title="Cerrar Comprobante" id="cerrar_detalles<?=$idFila?>" href="#" onclick="verMayoresCierre(<?=$idFila;?>);" class="dropdown-item"><span class="material-icons text-danger">ballot</span> Cerrar Comprobantes</a>       
                                                </div>
                                            </div>     			                        		
    			                        	 <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>	  
                        	             	<div class="btn-group dropdown">
								              <button type="button" class="btn btn-sm btn-success btn-fab dropdown-toggle material-icons text-dark" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Distribucion de Gastos">
								                <i class="material-icons">call_split</i>
								              </button>
								              <div class="dropdown-menu">   
								                <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionX<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,1);" class="dropdown-item">
								                  <i class="material-icons">bubble_chart</i> x Oficina
								                </a>
								                <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucionY<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>,2);" class="dropdown-item">
								                  <i class="material-icons">bubble_chart</i> x Área
								                </a>
								              </div>
								            </div>  
    			                             <input type="hidden" id="tipo_estadocuentas<?=$idFila?>" value="-100">
    			                             <input type="hidden" id="tipo_proveedorcliente<?=$idFila?>" value="-100">
    			                             <input type="hidden" id="proveedorcliente<?=$idFila?>" value="-100">
						    			     <input type="hidden" id="tipo_estadocuentas_casoespecial<?=$idFila?>">
						    			     
    			                             <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>	  
    			                              <!--LIBRETAS BANCARIAS DETALLE-->
    			                              <a title="Libretas Bancarias" id="libretas_bancarias<?=$idFila?>" href="#" onclick="verLibretasBancarias(<?=$idFila;?>);" class="btn btn-sm btn-primary btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestadolib<?=$idFila?>" class="bg-warning <?=$estiloLibreta?>"></span></a>       
    			                              <input type="hidden" id="cod_detallelibreta<?=$idFila?>" name="cod_detallelibreta<?=$idFila?>" value="<?=$codDetalleLibreta?>">
    			                              <input type="hidden" id="descripcion_detallelibreta<?=$idFila?>" value="<?=$descripcionDetalleLibreta?>">
    			                              <input type="hidden" id="tipo_libretabancaria<?=$idFila?>" value="">
    			                              <!--SOLICITUD DE RECURSOS SIS-->
                                               <input type="hidden" id="cod_detallesolicitudsis<?=$idFila?>" name="cod_detallesolicitudsis<?=$idFila?>" value="<?=$codDetalleSolicitudSis?>">

                                               <input type="hidden" id="cod_actividadproyecto<?=$idFila?>" name="cod_actividadproyecto<?=$idFila?>" value="<?=$codActividadProyecto?>">
               								   <input type="hidden" id="cod_accnum<?=$idFila?>" name="cod_accnum<?=$idFila?>" value="<?=$codAccNum?>">
                                               <!---->
    			                            </div>  
    			                        </div>
    		                        </div>
      	                        </div>
                                <?php
		                          	$numeroCuenta=trim($numeroDet);
		                          	$nombreCuenta=trim($nombreDet);
		                          	$existeAux=0;
                                    ?><script>filaActiva=<?=$idFila?>;</script><?php
                                    for ($i=0; $i < count($arrayCuentasAux) ; $i++) {
			  	                        $codigoCuentaAux=$arrayCuentasAux[$i]['codigo'];
			  	                        $codX=$arrayCuentasAux[$i]['cod_cuenta'];
			  	                        $nombreCuentaAux=$arrayCuentasAux[$i]['nombre'];
			  	                        if($codCuentaAuxDet==$codigoCuentaAux){
			  	                        	$existeAux=1;
			  	                        	break;
			  	                         
			  	                        }
			  	                     }
			  	                     if($existeAux==0){
                                       ?><script>setBusquedaCuentaEdit('<?=$codigoCuenta;?>','<?=$numeroCuenta;?>','<?=$nombreCuenta;?>','0','');</script><?php		
			  	                     }else{
			  	                       ?><script>setBusquedaCuentaEdit('<?=$codigoCuenta?>','<?=$numeroCuenta?>','<?=$nombreCuenta?>','<?=$codigoCuentaAux?>','<?=$nombreCuentaAux?>');</script><?php		
			  	                     }
                                  ?>
		                        <div class="col-sm-1">
                                    <div class="form-group">
                                    	<!--<label class="bmd-label-static">Debe</label>-->			
                                  		<input class="form-control small" type="number" placeholder="0" value="<?=$debeDet?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="any">	
		                        	</div>
      	                        </div>

		                        <div class="col-sm-1">
                                    <div class="form-group">
                                    	<!--<label class="bmd-label-static">Haber</label>-->			
                                  		<input class="form-control small" type="number" placeholder="0" value="<?=$haberDet?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="any"> 	
		                        	</div>
      	                        </div>

      	                        <div class="col-sm-3">
		                            <div class="form-group">
                                  		<!--<label class="bmd-label-static">GlosaDetalle</label>-->
		                        		<textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosaDet?></textarea>
		                        	</div>
		                        </div>

		                       <div class="col-sm-1">
		                         <div class="btn-group">
		                         	<a href="#" title="Retenciones" id="boton_ret<?=$idFila;?>" onclick="listRetencion(<?=$idFila;?>);" class="btn btn-warning text-dark btn-sm btn-fab">
                                     <i class="material-icons">ballot</i>
                                   </a>
		                         	<a title="Facturas" href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="facturas-boton btn btn-info btn-sm btn-fab <?=($cod_cuenta_configuracion_iva==$codigoCuenta)?'':'btn-default text-dark d-none';?>" >
                                      <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
                                    </a>
                                    <a title="Actividad Proyecto SIS" id="boton_actividad_proyecto<?=$idFila?>" href="#" onclick="verActividadesProyectosSis(<?=$idFila;?>);" class="btn btn-sm btn-orange btn-fab d-none"><span class="material-icons">assignment</span><span id="nestadoactproy<?=$idFila?>" class="bg-warning <?=$estiloActividadProyecto?>"></span></a>
                                    <a title="Solicitudes de Recursos SIS" id="boton_solicitud_recurso<?=$idFila?>" href="#" onclick="verSolicitudesDeRecursosSis(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab d-none"><span class="material-icons text-dark">view_sidebar</span><span id="nestadosol<?=$idFila?>" class="bg-warning <?=$estiloSolicitudRecurso?>"></span></a>
                                    <a title="Agregar Fila" id="boton_agregar_fila<?=$idFila?>" href="#" onclick="agregarFilaComprobante(<?=$idFila;?>);return false;" class="btn btn-sm btn-primary btn-fab"><span class="material-icons">add</span></a>              
                                   <a title="Eliminar (alt + q)" rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="quitarFilaComprobante('<?=$idFila;?>');return false;">
                                           <i class="material-icons">disabled_by_default</i>
                                    </a>
	                             </div>  
		                       </div>

	                          </div>
                            </div>
                           <div class="h-divider"></div>
                         </div>

                       <script>var nfac=[];
      itemFacturas.push(nfac);var nest=[];
      itemEstadosCuentas.push(nest);itemFacturas[<?=$idFila?>]=[];relacionSolicitudesSIS(<?=$idFila?>);</script>
						 <?php

						      for ($i=0; $i < count($arrayFacturasGenerales) ; $i++) {
						      	    $codX=$arrayFacturasGenerales[$i]['cod_comprobantedetalle'];
			  	                    $nit=$arrayFacturasGenerales[$i]['nit'];
				                    $factura=$arrayFacturasGenerales[$i]['nro_factura'];
				                    $fechaFac=$arrayFacturasGenerales[$i]['fecha'];
				                    $razon=$arrayFacturasGenerales[$i]['razon_social'];
				                    $importe=$arrayFacturasGenerales[$i]['importe'];
				                    $autorizacion=$arrayFacturasGenerales[$i]['nro_autorizacion'];
				                    $control=$arrayFacturasGenerales[$i]['codigo_control'];
				                    $exento=$arrayFacturasGenerales[$i]['exento'];
				                    $ice=$arrayFacturasGenerales[$i]['ice'];
				                    $tipocompra=$arrayFacturasGenerales[$i]['tipo_compra'];
				                    $tasacero=$arrayFacturasGenerales[$i]['tasa_cero'];
			  	                   if($codX==$codDet){
			  	                     ?><script>abrirFactura(<?=$idFila?>,'<?=$nit?>',<?=$factura?>,'<?=$fechaFac?>','<?=$razon?>',<?=$importe?>,<?=$exento?>,'<?=$autorizacion?>','<?=$control?>','<?=$ice?>','<?=$tipocompra?>','<?=$tasacero?>');</script><?php
			  	                   }
			                   } 	

						      // estados de cuenta
			                   for ($i=0; $i < count($arrayEstadosCuentas) ; $i++) {
						      	    $codX=$arrayEstadosCuentas[$i]['cod_comprobantedetalle'];
			  	                    $cuenta=$arrayEstadosCuentas[$i]['cod_plancuenta'];
				                    $codComproDet=$arrayEstadosCuentas[$i]['cod_comprobantedetalleorigen'];
				                    $monto=$arrayEstadosCuentas[$i]['monto'];
			  	                   if($codX==$codDet){
			  	                    ?><script>abrirEstado(<?=$idFila?>,'<?=$cuenta?>',<?=$codComproDet?>,'<?=$monto?>');</script><?php
			  	                   }
			                   }
						 $idFila=$idFila+1;
						}
						?>
		            </fieldset>
							<div class="row">
								<div class="col-sm-6">
						      	</div>
								<div class="col-sm-1">
						            <div class="form-group">	
						          		<input class="form-control" type="hidden" name="totaldeb_restante" placeholder="0" id="totaldeb_restante" readonly="true">
						          		<input class="form-control" type="hidden" name="totaldeb_total" placeholder="0" id="totaldeb_total" readonly="true">	
						          		<input class="form-control d-none" type="number" name="totaldeb" value="<?=$totalDebeComp?>" placeholder="0" id="totaldeb" readonly="true">
									</div>
						      	</div>
								<div class="col-sm-1">
						            <div class="form-group">
						            	<input class="form-control" type="hidden" name="totalhab_restante" placeholder="0" id="totalhab_restante" readonly="true">
						            	<input class="form-control" type="hidden" name="totalhab_total" placeholder="0" id="totalhab_total" readonly="true">
						            	<input class="form-control d-none" type="number" name="totalhab" value="<?=$totalHaberComp?>" placeholder="0" id="totalhab" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-1">
						            <div class="form-group">
						            	<input class="form-control text-primary d-none" value="<?=$totalesDif?>" type="number" name="total_dif" placeholder="0" id="total_dif" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-3">
								</div>
							</div>
					  </div>		
				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<?php 
                        if(isset($_GET['cuentas'])){
                         ?><a href="../<?=$urlEdit3;?>?codigo=<?=$globalCode;?>" class="<?=$buttonCancel;?>"> Volver a la Seleccion</a><?php
			            }else{
			            	?><a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>"> Volver </a><?php	
			             }  
						?>
						<div class="row col-sm-12">
								<div class="col-sm-6">
						      	</div>
								<div class="col-sm-2">
						            <div class="form-group">
						                <label class="bmd-label-static fondo-boton">Debe</label>	
						          		<input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step="any" placeholder="0" value="<?=$totalDebeComp?>" id="totaldeb_fijo" readonly="true">	
									</div>
						      	</div>
								<div class="col-sm-2">
						            <div class="form-group">
						            	<label class="bmd-label-static fondo-boton">Haber</label>	
						            	<input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step="any" placeholder="0" value="<?=$totalHaberComp?>" id="totalhab_fijo" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-2">
						            <div class="form-group">
						            	<label class="bmd-label-static fondo-boton">Diferencia</label>	
						            	<input class="form-control fondo-boton-active text-center" style="border-radius:10px;" type="number" step="any" placeholder="0" value="<?=$totalesDif?>" id="total_dif_fijo" readonly="true">	
									</div>
						      	</div>
							</div>
				  	</div>

				</div>
			</div>	
		
	</div>
</div>

<!-- small modal -->
<!--<div class="modal fade modal-primary" id="modalFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
</div>-->
<!--    end small modal -->

<!-- small modal -->
<div class="modal fade modal-primary" id="modalFile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content card">
      <div class="card-header card-header-info card-header-text">
                  <div class="card-text">
                    <h5>DOCUMENTOS DE RESPALDO</h5>      
                  </div>
                  <button type="button" class="btn btn-danger btn-sm btn-fab float-right" data-dismiss="modal" aria-hidden="true">
                    <i class="material-icons">close</i>
                  </button>
                </div>
      <div class="card-body">
      	<input type="hidden" value="-100" id="tipo_documento_otro" name="tipo_documento_otro">
           <p class="text-muted"><small>Los archivos se subir&aacute;n al servidor cuando se GUARDE el COMPROBANTE</small></p>
            <div class="row col-sm-11 div-center">
              <table class="table table-warning table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="small" width="30%">Tipo de Documento <a href="#" title="Otro Documento" class="btn btn-primary btn-round btn-sm btn-fab float-left" onClick="agregarFilaArchivosAdjuntosCabecera()"><i class="material-icons">add</i></a></th>
                    <th class="small">Obligatorio</th>
                    <th class="small" width="35%">Archivo</th>
                    <th class="small">Descripción</th>                  
                  </tr>
                </thead>
                <tbody id="tabla_archivos">
                  <?php
                  $stmtArchivo = $dbh->prepare("SELECT * from ibnorca.vw_plantillaDocumentos where idTipoServicio=$codPadreArchivos"); //$codPadreArchivos //$codPadreArchivos localhost
                  $stmtArchivo->execute();
                  $filaA=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaA++;
                     $codigoX=$rowArchivo['idClaDocumento'];
                     $nombreX=$rowArchivo['Documento'];
                     $ObligatorioX=$rowArchivo['Obligatorio'];
                     $Obli='<i class="material-icons text-danger">clear</i> NO';
                     if($ObligatorioX==1){
                      $Obli='<i class="material-icons text-success">done</i> SI<input type="hidden" id="obligatorio_file'.$filaA.'" value="1">';
                     }
                     $verificarArchivo=verificarArchivoAdjuntoExistente($codPadreArchivos,$globalCode,0,$codigoX);
                     //$nombreX=$verificarArchivo[1];
                     $urlArchivo=$verificarArchivo[2];
                     $codigoArchivoX=$verificarArchivo[3];
                  ?>
                  <tr>
                    <td class="text-left"><input type="hidden" name="codigo_archivo<?=$filaA?>" id="codigo_archivo<?=$filaA?>" value="<?=$codigoX;?>"><input type="hidden" name="nombre_archivo<?=$filaA?>" id="nombre_archivo<?=$filaA?>" value="<?=$nombreX;?>"><?=$nombreX;?></td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <?php
                      if($verificarArchivo[0]==0){
                       ?>
                      <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                      <span class="input-archivo">
                        <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                      </span>
                      <label title="Ningún archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-warning btn-sm"><i class="material-icons">publish</i> Subir Archivo
                      </label>
                       <?php
                      }else{
                        ?>
                        <small id="existe_archivo_cabecera<?=$filaA?>"></small>

                        <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                        <span class="input-archivo">
                          <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                        </span>
                        <label title="Ningún archivo - Click para Cambiar el Archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-success btn-sm btn-fab"><i class="material-icons">publish</i>
                        </label>
                        <div class="btn-group" id="existe_div_archivo_cabecera<?=$filaA?>">
                        <a href="#" class="btn btn-button btn-sm">Registrado</a>
                        <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  
                        <a href="#" title="Quitar" class="btn btn-danger btn-sm" onClick="quitarArchivoSistemaAdjunto(<?=$filaA?>,<?=$codigoArchivoX;?>,0)"><i class="material-icons">delete_outline</i></a>
                        </div> 
                        <?php
                      }
                    ?>  
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                  $stmtArchivo = $dbh->prepare("SELECT * from archivos_adjuntos where cod_tipoarchivo=-100 and cod_tipopadre=$codPadreArchivos and cod_objeto=$globalCode and cod_padre=0"); //$codPadreArchivos //$codPadreArchivos localhost
                  $stmtArchivo->execute();
                  $filaE=0;
                  while ($rowArchivo = $stmtArchivo->fetch(PDO::FETCH_ASSOC)) {
                     $filaE++;
                     $filaA++;
                     $codigoArchivoX=$rowArchivo['codigo'];
                     $codigoX=$rowArchivo['cod_tipoarchivo'];
                     $nombreX=$rowArchivo['descripcion'];
                     $urlArchivo=$rowArchivo['direccion_archivo'];
                     $ObligatorioX=0;
                     $Obli='<i class="material-icons text-danger">clear</i> NO';
                     if($ObligatorioX==1){
                      $Obli='<i class="material-icons text-success">done</i> SI';
                     }

                  ?>
                  <tr id="fila_archivo<?=$filaA?>">
                    <td class="text-left"><input type="hidden" name="codigo_archivoregistrado<?=$filaE?>" id="codigo_archivoregistrado<?=$filaE?>" value="<?=$codigoArchivoX;?>">Otros Documentos</td>
                    <td class="text-center"><?=$Obli?></td>
                    <td class="text-right">
                      <small id="existe_archivo_cabecera<?=$filaA?>"></small>

                        <small id="label_txt_documentos_cabecera<?=$filaA?>"></small> 
                        <span class="input-archivo">
                          <input type="file" class="archivo" name="documentos_cabecera<?=$filaA?>" id="documentos_cabecera<?=$filaA?>"/>
                        </span>
                        <label title="Ningún archivo" for="documentos_cabecera<?=$filaA?>" id="label_documentos_cabecera<?=$filaA?>" class="label-archivo btn btn-success btn-sm"><i class="material-icons">publish</i> Cambiar Archivo
                        </label>
                      <div class="btn-group">
                        <a href="#" class="btn btn-button btn-sm" >Registrado</a>  
                        <a class="btn btn-button btn-info btn-sm" href="<?=$urlArchivo?>" title="Descargar: Doc - IFINANCIERO (<?=$nombreX?>)" download="Doc - IFINANCIERO (<?=$nombreX?>)"><i class="material-icons">get_app</i></a>  
                        <a href="#" title="Quitar" class="btn btn-danger btn-sm" onClick="quitarArchivoSistemaAdjunto(<?=$filaA?>,<?=$codigoArchivoX;?>,1)"><i class="material-icons">delete_outline</i></a>
                      </div>     
                    </td>    
                    <td><?=$nombreX;?></td>
                  </tr> 
                  <?php
                   }
                      ?>     
                </tbody>
              </table>
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntos" name="cantidad_archivosadjuntos">
              <input type="hidden" value="<?=$filaA?>" id="cantidad_archivosadjuntosexistentes" name="cantidad_archivosadjuntosexistentes">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" onclick="" class="btn btn-success" data-dismiss="modal">Aceptar
          <div class="ripple-container"></div>
        </button>
      </div>
    </div>
  </div>
</div>


</form>
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

			$sqlCuentasAux="SELECT codigo, nombre, (select count(*) from estados_cuenta e, comprobantes c, comprobantes_detalle cd where c.codigo=cd.cod_comprobante and cd.codigo=e.cod_comprobantedetalle and c.cod_estadocomprobante<>2 and e.cod_cuentaaux=ca.codigo and e.cod_comprobantedetalleorigen=0)as contador FROM cuentas_auxiliares ca where ca.cod_cuenta='$codigoCuenta' order by 2";
			$stmtAux = $dbh->prepare($sqlCuentasAux);
			$stmtAux->execute();
			$stmtAux->bindColumn('codigo', $codigoCuentaAux);
			$stmtAux->bindColumn('nombre', $nombreCuentaAux);
			$stmtAux->bindColumn('contador', $contadorRegistrosEC);
			while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {
				$txtNumRegistros="";
				if($contadorRegistrosEC>0){
					$txtNumRegistros=" -- **".$contadorRegistrosEC."**";
				}
				$nombreCuentaAux=$nombreCuentaAux." ".$txtNumRegistros;
			  	
		  	?>
		  		<script>itemCuentasAux.push({codigo:"<?=$codigoCuentaAux?>",nombre:"<?=$nombreCuentaAux?>",codCuenta:"<?=$codigoCuenta?>"});
		  		</script>
	  		<?php
				$contAux++;
			}  	
		 ?> 	
		<script>
		    itemCuentas.push({codigo:"<?=$codigoCuenta?>",numero:"<?=$numeroCuenta?>",nombre:"<?=$nombreCuenta?>",cod_aux:"0",nom_aux:""});
		 </script><?php	
		$cont++;
		}
require_once 'modal.php';?>
<?php require_once '../simulaciones_servicios/modal_facturacion.php';?>
 <script>
 ponerConfirmacionDeArchivosSolRec();
 $("#totaldeb_total").val(<?=$totaldebDet?>);
 $("#totalhab_total").val(<?=$totalhabDet?>);

 $("#totaldeb_restante").val(<?=$totalesDebeHaber[0]-$totaldebDet?>);
 $("#totalhab_restante").val(<?=$totalesDebeHaber[1]-$totalhabDet?>);
 </script>
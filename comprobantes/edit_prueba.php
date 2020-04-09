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
	 $arrayFacturasGenerales[$un]['exento']=$row['exento'];
	 $arrayFacturasGenerales[$un]['nro_autorizacion']=$row['nro_autorizacion'];
	 $arrayFacturasGenerales[$un]['codigo_control']=$row['codigo_control'];
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
	var configuracionCentro=[];
	var configuraciones=[];
	var estado_cuentas=[];
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

<form id="formRegComp" class="form-horizontal" action="saveEdit.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="edicion" id="edicion" value="1">
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
		

			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="codigo_comprobante" id="codigo_comprobante" value="<?=$globalCode;?>">

			<div class="card">
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
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Fecha</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Tipo Comprobante</label>
	    <label class="col-sm-2 col-form-label" style="text-align: center;">Nro. Comprobante</label>
	    <label class="col-sm-1 col-form-label" style="text-align: center;">-</label>
 	</div>
					<div class="row">
						<div class="col-sm-1">							
						</div>
				 	<?php 
                  while ($row = $data->fetch(PDO::FETCH_BOUND)) {
                  	$fechaComp=explode("-",$fechaComprobante);
                  	$fechaComp2=explode(" ",$fechaComp[2]);
                  	$fechaComprobanteModal=$fechaComp2[0]."/".$fechaComp[1]."/".$fechaComp[0];
				 	?>
						<div class="col-sm-1">
							<div class="form-group">
						  		<!--<label class="bmd-label-static">Gestion</label>-->
					  			<input class="form-control" type="text" name="gestion" value="<?=$gestion;?>" id="gestion" readonly="true" />
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--<label class="bmd-label-static">Unidad</label>-->
						  		<input class="form-control" type="text" name="unidad_organizacional" value="<?=$unidad;?>" id="unidad_organizacional" readonly="true" />
							</div>
						</div>

						<div class="col-sm-2">
							<div class="form-group">
						  		<!--<label class="bmd-label-static">Fecha</label>-->
						  		<input class="form-control datepicker" type="text" name="fecha" value="<?=$fechaComprobanteModal;?>" id="fecha"/>
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
							  	$stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM tipos_comprobante where cod_estadoreferencial=1 order by 1");
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
						  		<div id="divnro_correlativo"><input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" readonly="true" value="<?=$nroCorrelativo?>" /></div>
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
		                        <a  title="Guardar como Plantilla (shift+s)" href="#" onclick="modalPlantilla()"class="btn btn-danger btn-fab btn-sm">
                      		        <i class="material-icons">favorite</i>
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
						<div class="col-sm-8">
						    <div class="form-group">
				          		<label for="glosa" class="bmd-label-static">Glosa</label>
								<input class="form-control" name="glosa" id="glosa" required="true" rows="1" value="<?=$glosaMadre?>"/>
							</div>
						</div>
					</h4>
				</div>
				<div class="card-body ">

					<?php
					//buscar detalles del comprobante
					?>
					<fieldset id="fiel" style="width:100%;border:0;">
						<div class="row">
							<div class="col-sm-1">
	                    		<button title="Agregar (alt+a)" type="button" id="add_boton" name="add" class="btn btn-warning btn-fab btn-round btn-sm" onClick="addCuentaContable(this)">
	                  		  <i class="material-icons x-s">add</i>
		                    </button>	
	                    	</div>

		                <label class="col-sm-1 col-form-label" style="text-align: center;">Centro Costos</label>
		                    <label class="col-sm-4 col-form-label" style="text-align: center;">Cuenta</label>
		                    <label class="col-sm-1 col-form-label" style="text-align: center;">Debe</label>
		                    <label class="col-sm-1 col-form-label" style="text-align: center;">Haber</label>
		                    <label class="col-sm-3 col-form-label" style="text-align: center;">Glosa</label>
			                <div class="col-sm-1" align="right">
								<a title="Copiar Unidad - Area (shift+u)" href="#modalCopySel" data-toggle="modal" data-target="#modalCopySel" class="<?=$buttonDelete?> btn-fab btn-sm">
	                      		  <i class="material-icons"><?=$iconCopy?></i>
			                    </a>
			                </div> 
			            </div>     
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
							?>
							<?php
							$codDet=$row['cod_det'];
							$unidadDet=$row['cod_unidadorganizacional'];
							$areaDet=$row['cod_area'];
							$debeDet=$row['debe'];
							$haberDet=$row['haber'];
							$glosaDet=$row['glosa'];
							$numeroDet=$row['numero'];
							$nombreDet=$row['nombre'];
							$cuentaAuxDet=$row['cuenta_auxiliar'];
							$codCuentaAuxDet=$row['cod_cuentaauxiliar'];
							$totaldebDet+=$row['debe'];$totalhabDet+=$row['haber'];
							$codigoCuenta=$row['codigo'];


						 ?>
                         <div id="div<?=$idFila?>">               	         
                             <div class="col-md-12">
                             	<div class="row">                     
		                          <div class="col-sm-1">
                                  	<div class="form-group">
	                                  <select class="selectpicker form-control form-control-sm" name="unidad<?=$idFila;?>" id="unidad<?=$idFila;?>" data-style="<?=$comboColor;?>" >  
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
    			                        	 <a title="Mayores" href="#" id="mayor<?=$idFila?>" onclick="mayorReporteComprobante(<?=$idFila?>)" class="btn btn-sm btn-info btn-fab"><span class="material-icons">list</span></a>	  	
    			                        	 <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>	  
    			                        	 <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab"><span class="material-icons">scatter_plot</span></a>	  
    			                             <input type="hidden" id="tipo_estadocuentas<?=$idFila?>">
    			                             <input type="hidden" id="tipo_proveedorcliente<?=$idFila?>">
    			                             <input type="hidden" id="proveedorcliente<?=$idFila?>">

    			                             <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>	  
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
                                       ?><script>setBusquedaCuenta('<?=$codigoCuenta;?>','<?=$numeroCuenta;?>','<?=$nombreCuenta;?>','0','');</script><?php		
			  	                     }else{
			  	                       ?><script>setBusquedaCuenta('<?=$codigoCuenta?>','<?=$numeroCuenta?>','<?=$nombreCuenta?>','<?=$codigoCuentaAux?>','<?=$nombreCuentaAux?>');</script><?php		
			  	                     }
                                  ?>
		                        <div class="col-sm-1">
                                    <div class="form-group">
                                    	<!--<label class="bmd-label-static">Debe</label>-->			
                                  		<input class="form-control small" type="number" placeholder="0" value="<?=$debeDet?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">	
		                        	</div>
      	                        </div>

		                        <div class="col-sm-1">
                                    <div class="form-group">
                                    	<!--<label class="bmd-label-static">Haber</label>-->			
                                  		<input class="form-control small" type="number" placeholder="0" value="<?=$haberDet?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 	
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
		                         	<a title="Facturas" href="#" id="boton_fac<?=$idFila;?>" onclick="listFac(<?=$idFila;?>);" class="btn btn-info btn-sm btn-fab">
                                      <i class="material-icons">featured_play_list</i><span id="nfac<?=$idFila;?>" class="count bg-warning">0</span>
                                    </a>
                                   <a title="Eliminar (alt + q)" rel="tooltip" href="#" class="btn btn-danger btn-sm btn-fab" id="boton_remove<?=$idFila;?>" onclick="minusCuentaContable('<?=$idFila;?>');">
                                           <i class="material-icons">remove_circle</i>
                                    </a>
	                             </div>  
		                       </div>

	                          </div>
                            </div>
                           <div class="h-divider"></div>
                         </div>

                       <script>var nfac=[];
      itemFacturas.push(nfac);var nest=[];
      itemEstadosCuentas.push(nest);</script>
						 <?php

						      for ($i=0; $i < count($arrayFacturasGenerales) ; $i++) {
						      	    $codX=$arrayFacturasGenerales[$i]['cod_comprobantedetalle'];
			  	                    $nit=$arrayFacturasGenerales[$i]['nit'];
				                    $factura=$arrayFacturasGenerales[$i]['nro_factura'];
				                    $fechaFac=$arrayFacturasGenerales[$i]['fecha'];
				                    $razon=$arrayFacturasGenerales[$i]['razon_social'];
				                    $importe=$arrayFacturasGenerales[$i]['importe'];
				                    $exento=$arrayFacturasGenerales[$i]['exento'];
				                    $autorizacion=$arrayFacturasGenerales[$i]['nro_autorizacion'];
				                    $control=$arrayFacturasGenerales[$i]['codigo_control'];
			  	                   if($codX==$codDet){
			  	                     ?><script>abrirFactura(<?=$idFila?>,'<?=$nit?>',<?=$factura?>,'<?=$fechaFac?>','<?=$razon?>',<?=$importe?>,<?=$exento?>,'<?=$autorizacion?>','<?=$control?>');</script><?php
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
						          		<input class="form-control" type="number" name="totaldeb" value="<?=$totalesDebeHaber[0]?>" placeholder="0" id="totaldeb" readonly="true">
									</div>
						      	</div>
								<div class="col-sm-1">
						            <div class="form-group">
						            	<input class="form-control" type="hidden" name="totalhab_restante" placeholder="0" id="totalhab_restante" readonly="true">
						            	<input class="form-control" type="hidden" name="totalhab_total" placeholder="0" id="totalhab_total" readonly="true">
						            	<input class="form-control" type="number" name="totalhab" value="<?=$totalesDebeHaber[1]?>" placeholder="0" id="totalhab" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-4">
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
				  	</div>

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
			for ($i=0; $i < count($arrayCuentasAux) ; $i++) {
			  	 $codigoCuentaAux=$arrayCuentasAux[$i]['codigo'];
			  	 $codX=$arrayCuentasAux[$i]['cod_cuenta'];
			  	 $nombreCuentaAux=$arrayCuentasAux[$i]['nombre'];
			  	 if($codX==$codigoCuenta){
			  	  ?><script>itemCuentasAux.push({codigo:"<?=$codigoCuentaAux?>",nombre:"<?=$nombreCuentaAux?>",codCuenta:"<?=$codigoCuenta?>"});</script><?php
				$contAux++;
			  	 }
			 } 	
		 ?><script>
		    itemCuentas.push({codigo:"<?=$codigoCuenta?>",numero:"<?=$numeroCuenta?>",nombre:"<?=$nombreCuenta?>",cod_aux:"0",nom_aux:""});
		 </script><?php	
		$cont++;
		}
require_once 'modal.php';?>
 <script>
 $("#totaldeb_total").val(<?=$totaldebDet?>);
 $("#totalhab_total").val(<?=$totalhabDet?>);

 $("#totaldeb_restante").val(<?=$totalesDebeHaber[0]-$totaldebDet?>);
 $("#totalhab_restante").val(<?=$totalesDebeHaber[1]-$totalhabDet?>);
 </script>
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
?>
<script>
	numFilas=<?=$contadorRegistros;?>;
	cantidadItems=<?=$contadorRegistros;?>;
	var distribucionPor=[];
	var configuracionCentro=[];
	var configuraciones=[];
	var estado_cuentas=[];
</script>
<form id="formRegComp" class="form-horizontal" action="saveEdit.php" method="post" enctype="multipart/form-data">
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

						<div class="col-sm-1">
							<div class="form-group">
						  		<label for="nro_correlativo" class="bmd-label-static">#</label>
						  		<div id="divnro_correlativo"><input class="form-control" type="number" name="nro_correlativo" id="nro_correlativo" min="1" required="true" readonly="true" value="<?=$nroCorrelativo?>" /></div>
							</div>
						</div>
						
					    <div class="col-sm-4">
						    <div class="form-group">
				          		<label for="glosa" class="bmd-label-static">Glosa</label>
								<textarea class="form-control" name="glosa" id="glosa" required="true" rows="2" value=""><?=$glosaComprobante?></textarea>
							</div>
						</div>
						<div class="col-sm-1">
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
                   <?php } ?>
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
					//buscar detalles del comprobante
					?>
					<fieldset id="fiel" style="width:100%;border:0;">
							<button title="Agregar (alt+a)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addCuentaContable(this)" accesskey="a">
                      		  <i class="material-icons">add</i>
		                    </button>

		                <div class="col-sm-1 float-right">
							<a title="Copiar Unidad - Area (shift+u)" href="#modalCopySel" data-toggle="modal" data-target="#modalCopySel" class="<?=$buttonDelete?> btn-fab">
                      		  <i class="material-icons"><?=$iconCopy?></i>
		                    </a>
		                </div>  
		                <div id="div">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
			            <?php
						$detalle=obtenerComprobantesDet($globalCode);
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
			  	                         $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM unidades_organizacionales where cod_estado=1 and centro_costos=1 order by 2");
				                         $stmt->execute();
				                           while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                           	$codigoX=$row['codigo'];
				                           	$nombreX=$row['nombre'];
				                           	$abrevX=$row['abreviatura'];
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
			  	                        $stmt = $dbh->prepare("SELECT codigo, nombre, abreviatura FROM areas where cod_estado=1 and centro_costos=1 order by 2");
				                        $stmt->execute();
				                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                        	$codigoX=$row['codigo'];
				                        	$nombreX=$row['nombre'];
				                        	$abrevX=$row['abreviatura'];
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
    	                        	<input type="hidden" name="cuenta_auxiliar<?=$idFila;?>" id="cuenta_auxiliar<?=$idFila;?>" value="">
                                	<div class="row">	
    			                        <div class="col-sm-8">
    			                        	<div class="form-group" id="divCuentaDetalle<?=$idFila;?>">
    			                        
        	                                </div>
    			                        </div>
    			                        <div class="col-sm-4">
    			                        	<div class="btn-group">
    			                        	 <!--<a title="Mayores" href="#" id="mayor<?=$idFila?>" onclick="mayorReporteComprobante(<?=$idFila?>)" class="btn btn-sm btn-info btn-fab"><span class="material-icons">list</span></a>	  	
    			                        	 <a title="Cambiar cuenta" href="#" id="cambiar_cuenta<?=$idFila?>" onclick="editarCuentaComprobante(<?=$idFila?>)" class="btn btn-sm btn-warning btn-fab"><span class="material-icons text-dark">edit</span></a>	  
    			                        	 <a title="Distribucion" href="#modalDist" data-toggle="modal" data-target="#modalDist" id="distribucion<?=$idFila?>" onclick="nuevaDistribucionPonerFila(<?=$idFila;?>);" class="btn btn-sm btn-default btn-fab"><span class="material-icons">scatter_plot</span></a>-->	  
    			                             <input type="hidden" id="tipo_estadocuentas<?=$idFila?>">
    			                             <a title="Estado de Cuentas" id="estados_cuentas<?=$idFila?>" href="#" onclick="verEstadosCuentas(<?=$idFila;?>,0);" class="btn btn-sm btn-danger btn-fab d-none"><span class="material-icons text-dark">ballot</span><span id="nestado<?=$idFila?>" class="bg-warning"></span></a>	  
    			                            </div>  
    			                        </div>
    		                        </div>
      	                        </div>
                                <?php
		                          	$numeroCuenta=trim($numeroDet);
		                          	$nombreCuenta=trim($nombreDet);
			                        $sqlCuentasAux="SELECT codigo, nombre FROM cuentas_auxiliares where cod_cuenta='$codigoCuenta' order by 2";
			                        $stmtAux = $dbh->prepare($sqlCuentasAux);
			                        $stmtAux->execute();
			                        $stmtAux->bindColumn('codigo', $codigoCuentaAux);
			                        $stmtAux->bindColumn('nombre', $nombreCuentaAux);
                                    ?><script>filaActiva=<?=$idFila?>;</script><?php
			                       // $txtAuxiliarCuentas="<table class='table table-condensed'>";
			                         while ($rowAux = $stmtAux->fetch(PDO::FETCH_BOUND)) {
			                         	?><script>setBusquedaCuenta('<?=$codigoCuenta?>','<?=$numeroCuenta?>','<?=$nombreCuenta?>','<?=$codigoCuentaAux?>','<?=$nombreCuentaAux?>');</script><?php
			                         }  	
		                            ?>
		                              <script>setBusquedaCuenta('<?=$codigoCuenta;?>','<?=$numeroCuenta;?>','<?=$nombreCuenta;?>','0','');</script>
	                                 

		                        <div class="col-sm-1">
                                    <div class="form-group">
                                    	<label class="bmd-label-static">Debe</label>			
                                  		<input class="form-control small" type="number" placeholder="0" value="<?=$debeDet?>" name="debe<?=$idFila;?>" id="debe<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01">	
		                        	</div>
      	                        </div>

		                        <div class="col-sm-1">
                                    <div class="form-group">
                                    	<label class="bmd-label-static">Haber</label>			
                                  		<input class="form-control small" type="number" placeholder="0" value="<?=$haberDet?>" name="haber<?=$idFila;?>" id="haber<?=$idFila;?>" onChange="calcularTotalesComprobante(this.id,event);" OnKeyUp="calcularTotalesComprobante(this.id,event);" step="0.01"> 	
		                        	</div>
      	                        </div>

      	                        <div class="col-sm-3">
		                            <div class="form-group">
                                  		<label class="bmd-label-static">GlosaDetalle</label>
		                        		<textarea rows="1" class="form-control" name="glosa_detalle<?=$idFila;?>" id="glosa_detalle<?=$idFila;?>"><?=$glosaDet?></textarea>
		                        	</div>
		                        </div>

		                       <div class="col-sm-1">
		                         <div class="btn-group">
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

                       <script>var nfac=[];itemFacturas.push(nfac);var nest=[];itemEstadosCuentas.push(nest);</script>
						 <?php
						      $stmt = $dbh->prepare("SELECT * FROM facturas_compra where cod_comprobantedetalle=$codDet");
				              $stmt->execute();
				              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                    $nit=$row['nit'];
				                    $factura=$row['nro_factura'];
				                    $fechaFac=$row['fecha'];
				                    $razon=$row['razon_social'];
				                    $importe=$row['importe'];
				                    $exento=$row['exento'];
				                    $autorizacion=$row['nro_autorizacion'];
				                    $control=$row['codigo_control'];
				                    ?><script>abrirFactura(<?=$idFila?>,'<?=$nit?>',<?=$factura?>,'<?=$fechaFac?>','<?=$razon?>',<?=$importe?>,<?=$exento?>,'<?=$autorizacion?>','<?=$control?>');</script><?php
			  	              }
						      
						      // estados de cuenta
						      $stmt = $dbh->prepare("SELECT * FROM estados_cuenta where cod_comprobantedetalle=$codDet");
				              $stmt->execute();
				              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
				                    $cuenta=$row['cod_plancuenta'];
				                    $codComproDet=$row['cod_comprobantedetalleorigen'];
				                    $monto=$row['monto'];
				                    ?><script>abrirEstado(<?=$idFila?>,'<?=$cuenta?>',<?=$codComproDet?>,'<?=$monto?>');</script><?php
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
						          		<input class="form-control" type="number" name="totaldeb" placeholder="0" id="totaldeb" readonly="true">	
									</div>
						      	</div>
								<div class="col-sm-1">
						            <div class="form-group">
						            	<input class="form-control" type="number" name="totalhab" placeholder="0" id="totalhab" readonly="true">	
									</div>
						      	</div>
						      	<div class="col-sm-4">
								</div>
							</div>
				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Cancelar</a>

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
		}
require_once 'modal.php';?>
 <script>$("#totaldeb").val(<?=$totaldebDet?>);$("#totalhab").val(<?=$totalhabDet?>);</script>
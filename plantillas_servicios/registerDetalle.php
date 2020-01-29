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
</script>

<?php
$fechaActual=date("Y-m-d");
$dbh = new Conexion();
if(isset($_GET['cod'])){
	$codigo=$_GET['cod'];
}else{
	$codigo=0;
}
			$stmt = $dbh->prepare("SELECT p.*,e.nombre as estado_plantilla, u.abreviatura as unidad,a.abreviatura as area from plantillas_servicios p,unidades_organizacionales u, areas a,estados_plantillascosto e
  where p.cod_unidadorganizacional=u.codigo and p.cod_area=a.codigo and e.codigo=p.cod_estadoplantilla and p.codigo='$codigo' order by codigo");
			$stmt->execute();
			$stmt->bindColumn('codigo', $codigoX);
            $stmt->bindColumn('nombre', $nombreX);
            $stmt->bindColumn('abreviatura', $abreviaturaX);
            $stmt->bindColumn('cod_unidadorganizacional', $codUnidadX);
            $stmt->bindColumn('cod_area', $codAreaX);
            $stmt->bindColumn('area', $areaX);
            $stmt->bindColumn('unidad', $unidadX);
            $stmt->bindColumn('estado_plantilla', $estadoX);
            $stmt->bindColumn('dias_auditoria', $diasAuditoriaX);
            $stmt->bindColumn('fecha_auditoria', $fechaAuditoria);
?>
<div class="cargar-ajax d-none">
  <div class="div-loading text-center">
     <h4 class="text-warning font-weight-bold">Procesando Datos</h4>
     <p class="text-white">Aguard&aacute; un momento por favor</p>  
  </div>
</div>
<form id="formDetTcp" class="form-horizontal" action="saveEdit.php" method="post">
<div class="content">
	<div id="contListaGrupos" class="container-fluid">
			<input type="hidden" name="cantidad_filas" id="cantidad_filas" value="<?=$contadorRegistros;?>">
			<input type="hidden" name="cod_plantilla" id="cod_plantilla" value="<?=$codigo?>">
			<div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h4 class="card-title">Registrar <?=$moduleNameSingular;?></h4>
					</div>
				</div>
				<div class="card-body ">
                     <div class="row">
					<?php while ($row = $stmt->fetch(PDO::FETCH_BOUND)) {?>
					<input class="form-control" type="hidden" name="cod_unidad" value="<?=$codUnidadX?>" id="cod_unidad" readonly/>
					<input class="form-control" type="hidden" name="cod_area" value="<?=$codAreaX?>" id="cod_area" readonly/>
						<div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Nombre</label>
					  			<input class="form-control" type="text" name="nombre" value="<?=$nombreX?>" id="nombre"/>
							</div>
						</div>

						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Abreviatura</label>
						  		<input class="form-control" type="text" name="abreviatura" value="<?=$abreviaturaX?>" id="abreviatura"/>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">D&iacute;as Auditor&iacute;a</label>
						  		<input class="form-control" type="number" min="1" name="dias_auditoria" value="<?=$diasAuditoriaX?>" id="dias_auditoria"/>
							</div>
						</div>
						<div class="col-sm-2">
							<div class="form-group has-success">
						  		<label class="bmd-label-static">Fecha Auditor&iacute;a</label>
						  		<input class="form-control" type="text" readonly name="fecha_auditoria" value="<?=$fechaAuditoria?>" id="fecha_auditoria"/>
							</div>
						</div>
                        <div class="col-sm-2">
							<div class="form-group">
						  		<label class="bmd-label-static">Estado</label>
						  		<input class="form-control" type="text" readonly name="estado_plan" value="<?=$estadoX?>" id="estado_plan"/>
							</div>
						</div>
						<div class="col-sm-1">
							<div class="form-group">
						  		<label class="bmd-label-static">Unidad</label>
						  		<input class="form-control" type="text" name="unidad" value="<?=$unidadX?>" id="unidad" readonly/>
							</div>
						</div>

						<div class="col-sm-1">
				        	<div class="form-group">
						  		<label class="bmd-label-static">Area</label>
						  		<input class="form-control" type="text" name="area" value="<?=$areaX?>" id="area" readonly/>
							</div>
				      	</div>
				      	<div class="col-sm-1 float-right">
							<div class="">
						  		<a href="#" title="Ayuda" class="btn btn-default btn-fab btn-round" onclick="ayudaPlantilla()"><span class="material-icons">help_outline</span></a>
							</div>
						</div>
				      </div>

				      	<?php } ?>
				</div>
			</div>
           <div class="row">
             <div class="col-sm-12">
			  <div class="card">
				<div class="card-header <?=$colorCard;?> card-header-text">
					<div class="card-text">
					  <h6 class="card-title">Detalles</h6>
					</div>
				</div>
				<div class="card-body">
					<fieldset id="fiel" style="width:100%;border:0;">
							<button title="Agregar (shift+n)" type="button" name="add" class="btn btn-warning btn-round btn-fab" onClick="addDetallePlantilla(this)">
                      		  <i class="material-icons">add</i>
		                    </button>  
						<div id="div">	
							
							<div class="h-divider">
	        				</div>
		 					
	 					</div>
	 					<?php
                       $stmt = $dbh->prepare("SELECT c.codigo as codCuenta,p.cod_partidapresupuestaria,p.codigo, p.cod_tipo, p.glosa,p.cantidad,p.unidad,p.monto_unitario,p.monto_total,p.cod_plantillatcp,c.nombre from plantillas_servicios_detalle p,plan_cuentas c where p.cod_plantillatcp=$codigo and p.cod_cuenta=c.codigo order by p.codigo");
                         $stmt->execute();
                         $idFila=1;
                         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                          $codigoCostoX=$row['codigo'];
                          $codTipoCostoX=$row['cod_tipo'];
                          $nombreCostoX=$row['glosa'];
                          $cantidadCostoX=$row['cantidad'];
                          $unidadCostoX=$row['unidad'];
                          $montoTotalCostoX=$row['monto_total'];
                          $montoUnitarioCostoX=$row['monto_unitario'];
                          $codPlantillaCostoX=$row['cod_plantillatcp'];
                          $nombreCuentaX=trim($row['nombre']);
                          $codCuentaX=$row['codCuenta'];
                          $codPartidaX=$row['cod_partidapresupuestaria'];
                          ?>
                          <script>numFilas++;cantidadItems++;</script>
                          <script>var ndet=[];itemDetalle.push(ndet);</script>
						 <?php
						      $stmt2 = $dbh->prepare("SELECT * FROM plantillas_grupocostodetalle where cod_plantillagrupocosto=$codigoCostoX");
				              $stmt2->execute();
				              $idFilas=0;
				           
						?>
                          <div id="div<?=$idFila?>" class="col-md-12">
	<div class="row">

		<div class="col-sm-2">
        	<div class="form-group">
	        <select class="selectpicker form-control form-control-sm" name="tipo_costo<?=$idFila;?>" id="tipo_costo<?=$idFila;?>" data-style="<?=$comboColor;?>" onchange="mostrarUnidadDetalle(<?=$idFila;?>)" required>
	        	                <option disabled value="">Tipo</option>
			  	              <?php        
                                if($codTipoCostoX==1){
                                ?><option value="1" selected>Fijo</option>
                                  <option value="2">Variable</option>
                                <?php
                                }else{
                                 ?><option value="1">Fijo</option>
                                  <option value="2" selected>Variable</option>
                                <?php
                                }
			  	              ?>
					
			</select>
			</div>
      	</div>
		<div class="col-sm-4">
            <div class="form-group">
            	<label for="detalle_plantilla<?=$idFila;?>" class="bmd-label-floating">Detalle</label>			
          		<input class="form-control" type="text" value="<?=$nombreCostoX?>" name="detalle_plantilla<?=$idFila;?>" id="detalle_plantilla<?=$idFila;?>" required>	
			</div>
      	</div>

		<div class="col-sm-1">
            <div class="form-group">
            	<label for="cantidad_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Cantidad</label>			
          		<input class="form-control" type="number" min="0" value="<?=$cantidadCostoX?>" name="cantidad_detalleplantilla<?=$idFila;?>" id="cantidad_detalleplantilla<?=$idFila;?>" onchange="calcularTotalFilaDetalle(<?=$idFila?>)" onkeypress"calcularTotalFilaDetalle(<?=$idFila?>)" id="cantidad_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-2">
            <div class="form-group">
            	<label for="unidad_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Unidad</label>			
          		<input class="form-control" type="text" value="<?=$unidadCostoX?>" <?=($codTipoCostoX==1)?"readonly":"";?> name="unidad_detalleplantilla<?=$idFila;?>" id="unidad_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="monto_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Precio Unit.</label>			
          		<input class="form-control" type="number" step="0.1" min="0" value="<?=$montoUnitarioCostoX?>" name="monto_detalleplantilla<?=$idFila;?>" onchange="calcularTotalFilaDetalle(<?=$idFila?>)" onkeypress"calcularTotalFilaDetalle(<?=$idFila?>)" id="monto_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>
      	<div class="col-sm-1">
            <div class="form-group">
            	<label for="monto_total_detalleplantilla<?=$idFila;?>" class="bmd-label-floating">Precio Total</label>			
          		<input class="form-control" type="number" readonly step="0.1" min="0" value="<?=$montoTotalCostoX?>" name="monto_total_detalleplantilla<?=$idFila;?>" id="monto_total_detalleplantilla<?=$idFila;?>" required> 	
			</div>
      	</div>

		<div class="col-sm-1">
		   <div class="btn-group">
		  	<a title="<?=$nombreCuentaX?>" href="#" id="boton_det<?=$idFila;?>" onclick="listDetallePlantilla(<?=$idFila;?>);" class="btn btn-just-icon btn-primary btn-link">
               <i class="material-icons">view_list</i><span id="ndet<?=$idFila;?>" class="bg-success estado2"></span>
             </a>
             <input type="hidden" value="<?=$codPartidaX?>" name="codigo_partidadetalle<?=$idFila;?>" id="codigo_partidadetalle<?=$idFila;?>">
             <input type="hidden" name="codigo_cuentadetalle<?=$idFila;?>" value="<?=$codCuentaX?>" id="codigo_cuentadetalle<?=$idFila;?>"> 	
			<a title="Eliminar Registro" href="#" class="btn btn-just-icon btn-danger btn-link" id="boton_remove<?=$idFila;?>" onclick="minusDetallePlantilla('<?=$idFila;?>');">
            	<i class="material-icons">remove_circle</i>
	        </a>
	      </div>  
		</div>

	</div>
	<div class="h-divider"></div>
</div>


                          <?php
                    while ($rowDet = $stmt2->fetch(PDO::FETCH_ASSOC)) {
				                    $codigo_partida=$rowDet['cod_partidapresupuestaria'];

				                    $nombrePartida=namePartidaPres($rowDet['cod_partidapresupuestaria']);         

				                    $tipo=$rowDet['tipo_calculo'];
				                    $monto_i=$rowDet['monto_local'];
				                    $monto_fi=$rowDet['monto_externo'];
				                    $monto_cal=$rowDet['monto_calculado'];
				                    $idFilas=$idFilas+1;
				                    ?><script>abrirDetalleCosto(<?=$idFila?>,'<?=$codigo_partida?>','<?=$nombrePartida?>','<?=$tipo?>',<?=$monto_i?>,<?=$monto_fi?>,<?=$monto_cal?>);</script><?php
			  	              }
			  	      ?><script>$("#cantidad_filas").val(<?=$idFila?>);</script><?php              
                          $idFila++;
                      }
	 					?>
		            </fieldset>

				  	<div class="card-footer fixed-bottom">
						<button type="submit" class="<?=$buttonMorado;?>">Guardar</button>
						<a href="../<?=$urlList;?>" class="<?=$buttonCancel;?>">Volver</a>

				  	</div>
				 </div>
			    </div>			
               </div>
            </div>
	</div>
</div>
</form>

<?php
require_once 'modal.php';
?>
